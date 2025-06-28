# Hybrid Recommendation System for Real Estate Management

## Overview

This implementation provides a comprehensive hybrid recommendation system that combines collaborative filtering and content-based filtering to deliver personalized property recommendations for users in the wishlist section.

## Features

### 🎯 Hybrid Algorithm
- **Collaborative Filtering (60% weight)**: Recommends properties based on similar users' preferences using Jaccard similarity coefficient
- **Content-Based Filtering (40% weight)**: Recommends properties similar to user's wishlist based on property characteristics
- **Intelligent Fallback**: Falls back to content-based when insufficient data for collaborative filtering

### 🚀 Performance Optimizations
- **Redis Caching**: 1-hour cache for hybrid recommendations, 30-minute cache for individual algorithms
- **Database Indexing**: Optimized indexes on key columns for fast queries
- **Lazy Loading**: Recommendations loaded asynchronously on wishlist page
- **Batch Processing**: Efficient similarity calculations using Laravel Collections

### 🇳🇵 Nepal Context Integration
- **NPR Currency**: All prices displayed in Nepalese Rupees
- **Local Geography**: Geographical preference overlap for Kathmandu, Pokhara, Lalitpur, Bhaktapur
- **Price Range Flexibility**: ±20% tolerance for NPR price matching
- **Realistic Test Data**: Nepalese names, locations, and pricing in test cases

## Architecture

### Service Classes

#### 1. RecommendationService
- **Purpose**: Main orchestrator for hybrid recommendations
- **Location**: `app/Services/RecommendationService.php`
- **Key Methods**:
  - `getHybridRecommendations()`: Combines collaborative and content-based results
  - `clearUserCache()`: Invalidates user-specific recommendation cache

#### 2. CollaborativeFilteringService
- **Purpose**: User-based collaborative filtering using Jaccard similarity
- **Location**: `app/Services/CollaborativeFilteringService.php`
- **Algorithm**: 
  - Finds users with similar wishlist patterns
  - Calculates Jaccard similarity coefficient
  - Recommends properties liked by similar users
- **Minimum Requirement**: 3 properties in user's wishlist

#### 3. ContentBasedService
- **Purpose**: Property similarity based on characteristics
- **Location**: `app/Services/ContentBasedService.php`
- **Features**:
  - Property type matching (30% weight)
  - Location/state similarity (25% weight)
  - Bedroom count similarity (20% weight)
  - Price range matching (15% weight)
  - Bathroom count similarity (10% weight)

### API Endpoints

#### GET `/api/recommendations`
- **Purpose**: Get personalized recommendations for authenticated user
- **Parameters**: 
  - `limit` (optional): Number of recommendations (1-20, default: 6)
- **Response**: JSON with recommendations array and metadata

#### POST `/api/recommendations/clear-cache`
- **Purpose**: Clear user's recommendation cache
- **Authentication**: Required
- **Response**: Success confirmation

#### GET `/api/recommendations/stats`
- **Purpose**: Get user's recommendation statistics
- **Response**: Wishlist count, readiness level, preferences analysis

### Database Schema

#### New Indexes Added
```sql
-- Wishlist table indexes
CREATE INDEX idx_wishlists_user_id ON wishlists(user_id);
CREATE INDEX idx_wishlists_property_id ON wishlists(property_id);
CREATE INDEX idx_wishlists_user_property ON wishlists(user_id, property_id);

-- Properties table indexes
CREATE INDEX idx_properties_ptype_id ON properties(ptype_id);
CREATE INDEX idx_properties_state ON properties(state);
CREATE INDEX idx_properties_status ON properties(status);
CREATE INDEX idx_properties_status_ptype ON properties(status, ptype_id);
CREATE INDEX idx_properties_status_state ON properties(status, state);
```

#### Model Relationships Added
```php
// User.php
public function wishlists() {
    return $this->hasMany(Wishlist::class);
}

public function propertyViews() {
    return $this->hasMany(UserPropertyView::class);
}
```

## Frontend Integration

### Wishlist Page Enhancement
- **Location**: `resources/views/frontend/dashboard/wishlist.blade.php`
- **Features**:
  - Asynchronous recommendation loading
  - Personalized recommendation badges
  - Recommendation type indicators (Similar Users vs Similar Properties)
  - Refresh recommendations functionality
  - Fallback messaging for insufficient data

### JavaScript Functions
- `loadRecommendations()`: Fetches and displays recommendations
- `displayRecommendations()`: Renders recommendation cards
- `clearRecommendationCache()`: Refreshes recommendations

### CSS Styling
- Personalized indicator with pulse animation
- Recommendation badges and type indicators
- Loading states and error handling
- Responsive design for mobile devices

## Installation & Setup

### 1. Run Database Migration
```bash
php artisan migrate
```

### 2. Clear Application Cache
```bash
php artisan cache:clear
php artisan config:clear
```

### 3. Install Dependencies (if needed)
```bash
composer install
```

### 4. Configure Redis (Optional but Recommended)
Ensure Redis is configured in `.env` for optimal caching performance:
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Testing

### Run Feature Tests
```bash
php artisan test tests/Feature/RecommendationSystemTest.php
```

### Test Coverage
- ✅ Hybrid recommendation generation
- ✅ Collaborative filtering with similar users
- ✅ Content-based filtering with property similarity
- ✅ Cache functionality and performance
- ✅ API endpoint authentication and validation
- ✅ Edge cases (no wishlist, insufficient data)
- ✅ Nepalese context integration

## Performance Metrics

### Target Performance
- **Load Time**: 2-3 seconds for recommendation generation
- **Cache Hit Rate**: >80% for returning users
- **Database Queries**: <10 queries per recommendation request
- **Memory Usage**: <50MB for recommendation calculation

### Monitoring
- Cache hit/miss ratios logged
- Recommendation generation time tracked
- User interaction patterns analyzed
- Error rates monitored with fallback mechanisms

## Security Considerations

### Authentication
- All recommendation endpoints require user authentication
- User can only access their own recommendations
- Cache keys include user ID for isolation

### Data Privacy
- No sensitive user data exposed in recommendations
- Recommendation reasons are generic and safe
- User similarity calculations don't expose personal information

## Future Enhancements

### Planned Features
1. **Machine Learning Integration**: TensorFlow/PyTorch models for advanced recommendations
2. **Real-time Updates**: WebSocket integration for live recommendation updates
3. **A/B Testing**: Framework for testing different recommendation algorithms
4. **Analytics Dashboard**: Admin panel for recommendation performance metrics
5. **Mobile API**: Dedicated mobile app endpoints with optimized responses

### Scalability Considerations
- **Microservice Architecture**: Separate recommendation service for high-traffic scenarios
- **Distributed Caching**: Redis Cluster for large-scale deployments
- **Queue Processing**: Background job processing for expensive calculations
- **CDN Integration**: Static recommendation data caching

## Support & Maintenance

### Monitoring Commands
```bash
# Check recommendation cache status
php artisan cache:table

# Clear all recommendation caches
php artisan cache:forget "hybrid_recommendations_*"

# Monitor recommendation performance
php artisan queue:work --queue=recommendations
```

### Troubleshooting
- **No Recommendations**: Check user has sufficient wishlist items (minimum 1)
- **Slow Performance**: Verify Redis is running and indexes are created
- **Cache Issues**: Clear application cache and restart Redis
- **API Errors**: Check Laravel logs in `storage/logs/laravel.log`

---

**Implementation Date**: June 27, 2025  
**Laravel Version**: 10.x  
**PHP Version**: 8.1+  
**Database**: MySQL 8.0+  
**Cache**: Redis 6.0+
