<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\Wishlist;
use App\Services\RecommendationService;
use App\Services\CollaborativeFilteringService;
use App\Services\ContentBasedService;
use Illuminate\Support\Facades\Cache;

class RecommendationSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $properties;
    protected $recommendationService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'role' => 'user',
            'status' => 'active'
        ]);

        // Create or get property types
        $propertyTypes = PropertyType::firstOrCreate(['type_name' => 'Test Apartment'], [
            'type_icon' => 'icon-1'
        ]);
        $propertyType2 = PropertyType::firstOrCreate(['type_name' => 'Test House'], [
            'type_icon' => 'icon-2'
        ]);

        // Create or get states
        $state1 = State::firstOrCreate(['state_name' => 'Test Kathmandu'], [
            'state_image' => 'upload/state/kathmandu.jpg'
        ]);
        $state2 = State::firstOrCreate(['state_name' => 'Test Pokhara'], [
            'state_image' => 'upload/state/pokhara.jpg'
        ]);

        // Create test properties manually
        $this->properties = collect();
        for ($i = 1; $i <= 10; $i++) {
            $property = Property::create([
                'ptype_id' => ($i % 2 == 0) ? $propertyTypes->id : $propertyType2->id,
                'amenities_id' => '1,2,3',
                'property_name' => "Test Property {$i}",
                'property_slug' => "test-property-{$i}",
                'property_code' => "PC{$i}000",
                'property_status' => ($i % 2 == 0) ? 'rent' : 'buy',
                'lowest_price' => rand(5000000, 20000000),
                'max_price' => rand(20000000, 50000000),
                'property_thambnail' => 'upload/property/test.jpg',
                'short_descp' => "Test property {$i} description",
                'long_descp' => "Long description for test property {$i}",
                'bedrooms' => (string)rand(1, 4),
                'bathrooms' => (string)rand(1, 3),
                'garage' => '1',
                'property_size' => (string)rand(1000, 3000),
                'address' => "Test Address {$i}",
                'city' => ($i % 2 == 0) ? 'Kathmandu' : 'Pokhara',
                'state' => ($i % 2 == 0) ? $state1->id : $state2->id,
                'postal_code' => '44600',
                'agent_id' => $this->user->id,
                'status' => '1',
            ]);
            $this->properties->push($property);
        }

        // Initialize recommendation service
        $this->recommendationService = app(RecommendationService::class);
    }

    /** @test */
    public function test_user_can_get_recommendations_with_sufficient_wishlist()
    {
        // Add 3 properties to wishlist (minimum for collaborative filtering)
        $wishlistProperties = $this->properties->take(3);
        foreach ($wishlistProperties as $property) {
            Wishlist::create([
                'user_id' => $this->user->id,
                'property_id' => $property->id
            ]);
        }

        $this->actingAs($this->user);

        $response = $this->getJson('/api/recommendations?limit=5');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'recommendations' => [
                        '*' => [
                            'id',
                            'property_name',
                            'property_slug',
                            'lowest_price',
                            'recommendation_type',
                            'recommendation_reason'
                        ]
                    ],
                    'total_count',
                    'user_wishlist_count',
                    'recommendation_type'
                ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(3, $response->json('user_wishlist_count'));
    }

    /** @test */
    public function test_user_gets_content_based_recommendations_with_insufficient_wishlist()
    {
        // Add only 1 property to wishlist (insufficient for collaborative filtering)
        Wishlist::create([
            'user_id' => $this->user->id,
            'property_id' => $this->properties->first()->id
        ]);

        $this->actingAs($this->user);

        $response = $this->getJson('/api/recommendations?limit=5');

        $response->assertStatus(200);
        $this->assertEquals('content_based', $response->json('recommendation_type'));
    }

    /** @test */
    public function test_unauthenticated_user_cannot_get_recommendations()
    {
        $response = $this->getJson('/api/recommendations');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
    }

    /** @test */
    public function test_user_with_no_wishlist_gets_appropriate_message()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/recommendations');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Add properties to your wishlist to get personalized recommendations',
                    'recommendations' => [],
                    'recommendation_type' => 'none'
                ]);
    }

    /** @test */
    public function test_collaborative_filtering_finds_similar_users()
    {
        $collaborativeService = app(CollaborativeFilteringService::class);

        // Create another user with similar preferences
        $similarUser = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Both users like the same properties
        $sharedProperties = $this->properties->take(3);
        foreach ($sharedProperties as $property) {
            Wishlist::create(['user_id' => $this->user->id, 'property_id' => $property->id]);
            Wishlist::create(['user_id' => $similarUser->id, 'property_id' => $property->id]);
        }

        // Similar user likes additional properties
        $additionalProperties = $this->properties->skip(3)->take(2);
        foreach ($additionalProperties as $property) {
            Wishlist::create(['user_id' => $similarUser->id, 'property_id' => $property->id]);
        }

        $recommendations = $collaborativeService->getRecommendations($this->user, 5);

        $this->assertGreaterThan(0, $recommendations->count());
    }

    /** @test */
    public function test_content_based_filtering_recommends_similar_properties()
    {
        $contentBasedService = app(ContentBasedService::class);

        // Test user with no wishlist should get fallback recommendations
        $userWithoutWishlist = User::factory()->create([
            'role' => 'user',
            'status' => 'active'
        ]);

        $recommendations = $contentBasedService->getRecommendations($userWithoutWishlist, 5);
        $this->assertGreaterThan(0, $recommendations->count(), "Should have fallback recommendations");

        // Test user with wishlist should get recommendations (content-based or fallback)
        $wishlistProperty = $this->properties->first();
        Wishlist::create([
            'user_id' => $this->user->id,
            'property_id' => $wishlistProperty->id
        ]);

        Cache::forget("content_based_recommendations_user_{$this->user->id}");
        $recommendations = $contentBasedService->getRecommendations($this->user, 5);

        // Should get recommendations (now with fallback mechanism fixed)
        $this->assertGreaterThan(0, $recommendations->count(), "Should have recommendations with fallback mechanism");

        // Ensure recommendations don't include the wishlist property
        $recommendedIds = $recommendations->pluck('id')->toArray();
        $this->assertNotContains($wishlistProperty->id, $recommendedIds, "Recommendations should not include wishlist properties");
    }

    /** @test */
    public function test_recommendation_cache_works()
    {
        // Add properties to wishlist
        $wishlistProperties = $this->properties->take(3);
        foreach ($wishlistProperties as $property) {
            Wishlist::create([
                'user_id' => $this->user->id,
                'property_id' => $property->id
            ]);
        }

        // First call should cache the results
        $recommendations1 = $this->recommendationService->getHybridRecommendations($this->user, 5);

        // Second call should use cache
        $recommendations2 = $this->recommendationService->getHybridRecommendations($this->user, 5);

        $this->assertEquals($recommendations1->count(), $recommendations2->count());

        // Clear cache
        $this->recommendationService->clearUserCache($this->user->id);

        // Verify cache is cleared
        $cacheKey = "hybrid_recommendations_user_{$this->user->id}";
        $this->assertFalse(Cache::has($cacheKey));
    }

    /** @test */
    public function test_clear_cache_endpoint()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/recommendations/clear-cache');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Recommendation cache cleared successfully'
                ]);
    }

    /** @test */
    public function test_recommendation_stats_endpoint()
    {
        // Add some properties to wishlist
        $wishlistProperties = $this->properties->take(2);
        foreach ($wishlistProperties as $property) {
            Wishlist::create([
                'user_id' => $this->user->id,
                'property_id' => $property->id
            ]);
        }

        $this->actingAs($this->user);

        $response = $this->getJson('/api/recommendations/stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'stats' => [
                        'wishlist_count',
                        'viewed_properties_count',
                        'can_get_collaborative',
                        'preferred_property_types',
                        'preferred_locations',
                        'recommendation_readiness'
                    ]
                ]);

        $this->assertEquals(2, $response->json('stats.wishlist_count'));
        $this->assertEquals('basic', $response->json('stats.recommendation_readiness'));
    }

    /** @test */
    public function test_recommendation_excludes_wishlist_properties()
    {
        // Add all properties to wishlist
        foreach ($this->properties as $property) {
            Wishlist::create([
                'user_id' => $this->user->id,
                'property_id' => $property->id
            ]);
        }

        $recommendations = $this->recommendationService->getHybridRecommendations($this->user, 5);

        // Should not recommend properties already in wishlist
        $recommendedIds = $recommendations->pluck('id')->toArray();
        $wishlistIds = $this->properties->pluck('id')->toArray();

        $this->assertEmpty(array_intersect($recommendedIds, $wishlistIds));
    }
}
