<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\Wishlist;
use App\Models\UserPropertyView;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CollaborativeFilteringService
{
    /**
     * Get collaborative filtering recommendations for a user
     */
    public function getRecommendations($user, $limit = 6)
    {
        $cacheKey = "collaborative_recommendations_user_{$user->id}";
        
        return Cache::remember($cacheKey, 1800, function() use ($user, $limit) {
            // Check if user has minimum required wishlist items
            $userWishlistCount = Wishlist::where('user_id', $user->id)->count();
            
            if ($userWishlistCount < 3) {
                // Not enough data for collaborative filtering
                return collect();
            }
            
            // Find similar users using Jaccard similarity
            $similarUsers = $this->findSimilarUsers($user);
            
            if ($similarUsers->isEmpty()) {
                return collect();
            }
            
            // Get recommendations based on similar users' preferences
            return $this->getRecommendationsFromSimilarUsers($user, $similarUsers, $limit);
        });
    }

    /**
     * Find users with similar preferences using Jaccard similarity coefficient
     */
    protected function findSimilarUsers($user, $minSimilarity = 0.1)
    {
        // Get current user's wishlist property IDs
        $userWishlist = Wishlist::where('user_id', $user->id)
            ->pluck('property_id')
            ->toArray();
        
        if (empty($userWishlist)) {
            return collect();
        }
        
        // Get all other users who have wishlist items
        $otherUsers = User::whereHas('wishlists')
            ->where('id', '!=', $user->id)
            ->where('status', 'active')
            ->get();
        
        $similarities = collect();
        
        foreach ($otherUsers as $otherUser) {
            $otherWishlist = Wishlist::where('user_id', $otherUser->id)
                ->pluck('property_id')
                ->toArray();
            
            // Calculate Jaccard similarity
            $similarity = $this->calculateJaccardSimilarity($userWishlist, $otherWishlist);
            
            if ($similarity >= $minSimilarity) {
                $similarities->push([
                    'user' => $otherUser,
                    'similarity' => $similarity
                ]);
            }
        }
        
        // Sort by similarity (descending) and return top similar users
        return $similarities->sortByDesc('similarity')->take(10);
    }

    /**
     * Calculate Jaccard similarity coefficient between two sets
     */
    protected function calculateJaccardSimilarity($set1, $set2)
    {
        $intersection = array_intersect($set1, $set2);
        $union = array_unique(array_merge($set1, $set2));
        
        if (empty($union)) {
            return 0;
        }
        
        return count($intersection) / count($union);
    }

    /**
     * Get property recommendations from similar users
     */
    protected function getRecommendationsFromSimilarUsers($user, $similarUsers, $limit)
    {
        // Get current user's wishlist to exclude
        $userWishlistIds = Wishlist::where('user_id', $user->id)
            ->pluck('property_id')
            ->toArray();
        
        $recommendations = collect();
        $propertyScores = [];
        
        foreach ($similarUsers as $similarUserData) {
            $similarUser = $similarUserData['user'];
            $similarity = $similarUserData['similarity'];
            
            // Get properties liked by similar user but not by current user
            $similarUserProperties = Wishlist::where('user_id', $similarUser->id)
                ->whereNotIn('property_id', $userWishlistIds)
                ->with(['property' => function($query) {
                    $query->where('status', '1'); // Only active properties
                }])
                ->get()
                ->pluck('property')
                ->filter(); // Remove null properties
            
            foreach ($similarUserProperties as $property) {
                if (!isset($propertyScores[$property->id])) {
                    $propertyScores[$property->id] = [
                        'property' => $property,
                        'score' => 0
                    ];
                }
                
                // Weight the score by user similarity
                $propertyScores[$property->id]['score'] += $similarity;
            }
        }
        
        // Sort by score and return top recommendations
        $sortedProperties = collect($propertyScores)
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('property');
        
        return $sortedProperties;
    }

    /**
     * Add user relationship to User model if not exists
     */
    protected function ensureUserWishlistRelation()
    {
        // This method ensures the User model has the wishlists relationship
        // The relationship should be added to the User model:
        // public function wishlists() { return $this->hasMany(Wishlist::class); }
    }
}
