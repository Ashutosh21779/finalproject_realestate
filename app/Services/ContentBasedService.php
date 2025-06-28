<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\Wishlist;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\UserPropertyView;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ContentBasedService
{
    /**
     * Get content-based recommendations for a user
     */
    public function getRecommendations($user, $limit = 6)
    {
        $cacheKey = "content_based_recommendations_user_{$user->id}";
        
        return Cache::remember($cacheKey, 1800, function() use ($user, $limit) {
            // Get user's preference profile from wishlist
            $userProfile = $this->buildUserProfile($user);

            if (empty($userProfile)) {
                // Fallback to popular properties in Nepal
                return $this->getFallbackRecommendations($user, $limit);
            }

            // Find similar properties based on user profile
            $recommendations = $this->findSimilarProperties($user, $userProfile, $limit);

            // If no content-based recommendations found, use fallback
            if ($recommendations->isEmpty()) {
                return $this->getFallbackRecommendations($user, $limit);
            }

            return $recommendations;
        });
    }

    /**
     * Build user preference profile from wishlist and views
     */
    protected function buildUserProfile($user)
    {
        // Get user's wishlist properties
        $wishlistProperties = Wishlist::where('user_id', $user->id)
            ->with(['property.type', 'property.pstate'])
            ->get()
            ->pluck('property')
            ->filter();
        
        if ($wishlistProperties->isEmpty()) {
            return [];
        }
        
        $profile = [
            'property_types' => [],
            'states' => [],
            'price_range' => ['min' => null, 'max' => null],
            'bedrooms' => [],
            'bathrooms' => []
        ];
        
        foreach ($wishlistProperties as $property) {
            // Property types
            if ($property->ptype_id) {
                $profile['property_types'][] = $property->ptype_id;
            }
            
            // States/Locations
            if ($property->state) {
                $profile['states'][] = $property->state;
            }
            
            // Price range
            $price = (float) str_replace(',', '', $property->lowest_price ?? 0);
            if ($price > 0) {
                if (is_null($profile['price_range']['min']) || $price < $profile['price_range']['min']) {
                    $profile['price_range']['min'] = $price;
                }
                if (is_null($profile['price_range']['max']) || $price > $profile['price_range']['max']) {
                    $profile['price_range']['max'] = $price;
                }
            }
            
            // Bedrooms
            if ($property->bedrooms) {
                $profile['bedrooms'][] = (int) $property->bedrooms;
            }
            
            // Bathrooms
            if ($property->bathrooms) {
                $profile['bathrooms'][] = (int) $property->bathrooms;
            }
        }
        
        // Calculate preferences (most common values)
        $profile['property_types'] = array_count_values($profile['property_types']);
        $profile['states'] = array_count_values($profile['states']);
        $profile['bedrooms'] = array_count_values($profile['bedrooms']);
        $profile['bathrooms'] = array_count_values($profile['bathrooms']);
        
        // Expand price range by 20% for flexibility
        if (!is_null($profile['price_range']['min']) && !is_null($profile['price_range']['max'])) {
            $range = $profile['price_range']['max'] - $profile['price_range']['min'];
            $expansion = $range * 0.2;
            $profile['price_range']['min'] = max(0, $profile['price_range']['min'] - $expansion);
            $profile['price_range']['max'] = $profile['price_range']['max'] + $expansion;
        }
        
        return $profile;
    }

    /**
     * Find properties similar to user preferences
     */
    protected function findSimilarProperties($user, $userProfile, $limit)
    {
        // Get user's wishlist IDs to exclude
        $excludeIds = Wishlist::where('user_id', $user->id)
            ->pluck('property_id')
            ->toArray();
        
        $query = Property::where('status', '1')
            ->whereNotIn('id', $excludeIds);
        
        // Apply content-based filters
        $this->applyContentFilters($query, $userProfile);
        
        // Get properties and calculate similarity scores
        $properties = $query->with(['type', 'pstate'])
            ->orderBy('id', 'DESC')
            ->limit($limit * 3) // Get more to calculate similarity
            ->get();
        
        // Calculate similarity scores and sort
        $scoredProperties = $properties->map(function($property) use ($userProfile) {
            $score = $this->calculateSimilarityScore($property, $userProfile);
            $property->similarity_score = $score;
            return $property;
        })->sortByDesc('similarity_score')->take($limit);
        
        return $scoredProperties->values();
    }

    /**
     * Apply content-based filters to query
     */
    protected function applyContentFilters($query, $userProfile)
    {
        // Filter by preferred property types
        if (!empty($userProfile['property_types'])) {
            $preferredTypes = array_keys($userProfile['property_types']);
            $query->whereIn('ptype_id', $preferredTypes);
        }
        
        // Filter by preferred states
        if (!empty($userProfile['states'])) {
            $preferredStates = array_keys($userProfile['states']);
            $query->where(function($q) use ($preferredStates) {
                $q->whereIn('state', $preferredStates);
                // Also check for state IDs if states are stored as names
                foreach ($preferredStates as $state) {
                    if (is_numeric($state)) {
                        $q->orWhere('state', $state);
                    } else {
                        // Find state ID by name
                        $stateObj = State::where('state_name', $state)->first();
                        if ($stateObj) {
                            $q->orWhere('state', $stateObj->id);
                        }
                    }
                }
            });
        }
        
        // Filter by price range
        if (!is_null($userProfile['price_range']['min']) && !is_null($userProfile['price_range']['max'])) {
            $query->where(function($q) use ($userProfile) {
                $q->whereBetween(DB::raw('CAST(REPLACE(lowest_price, ",", "") AS UNSIGNED)'), [
                    $userProfile['price_range']['min'],
                    $userProfile['price_range']['max']
                ]);
            });
        }
    }

    /**
     * Calculate similarity score between property and user profile
     */
    protected function calculateSimilarityScore($property, $userProfile)
    {
        $score = 0;
        
        // Property type similarity (30% weight)
        if (isset($userProfile['property_types'][$property->ptype_id])) {
            $score += 0.3 * ($userProfile['property_types'][$property->ptype_id] / max(array_values($userProfile['property_types'])));
        }
        
        // State similarity (25% weight)
        if (isset($userProfile['states'][$property->state])) {
            $score += 0.25 * ($userProfile['states'][$property->state] / max(array_values($userProfile['states'])));
        }
        
        // Bedroom similarity (20% weight)
        if (!empty($userProfile['bedrooms']) && $property->bedrooms) {
            $preferredBedrooms = array_keys($userProfile['bedrooms'], max($userProfile['bedrooms']))[0];
            $bedroomDiff = abs((int)$property->bedrooms - $preferredBedrooms);
            $score += 0.2 * max(0, 1 - ($bedroomDiff / 3)); // Penalty decreases with difference
        }
        
        // Price similarity (15% weight)
        if (!is_null($userProfile['price_range']['min']) && !is_null($userProfile['price_range']['max'])) {
            $propertyPrice = (float) str_replace(',', '', $property->lowest_price ?? 0);
            if ($propertyPrice > 0) {
                $priceRange = $userProfile['price_range']['max'] - $userProfile['price_range']['min'];
                if ($priceRange > 0) {
                    $priceDiff = abs($propertyPrice - (($userProfile['price_range']['min'] + $userProfile['price_range']['max']) / 2));
                    $score += 0.15 * max(0, 1 - ($priceDiff / $priceRange));
                }
            }
        }
        
        // Bathroom similarity (10% weight)
        if (!empty($userProfile['bathrooms']) && $property->bathrooms) {
            $preferredBathrooms = array_keys($userProfile['bathrooms'], max($userProfile['bathrooms']))[0];
            $bathroomDiff = abs((int)$property->bathrooms - $preferredBathrooms);
            $score += 0.1 * max(0, 1 - ($bathroomDiff / 2));
        }
        
        return $score;
    }

    /**
     * Get fallback recommendations when user has no preferences
     */
    protected function getFallbackRecommendations($user, $limit)
    {
        // Get user's wishlist IDs to exclude
        $excludeIds = Wishlist::where('user_id', $user->id)
            ->pluck('property_id')
            ->toArray();

        // Return popular properties from major Nepalese cities
        $query = Property::where('status', '1')
            ->where(function($query) {
                $query->where('city', 'LIKE', '%Kathmandu%')
                      ->orWhere('city', 'LIKE', '%Pokhara%')
                      ->orWhere('city', 'LIKE', '%Lalitpur%')
                      ->orWhere('city', 'LIKE', '%Bhaktapur%');
            });

        // Exclude wishlist properties
        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        return $query->orderBy('id', 'DESC')
            ->limit($limit)
            ->get();
    }
}
