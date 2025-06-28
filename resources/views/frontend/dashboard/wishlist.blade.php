@extends('frontend.frontend_dashboard')
@section('main')

<style>
/* Recommendation Section Styles */
.personalized-indicator {
    display: inline-block;
    margin-left: 12px;
    padding: 4px 10px;
    background: #ffedcc;
    color: #ff9900;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    vertical-align: middle;
    box-shadow: 0 2px 4px rgba(255, 153, 0, 0.15);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 153, 0, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(255, 153, 0, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 153, 0, 0); }
}

.recommendation-item {
    position: relative;
    margin-bottom: 20px;
}

.recommendation-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255, 153, 0, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    z-index: 2;
}

.loading-recommendations {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.loading-recommendations i {
    font-size: 2em;
    margin-bottom: 10px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>


    <!--Page Title-->
        <section class="page-title-two bg-color-1 centred">
            <div class="pattern-layer">
                <div class="pattern-1" style="background-image: url({{ asset('frontend/assets/images/shape/shape-9.png') }});"></div>
                <div class="pattern-2" style="background-image: url({{ asset('frontend/assets/images/shape/shape-10.png') }});"></div>
            </div>
            <div class="auto-container">
                <div class="content-box clearfix">
                    <h1>WishList Property </h1>
                    <ul class="bread-crumb clearfix">
                        <li><a href="index.html">Home</a></li>
                        <li>WishList Property</li>
                    </ul>
                </div>
            </div>
        </section>
        <!--End Page Title-->


        <!-- property-page-section -->
        <section class="property-page-section property-list">
            <div class="auto-container">
                <div class="row clearfix">
                    <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                         
        @php

            $id = Auth::user()->id;
            $userData = App\Models\User::find($id); 
        @endphp


   <div class="blog-sidebar">
  <div class="sidebar-widget post-widget">
                    <div class="widget-title">
                        <h4>User Profile </h4>
                    </div>
                       <div class="post-inner">
                        <div class="post">
                            <figure class="post-thumb"><a href="blog-details.html">
        <img src="{{ (!empty($userData->photo)) ? url('upload/user_images/'.$userData->photo) : url('upload/no_image.jpg') }}" alt=""></a></figure>
        <h5><a href="blog-details.html">{{ $userData->name }} </a></h5>
         <p>{{ $userData->email }} </p>
                        </div> 
                    </div>
                </div> 


<div class="sidebar-widget category-widget">
            <div class="widget-title">
                
            </div>
             @include('frontend.dashboard.dashboard_sidebar')


          </div> 
</div>






                    </div>
                    <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                        <div class="property-content-side">
                            
                            <div class="wrapper list">
                                <div class="deals-list-content list-item">
                                 
                  <div id="wishlist">

                  </div>

                  <!-- Recommendations Section -->
                  <div id="recommendations-section" class="mt-5" style="display: none;">
                      <div class="sec-title">
                          <h5>Recommended Properties</h5>
                          <span class="personalized-indicator">
                              <i class="fas fa-magic"></i> Personalized for You
                          </span>
                      </div>
                      <div id="recommendations-container">
                          <!-- Recommendations will be loaded here -->
                      </div>
                      <div class="text-center mt-3">
                          <button id="refresh-recommendations" class="theme-btn btn-two">
                              <i class="fas fa-sync-alt"></i> Refresh Recommendations
                          </button>
                          <button id="debug-stats" class="theme-btn btn-one ml-2">
                              <i class="fas fa-bug"></i> Debug Stats
                          </button>
                          <button id="debug-collaborative" class="theme-btn btn-three ml-2">
                              <i class="fas fa-users"></i> Debug Collaborative
                          </button>
                      </div>
                  </div>

                  <!-- No Recommendations Message -->
                  <div id="no-recommendations-message" class="text-center mt-5" style="display: none;">
                      <div class="no-suggestions-container py-4">
                          <div class="no-suggestions-icon mb-3">
                              <i class="fas fa-heart fa-3x text-muted"></i>
                          </div>
                          <h4 class="mb-3">Get Personalized Recommendations</h4>
                          <p class="mb-3">Add more properties to your wishlist to receive personalized recommendations based on your preferences.</p>
                          <div class="btn-box">
                              <a href="{{ route('all.property.list') }}" class="theme-btn btn-one">Browse Properties</a>
                          </div>
                      </div>
                  </div>

               </div>
                           
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- property-page-section end -->


        <!-- subscribe-section -->
        <section class="subscribe-section bg-color-3">
            <div class="pattern-layer" style="background-image: url({{ asset('frontend/assets/images/shape/shape-2.png') }});"></div>
            <div class="auto-container">
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-6 col-sm-12 text-column">
                        <div class="text">
                            <span>Subscribe</span>
                            <h2>Sign Up To Our Newsletter To Get The Latest News And Offers.</h2>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 form-column">
                        <div class="form-inner">
                            <form action="contact.html" method="post" class="subscribe-form">
                                <div class="form-group">
                                    <input type="email" name="email" placeholder="Enter your email" required="">
                                    <button type="submit">Subscribe Now</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- subscribe-section end -->

@endsection

@push('scripts')
<script>
// Ensure jQuery is loaded before executing
if (typeof $ === 'undefined') {
    console.error('jQuery is not loaded! Recommendation system cannot function.');
} else {
    console.log('jQuery is loaded successfully');

    // Load recommendations when page loads
    $(document).ready(function() {
    console.log('Wishlist page loaded, starting recommendation loading...');
    loadRecommendations();

    // Refresh recommendations button
    $('#refresh-recommendations').on('click', function() {
        clearRecommendationCache();
    });

    // Debug stats button
    $('#debug-stats').on('click', function() {
        $.ajax({
            type: "GET",
            url: "/api/recommendations/stats",
            dataType: 'json',
            success: function(response) {
                console.log('Debug Stats:', response);
                alert('Debug stats logged to console. Check F12 > Console tab.');
            },
            error: function(xhr) {
                console.error('Debug stats error:', xhr.status, xhr.responseJSON);
                alert('Debug error: ' + xhr.status + '. Check console for details.');
            }
        });
    });

    // Debug collaborative filtering button
    $('#debug-collaborative').on('click', function() {
        $.ajax({
            type: "GET",
            url: "/api/recommendations/debug-collaborative",
            dataType: 'json',
            success: function(response) {
                console.log('Collaborative Debug Info:', response);
                alert('Collaborative filtering debug info logged to console. Check F12 > Console tab for detailed analysis.');
            },
            error: function(xhr) {
                console.error('Collaborative debug error:', xhr.status, xhr.responseJSON);
                alert('Collaborative debug error: ' + xhr.status + '. Check console for details.');
            }
        });
    });
});

function loadRecommendations() {
    console.log('loadRecommendations() called');

    // Show loading state
    $('#recommendations-container').html(`
        <div class="loading-recommendations">
            <i class="fas fa-spinner"></i>
            <p>Loading personalized recommendations...</p>
        </div>
    `);
    $('#recommendations-section').show();

    console.log('Making AJAX request to /api/recommendations');
    $.ajax({
        type: "GET",
        url: "/api/recommendations",
        dataType: 'json',
        data: { limit: 6 },
        success: function(response) {
            console.log('Recommendations API response:', response);
            if (response.success && response.recommendations.length > 0) {
                console.log('Displaying recommendations:', response.recommendations.length);
                displayRecommendations(response.recommendations);
                $('#recommendations-section').show();
                $('#no-recommendations-message').hide();
            } else {
                console.log('No recommendations available, showing message');
                $('#recommendations-section').hide();
                $('#no-recommendations-message').show();
            }
        },
        error: function(xhr) {
            console.error('Recommendation loading error:', xhr.status, xhr.responseJSON);
            if (xhr.status === 401) {
                console.log('Authentication error - hiding recommendations');
                $('#recommendations-section').hide();
                $('#no-recommendations-message').show();
            } else {
                console.log('Other error - showing retry option');
                $('#recommendations-container').html(`
                    <div class="text-center py-4">
                        <p class="text-muted">Unable to load recommendations at this time.</p>
                        <button onclick="loadRecommendations()" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-retry"></i> Try Again
                        </button>
                    </div>
                `);
                $('#recommendations-section').show();
            }
        }
    });
}

function displayRecommendations(recommendations) {
    let html = '';

    recommendations.forEach(function(property) {
        const recommendationType = property.recommendation_type === 'collaborative' ?
            'Similar Users' : 'Similar Properties';

        html += `
            <div class="deals-block-one recommendation-item">
                <div class="inner-box">
                    <div class="recommendation-badge">${recommendationType}</div>
                    <div class="image-box">
                        <figure class="image">
                            <img src="/${property.property_thambnail}" alt="${property.property_name}">
                        </figure>
                        <div class="batch"><i class="icon-11"></i></div>
                        <span class="category">Recommended</span>
                        <div class="buy-btn">
                            <a href="#">For ${property.property_status}</a>
                        </div>
                    </div>
                    <div class="lower-content">
                        <div class="title-text">
                            <h4><a href="/property/details/${property.id}/${property.property_slug}">${property.property_name}</a></h4>
                        </div>
                        <div class="price-box clearfix">
                            <div class="price-info pull-left">
                                <h6>Start From</h6>
                                <h4>NPR ${property.lowest_price}</h4>
                            </div>
                        </div>
                        <ul class="more-details clearfix">
                            <li><i class="icon-14"></i>${property.bedrooms || 'N/A'} Beds</li>
                            <li><i class="icon-15"></i>${property.bathrooms || 'N/A'} Baths</li>
                            <li><i class="icon-16"></i>${property.property_size || 'N/A'} Sq Ft</li>
                        </ul>
                        <div class="other-info-box clearfix">
                            <div class="recommendation-reason">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb"></i> ${property.recommendation_reason}
                                </small>
                            </div>
                            <ul class="other-option pull-right clearfix">
                                <li>
                                    <a href="javascript:void(0)" onclick="addToWishList(${property.id})" class="text-body">
                                        <i class="fa fa-heart"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="addToCompare(${property.id})" class="text-body">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    $('#recommendations-container').html(html);
}

function clearRecommendationCache() {
    $.ajax({
        type: "POST",
        url: "/api/recommendations/clear-cache",
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Show success message
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });

                Toast.fire({
                    type: 'success',
                    icon: 'success',
                    title: 'Recommendations refreshed!'
                });

                // Reload recommendations
                loadRecommendations();
            }
        },
        error: function(xhr) {
            console.error('Cache clear error:', xhr.responseJSON);
        }
    });
} // End jQuery check
}
</script>
@endpush