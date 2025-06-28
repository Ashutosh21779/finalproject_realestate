<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\Wishlist;
use App\Models\UserPropertyView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class RecommendationService
{
    protected $collaborativeService;
    protected $contentBasedService;
    
    public function __construct(
        CollaborativeFilteringService $collaborativeService,
        ContentBasedService $contentBasedService
    ) {
        $this->collaborativeService = $collaborativeService;
        $this->contentBasedService = $contentBasedService;
    }

    /**
     * Get hybrid recommendations combining collaborative and content-based filtering
     */
    public function getHybridRecommendations($user, $limit = 10)
    {
        $cacheKey = "hybrid_recommendations_user_{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function() use ($user, $limit) {
            try {
                // Get collaborative recommendations (60% weight)
                $collaborativeRecommendations = $this->collaborativeService
                    ->getRecommendations($user, ceil($limit * 0.6));
                
                // Get content-based recommendations (40% weight)
                $contentBasedRecommendations = $this->contentBasedService
                    ->getRecommendations($user, ceil($limit * 0.4));
                
                // Merge and deduplicate recommendations
                $hybridRecommendations = $this->mergeRecommendations(
                    $collaborativeRecommendations,
                    $contentBasedRecommendations,
                    $limit
                );
                
                return $hybridRecommendations;
                
            } catch (\Exception $e) {
                Log::error('Hybrid recommendation error: ' . $e->getMessage());
                
                // Fallback to content-based only
                return $this->contentBasedService->getRecommendations($user, $limit);
            }
        });
    }

    /**
     * Merge collaborative and content-based recommendations
     */
    protected function mergeRecommendations($collaborative, $contentBased, $limit)
    {
        $merged = collect();
        $usedIds = collect();
        
        // Add collaborative recommendations first (higher priority)
        foreach ($collaborative as $property) {
            if ($merged->count() >= $limit) break;
            
            if (!$usedIds->contains($property->id)) {
                $property->recommendation_type = 'collaborative';
                $property->recommendation_reason = 'Users with similar interests also liked';
                $merged->push($property);
                $usedIds->push($property->id);
            }
        }
        
        // Fill remaining slots with content-based recommendations
        foreach ($contentBased as $property) {
            if ($merged->count() >= $limit) break;
            
            if (!$usedIds->contains($property->id)) {
                $property->recommendation_type = 'content_based';
                $property->recommendation_reason = 'Similar to your preferred properties';
                $merged->push($property);
                $usedIds->push($property->id);
            }
        }
        
        return $merged;
    }

    /**
     * Clear recommendation cache for user
     */
    public function clearUserCache($userId)
    {
        Cache::forget("hybrid_recommendations_user_{$userId}");
        Cache::forget("collaborative_recommendations_user_{$userId}");
        Cache::forget("content_based_recommendations_user_{$userId}");
    }

    /**
     * Get user's wishlist property IDs for exclusion
     */
    public function getUserWishlistIds($user)
    {
        return Wishlist::where('user_id', $user->id)
            ->pluck('property_id')
            ->toArray();
    }
}
