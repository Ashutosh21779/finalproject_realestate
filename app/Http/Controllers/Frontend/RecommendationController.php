<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\RecommendationService;
use App\Models\User;
use App\Models\Property;
use App\Models\Wishlist;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get hybrid recommendations for authenticated user
     */
    public function getRecommendations(Request $request)
    {
        try {
            // Validate authentication
            if (!Auth::check()) {
                return response()->json([
                    'error' => 'Authentication required for personalized recommendations'
                ], 401);
            }

            // Validate request parameters
            $validator = Validator::make($request->all(), [
                'limit' => 'nullable|integer|min:1|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Invalid parameters',
                    'details' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $limit = $request->get('limit', 6);

            // Check if user has sufficient data for recommendations
            $wishlistCount = Wishlist::where('user_id', $user->id)->count();
            
            if ($wishlistCount === 0) {
                return response()->json([
                    'message' => 'Add properties to your wishlist to get personalized recommendations',
                    'recommendations' => [],
                    'recommendation_type' => 'none'
                ]);
            }

            // Get hybrid recommendations
            $recommendations = $this->recommendationService
                ->getHybridRecommendations($user, $limit);

            // Format response
            $formattedRecommendations = $recommendations->map(function($property) {
                return [
                    'id' => $property->id,
                    'property_name' => $property->property_name,
                    'property_slug' => $property->property_slug,
                    'property_status' => $property->property_status,
                    'lowest_price' => $property->lowest_price,
                    'max_price' => $property->max_price,
                    'property_thambnail' => $property->property_thambnail,
                    'bedrooms' => $property->bedrooms,
                    'bathrooms' => $property->bathrooms,
                    'property_size' => $property->property_size,
                    'city' => $property->city,
                    'state_name' => $property->state_name ?? $property->state,
                    'recommendation_type' => $property->recommendation_type ?? 'hybrid',
                    'recommendation_reason' => $property->recommendation_reason ?? 'Recommended for you',
                    'agent_id' => $property->agent_id,
                    'created_at' => $property->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'recommendations' => $formattedRecommendations,
                'total_count' => $formattedRecommendations->count(),
                'user_wishlist_count' => $wishlistCount,
                'recommendation_type' => $wishlistCount >= 3 ? 'hybrid' : 'content_based'
            ]);

        } catch (\Exception $e) {
            \Log::error('Recommendation API error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Unable to generate recommendations at this time',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    /**
     * Clear user's recommendation cache
     */
    public function clearCache(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }

            $user = Auth::user();
            $this->recommendationService->clearUserCache($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Recommendation cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Cache clear error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Unable to clear cache'
            ], 500);
        }
    }

    /**
     * Get recommendation statistics for user
     */
    public function getStats(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }

            $user = Auth::user();
            
            // Get user statistics
            $wishlistCount = Wishlist::where('user_id', $user->id)->count();
            $viewedPropertiesCount = \App\Models\UserPropertyView::where('user_id', $user->id)->count();
            
            // Get wishlist property types for analysis
            $wishlistPropertyTypes = Wishlist::where('user_id', $user->id)
                ->join('properties', 'wishlists.property_id', '=', 'properties.id')
                ->join('property_types', 'properties.ptype_id', '=', 'property_types.id')
                ->select('property_types.type_name')
                ->get()
                ->groupBy('type_name')
                ->map(function($group) {
                    return $group->count();
                });

            // Get wishlist states/locations
            $wishlistStates = Wishlist::where('user_id', $user->id)
                ->join('properties', 'wishlists.property_id', '=', 'properties.id')
                ->select('properties.state', 'properties.city')
                ->get()
                ->groupBy('state')
                ->map(function($group) {
                    return $group->count();
                });

            return response()->json([
                'success' => true,
                'stats' => [
                    'wishlist_count' => $wishlistCount,
                    'viewed_properties_count' => $viewedPropertiesCount,
                    'can_get_collaborative' => $wishlistCount >= 3,
                    'preferred_property_types' => $wishlistPropertyTypes,
                    'preferred_locations' => $wishlistStates,
                    'recommendation_readiness' => $wishlistCount >= 3 ? 'full' : ($wishlistCount > 0 ? 'basic' : 'none')
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Stats API error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Unable to fetch statistics'
            ], 500);
        }
    }

    /**
     * Debug collaborative filtering specifically
     */
    public function debugCollaborative(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }

            $user = Auth::user();

            // Get user's wishlist
            $userWishlist = Wishlist::where('user_id', $user->id)->pluck('property_id')->toArray();

            // Get other users with wishlists
            $otherUsers = User::whereHas('wishlists')
                ->where('id', '!=', $user->id)
                ->where('status', 'active')
                ->with('wishlists')
                ->get();

            $debugInfo = [
                'current_user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'wishlist_count' => count($userWishlist),
                    'wishlist_properties' => $userWishlist
                ],
                'other_users' => [],
                'similarities' => [],
                'collaborative_recommendations' => []
            ];

            // Calculate similarities with other users
            foreach ($otherUsers as $otherUser) {
                $otherWishlist = $otherUser->wishlists->pluck('property_id')->toArray();

                $intersection = array_intersect($userWishlist, $otherWishlist);
                $union = array_unique(array_merge($userWishlist, $otherWishlist));
                $similarity = empty($union) ? 0 : count($intersection) / count($union);

                $debugInfo['other_users'][] = [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'wishlist_count' => count($otherWishlist),
                    'wishlist_properties' => $otherWishlist,
                    'intersection' => $intersection,
                    'union_size' => count($union),
                    'similarity' => $similarity
                ];

                if ($similarity >= 0.1) {
                    $debugInfo['similarities'][] = [
                        'user_id' => $otherUser->id,
                        'user_name' => $otherUser->name,
                        'similarity' => $similarity
                    ];
                }
            }

            // Test collaborative filtering service
            $collaborativeRecommendations = $this->collaborativeFilteringService->getRecommendations($user, 6);
            $debugInfo['collaborative_recommendations'] = $collaborativeRecommendations->map(function($property) {
                return [
                    'id' => $property->id,
                    'name' => $property->property_name,
                    'type' => $property->recommendation_type ?? 'not_set',
                    'reason' => $property->recommendation_reason ?? 'not_set'
                ];
            });

            return response()->json([
                'success' => true,
                'debug_info' => $debugInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Debug failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
