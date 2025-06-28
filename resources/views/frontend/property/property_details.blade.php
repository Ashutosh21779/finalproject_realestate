@extends('frontend.frontend_dashboard')
@section('main')

@push('scripts')
<script src="{{ asset('frontend/assets/js/mortgage-calculator-new.js') }}"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/assets/css/suggested-properties.css') }}">
@endpush

@section('title')
  {{ $property->property_name }} | Easy RealEstate
@endsection


   <!--Page Title-->
        <section class="page-title-two bg-color-1 centred">
            <div class="pattern-layer">
                <div class="pattern-1" style="background-image: url({{ asset('frontend/assets/images/shape/shape-9.png') }});"></div>
                <div class="pattern-2" style="background-image: url({{ asset('frontend/assets/images/shape/shape-10.png') }});"></div>
            </div>
            <div class="auto-container">
                <div class="content-box clearfix">
                    <h1>{{ $property->property_name }}</h1>
                    <ul class="bread-crumb clearfix">
                        <li><a href="index.html">Home</a></li>
                        <li>{{ $property->property_name }}</li>
                    </ul>
                </div>
            </div>
        </section>
        <!--End Page Title-->


        <!-- property-details -->
        <section class="property-details property-details-one">
            <div class="auto-container">
                <div class="top-details clearfix">
                    <div class="left-column pull-left clearfix">
                        <h3>{{ $property->property_name }}</h3>
                        <div class="author-info clearfix">
                            <div class="author-box pull-left">
                  @if($property->agent_id == Null)
  <figure class="author-thumb"><img src="{{ url('upload/ariyan.jpg') }}" alt=""></figure>
                      <h6>Admin</h6>
                  @else

                    <figure class="author-thumb"><img src="{{ (!empty($property->user->photo)) ? url('upload/agent_images/'.$property->user->photo) : url('upload/no_image.jpg') }}" alt=""></figure>
                                <h6>{{ $property->user->name }}</h6>

                  @endif



                            </div>
                            <ul class="rating clearfix pull-left">
                                <li><i class="icon-39"></i></li>
                                <li><i class="icon-39"></i></li>
                                <li><i class="icon-39"></i></li>
                                <li><i class="icon-39"></i></li>
                                <li><i class="icon-40"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="right-column pull-right clearfix">
                        <div class="price-inner clearfix">
                            <ul class="category clearfix pull-left">
     <li><a href="property-details.html">{{ $property->type->type_name }}</a></li>
                                <li><a href="property-details.html">For {{ $property->property_status }}</a></li>
                            </ul>
                            <div class="price-box pull-right">
                                <h3>NPR {{ $property->lowest_price }}</h3>
                            </div>
                        </div>
                        <ul class="other-option pull-right clearfix">
                            <li><a href="property-details.html"><i class="icon-37"></i></a></li>
                            <li><a href="property-details.html"><i class="icon-38"></i></a></li>
                            <li><a href="property-details.html"><i class="icon-12"></i></a></li>
                            <li><a href="property-details.html"><i class="icon-13"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                        <div class="property-details-content">
    <div class="carousel-inner">
        <div class="single-item-carousel owl-carousel owl-theme owl-dots-none">
        	@foreach($multiImage as $img)
            <figure class="image-box"><img src="{{ asset($img->photo_name) }}" alt=""></figure>
            @endforeach
        </div>
    </div>
                            <div class="discription-box content-widget">
                                <div class="title-box">
                                    <h4>Property Description</h4>
                                </div>
                                <div class="text">
                                    <p>{!! $property->long_descp !!}</p>
                                </div>
                            </div>
                            <div class="details-box content-widget">
                                <div class="title-box">
                                    <h4>Property Details</h4>
                                </div>
    <ul class="list clearfix">
        <li>Property ID: <span>{{ $property->property_code }}</span></li>
        <li>Rooms: <span>{{ $property->bedrooms }}</span></li>
        <li>Garage Size: <span>{{ $property->garage_size }} Sq Ft</span></li>

        <li>Property Type: <span>{{ $property->type->type_name }}</span></li>
        <li>Bathrooms: <span>{{ $property->bathrooms }}</span></li>
        <li>Property Status: <span>For {{ $property->property_status }}</span></li>
        <li>Property Size: <span>{{ $property->property_size }} Sq Ft</span></li>
        <li>Garage: <span>{{ $property->garage }}</span></li>
    </ul>
                            </div>
                            <div class="amenities-box content-widget">
                                <div class="title-box">
                                    <h4>Amenities</h4>
                                </div>
                                <ul class="list clearfix">
                                	@foreach($property_amen as $amen)
                                    <li>{{ $amen }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="location-box content-widget">
                                <div class="title-box">
                                    <h4>Location</h4>
                                </div>
<ul class="info clearfix">
    <li><span>Address:</span> {{ $property->address }}</li>
    <li><span>State/county:</span> {{ $property->state_name }}</li>
    <li><span>Neighborhood:</span> {{ $property->neighborhood }}</li>
    <li><span>Zip/Postal Code:</span> {{ $property->postal_code }}</li>
    <li><span>City:</span> {{ $property->city }}</li>
</ul>
<div class="google-map-area">
    <div class="map-container" style="width: 100%; height: 400px; border-radius: 10px; overflow: hidden; position: relative;">
        <!-- Primary Google Maps Embed API -->
        <iframe
            id="google-map-iframe"
            width="100%"
            height="100%"
            style="border:0; border-radius: 10px;"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBwjrCx-T4UlUDmXALumCOWkJv-B7m9yCE&q={{ $property->city }}, Nepal&zoom=13&maptype=roadmap&language=en&region=NP">
        </iframe>

        <!-- Fallback static map in case the iframe fails -->
        <div id="static-map-fallback" style="display: none; width: 100%; height: 100%; border-radius: 10px; background-position: center; background-size: cover;"
             title="{{ $property->property_name }} - {{ $property->address }}, {{ $property->city }}">
        </div>

        <!-- Loading indicator and error message -->
        <div id="map-loading" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; background-color: #f8f9fa; border-radius: 10px; z-index: -1;">
            <div style="text-align: center; padding: 20px;">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p id="map-error-message">Loading map...</p>
            </div>
        </div>
    </div>

    <div class="map-info mt-2">
        <p><i class="fas fa-map-marker-alt text-danger"></i> <strong>{{ $property->property_name }}</strong> - Located in {{ $property->city }}, Nepal</p>
        <p class="text-muted small">Address: {{ $property->address }}, {{ $property->city }}</p>
    </div>
</div>

<script>
    // Check if the Google Maps iframe loaded correctly
    document.addEventListener('DOMContentLoaded', function() {
        var mapIframe = document.getElementById('google-map-iframe');
        var staticMapFallback = document.getElementById('static-map-fallback');
        var mapLoading = document.getElementById('map-loading');
        var mapErrorMessage = document.getElementById('map-error-message');

        // Set up a fallback static map using the city
        var city = "{{ $property->city }}, Nepal";
        var encodedAddress = encodeURIComponent(city);
        var zoom = 13;

        // Use the address for the static map
        var staticMapUrl = 'https://maps.googleapis.com/maps/api/staticmap?center=' + encodedAddress +
                           '&zoom=' + zoom + '&size=600x400&maptype=roadmap&markers=color:red%7C' + encodedAddress +
                           '&key=AIzaSyBwjrCx-T4UlUDmXALumCOWkJv-B7m9yCE';

        // Fallback to coordinates if available
        var lat = {{ $property->latitude ?? 27.7172 }};
        var lng = {{ $property->longitude ?? 85.3240 }};

        // Alternative OpenStreetMap URL if Google Static Maps also fails
        var openStreetMapUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=' +
                              (lng - 0.01) + '%2C' + (lat - 0.01) + '%2C' +
                              (lng + 0.01) + '%2C' + (lat + 0.01) +
                              '&layer=mapnik&marker=' + lat + '%2C' + lng;

        // Set the background image for the static fallback
        staticMapFallback.style.backgroundImage = 'url("' + staticMapUrl + '")';

        // Function to handle iframe load error
        function handleMapError() {
            mapIframe.style.display = 'none';
            staticMapFallback.style.display = 'block';
            mapLoading.style.zIndex = '-1';

            // Create a link to view on Google Maps
            var viewOnGoogleMapsLink = document.createElement('a');
            viewOnGoogleMapsLink.href = 'https://www.google.com/maps/search/?api=1&query=' + encodedAddress;
            viewOnGoogleMapsLink.target = '_blank';
            viewOnGoogleMapsLink.className = 'btn btn-sm btn-primary mt-2 me-2';
            viewOnGoogleMapsLink.innerHTML = 'View on Google Maps';
            staticMapFallback.appendChild(viewOnGoogleMapsLink);

            // Add a fallback to OpenStreetMap if the static image fails to load
            var img = new Image();
            img.onerror = function() {
                console.log('Google Static Maps failed to load, using OpenStreetMap fallback');
                // Create an iframe for OpenStreetMap
                var openStreetMapIframe = document.createElement('iframe');
                openStreetMapIframe.width = '100%';
                openStreetMapIframe.height = '100%';
                openStreetMapIframe.style.border = '0';
                openStreetMapIframe.style.borderRadius = '10px';
                openStreetMapIframe.src = openStreetMapUrl;

                // Replace the static map div content with the OpenStreetMap iframe
                staticMapFallback.innerHTML = '';
                staticMapFallback.appendChild(openStreetMapIframe);

                // Re-add the Google Maps link
                staticMapFallback.appendChild(viewOnGoogleMapsLink);

                // Add an OpenStreetMap link
                var viewOnOsmLink = document.createElement('a');
                viewOnOsmLink.href = 'https://www.openstreetmap.org/?mlat=' + lat + '&mlon=' + lng + '&zoom=' + zoom;
                viewOnOsmLink.target = '_blank';
                viewOnOsmLink.className = 'btn btn-sm btn-info mt-2';
                viewOnOsmLink.innerHTML = 'View on OpenStreetMap';
                staticMapFallback.appendChild(viewOnOsmLink);

                mapErrorMessage.innerHTML = 'Using OpenStreetMap as fallback.';
            };
            img.src = staticMapUrl;

            console.log('Google Maps iframe failed to load, using static map fallback');
        }

        // Check if the iframe loaded correctly after a timeout
        setTimeout(function() {
            try {
                // If we can access the iframe content, it loaded correctly
                if (mapIframe.contentWindow.document) {
                    mapLoading.style.zIndex = '-1';
                    console.log('Google Maps iframe loaded successfully');
                }
            } catch (e) {
                // If we can't access the iframe content, it failed to load
                handleMapError();
                mapErrorMessage.innerHTML = 'Could not load interactive map. Displaying static map instead.';
            }
        }, 3000);

        // Also set up an error event listener for the iframe
        mapIframe.addEventListener('error', function() {
            handleMapError();
            mapErrorMessage.innerHTML = 'Error loading map. Displaying static map instead.';
        });
    });
</script>
                            </div>
                            <div class="nearby-box content-widget">
                                <div class="title-box">
                                    <h4>What's Nearby?</h4>
                                </div>
<div class="inner-box">


    <div class="single-item">
        <div class="icon-box"><i class="fas fa-book-reader"></i></div>
        <div class="inner">
            <h5>Places:</h5>

            @foreach($facility as $item)
            <div class="box clearfix">
                <div class="text pull-left">
                    <h6>{{ $item->facility_name }} <span>({{ $item->distance }} km)</span></h6>
                </div>
                <ul class="rating pull-right clearfix">
                    <li><i class="icon-39"></i></li>
                    <li><i class="icon-39"></i></li>
                    <li><i class="icon-39"></i></li>
                    <li><i class="icon-39"></i></li>
                    <li><i class="icon-40"></i></li>
                </ul>
            </div>
            @endforeach
        </div>
    </div>




                                </div>
                            </div>
                            <div class="statistics-box content-widget">
                                <div class="title-box">
                                    <h4>Property Video </h4>
                                </div>
<figure class="image-box">
   <iframe width="700" height="415" src="{{ $property->property_video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
</figure>
                            </div>


    <div class="schedule-box content-widget">
        <div class="title-box">
            <h4>Schedule A Tour</h4>
        </div>
        <div class="form-inner">
            <form action="{{ route('store.schedule') }}" method="post">
                @csrf


                <div class="row clearfix">

  <input type="hidden" name="property_id" value="{{ $property->id }}">

  @if($property->agent_id == Null)
  <input type="hidden" name="agent_id" value="">
  @else
<input type="hidden" name="agent_id" value="{{ $property->agent_id }}">
  @endif

                    <div class="col-lg-6 col-md-12 col-sm-12 column">
                        <div class="form-group">
                            <i class="far fa-calendar-alt"></i>
                            <input type="text" name="tour_date" placeholder="Tour Date" id="datepicker">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 column">
                        <div class="form-group">
                            <i class="far fa-clock"></i>
                            <input type="text" name="tour_time" placeholder="Any Time">
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 column">
                        <div class="form-group">
                            <textarea name="message" placeholder="Your message"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 column">
                        <div class="form-group message-btn">
                            <button type="submit" class="theme-btn btn-one">Submit Now</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
                        </div>
                    </div>


                    <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
    <div class="property-sidebar default-sidebar">
        <div class="author-widget sidebar-widget">
            <div class="author-box">

             @if($property->agent_id == Null)

              <figure class="author-thumb"><img src="{{ url('upload/ariyan.jpg') }}" alt=""></figure>
                <div class="inner">
                    <h4>Admin </h4>
                    <ul class="info clearfix">
                        <li><i class="fas fa-map-marker-alt"></i>84 St. John Wood High Street,
                        St Johns Wood</li>
                        <li><i class="fas fa-phone"></i><a href="tel:03030571965">030 3057 1965</a></li>
                    </ul>
                    <div class="btn-box"><a href="agents-details.html">View Listing</a></div>
                </div>

             @else

              <figure class="author-thumb"><img src="{{ (!empty($property->user->photo)) ? url('upload/agent_images/'.$property->user->photo) : url('upload/no_image.jpg') }}" alt=""></figure>
                <div class="inner">
                    <h4>{{ $property->user->name }}</h4>
                    <ul class="info clearfix">
                        <li><i class="fas fa-map-marker-alt"></i>{{ $property->user->address }}</li>
                        <li><i class="fas fa-phone"></i><a href="tel:03030571965">{{ $property->user->phone }}</a></li>
                    </ul>


   @auth
  <div class="btn-box mt-2" style="text-align: center;">
    <a href="{{ route('user.chat.property', ['propertyId' => $property->id]) }}" class="theme-btn btn-one">Chat With Agent</a>
  </div>
  @else
  <div class="mt-2" style="text-align: center;">
    <span class="text-danger">Login to Chat with Agent</span>
  </div>
  @endauth






                </div>

             @endif

            </div>



    <div class="form-inner">
@auth

@php
    $id = Auth::user()->id;
    $userData = App\Models\User::find($id);
@endphp

 <form action="{{ route('property.message') }}" method="post" class="default-form">
    @csrf

    <input type="hidden" name="property_id" value="{{ $property->id }}">

    @if($property->agent_id == Null)
    <input type="hidden" name="agent_id" value="">

    @else
    <input type="hidden" name="agent_id" value="{{ $property->agent_id }}">
    @endif

            <div class="form-group">
                <input type="text" name="msg_name" placeholder="Your name" value="{{ $userData->name }}">
            </div>
            <div class="form-group">
                <input type="email" name="msg_email" placeholder="Your Email" value="{{ $userData->email }}">
            </div>
            <div class="form-group">
                <input type="text" name="msg_phone" placeholder="Phone" value="{{ $userData->phone }}">
            </div>
            <div class="form-group">
                <textarea name="message" placeholder="Message"></textarea>
            </div>
            <div class="form-group message-btn">
                <button type="submit" class="theme-btn btn-one">Send Message</button>
            </div>
        </form>

@else

<form action="{{ route('property.message') }}" method="post" class="default-form">
    @csrf

    <input type="hidden" name="property_id" value="{{ $property->id }}">

    @if($property->agent_id == Null)
    <input type="hidden" name="agent_id" value="">

    @else
    <input type="hidden" name="agent_id" value="{{ $property->agent_id }}">
    @endif

            <div class="form-group">
                <input type="text" name="msg_name" placeholder="Your name" required="">
            </div>
            <div class="form-group">
                <input type="email" name="msg_email" placeholder="Your Email" required="">
            </div>
            <div class="form-group">
                <input type="text" name="msg_phone" placeholder="Phone" required="">
            </div>
            <div class="form-group">
                <textarea name="message" placeholder="Message"></textarea>
            </div>
            <div class="form-group message-btn">
                <button type="submit" class="theme-btn btn-one">Send Message</button>
            </div>
        </form>

@endauth



    </div>



</div>


                            <div class="calculator-widget sidebar-widget">
                                <div class="calculate-inner">
                                    <div class="widget-title">
                                        <h4>Mortgage Calculator</h4>
                                    </div>
                                    <form id="mortgage-calculator-form" class="default-form">
                                        <div class="form-group">
                                            <i class="fas fa-rupee-sign"></i>
                                            <input type="text" id="total-amount" class="currency-input" placeholder="Total Amount (NPR)" value="{{ $property->lowest_price }}">
                                            <div id="property-price" data-price="{{ $property->lowest_price }}"></div>
                                        </div>
                                        <div class="form-group">
                                            <i class="fas fa-percent"></i>
                                            <input type="text" id="down-payment" class="percentage-input" placeholder="Down Payment %" value="20">
                                        </div>
                                        <div class="form-group">
                                            <i class="fas fa-percent"></i>
                                            <input type="text" id="interest-rate" class="percentage-input" placeholder="Interest Rate %" value="5.5">
                                        </div>
                                        <div class="form-group">
                                            <i class="far fa-calendar-alt"></i>
                                            <input type="text" id="loan-term" placeholder="Loan Term (Years)" value="30">
                                        </div>
                                        <div class="form-group">
                                            <div class="select-box">
                                                <select id="payment-frequency" class="wide">
                                                   <option value="monthly" selected>Monthly</option>
                                                   <option value="biweekly">Bi-Weekly</option>
                                                   <option value="weekly">Weekly</option>
                                                   <option value="yearly">Yearly</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group message-btn">
                                            <button id="calculate-mortgage" class="theme-btn btn-one">Calculate</button>
                                        </div>

                                        <div class="mortgage-results" style="display: none;">
                                            <div class="result-box">
                                                <h5 id="payment-label">Monthly Payment</h5>
                                                <h3 id="payment-amount">$0.00</h3>
                                            </div>
                                            <div class="result-details">
                                                <div class="detail-item">
                                                    <span>Loan Amount:</span>
                                                    <strong id="loan-amount">$0.00</strong>
                                                </div>
                                                <div class="detail-item">
                                                    <span>Total Interest:</span>
                                                    <strong id="total-interest">$0.00</strong>
                                                </div>
                                                <div class="detail-item">
                                                    <span>Total Payment:</span>
                                                    <strong id="total-payment">$0.00</strong>
                                                </div>
                                            </div>
                                            <div class="reset-btn text-center mt-3">
                                                <button id="reset-calculator" class="btn btn-sm btn-outline-secondary">Reset</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Suggested Properties Section -->
                <div class="suggested-content">
                    <div class="title">
                        <h4>Suggested For You
                        @if(Auth::check())
                            <!-- <span class="personalized-indicator">Personalized</span> -->
                        @endif
                        </h4>
                        <p class="text-muted">
                            @if(Auth::check())
                                Properties in {{ $property->state_name }} tailored to your browsing history and preferences
                            @else
                                Properties in {{ $property->state_name }} similar to what you're viewing
                            @endif
                        </p>
                    </div>

                    @if($hasSuggestions)
                    <div class="row clearfix">
                        @foreach($suggestedProperties as $item)
                        <div class="col-lg-4 col-md-6 col-sm-12 feature-block">
                            <div class="feature-block-one wow fadeInUp animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                                <div class="inner-box">
                                    <div class="image-box">
                                        <figure class="image"><img src="{{ asset($item->property_thambnail) }}" alt=""></figure>
                                        <div class="batch"><i class="icon-11"></i></div>
                                        <span class="category">{{ $item->type->type_name }}</span>
                                        <span class="suggested-badge">Suggested</span>
                                    </div>
                                    <div class="lower-content">
                                        <div class="author-info clearfix">
                                            <div class="author pull-left">
                                              @if($item->agent_id == Null)
                                                <figure class="author-thumb"><img src="{{ url('upload/ariyan.jpg') }}" alt=""></figure>
                                                <h6>Admin</h6>
                                              @else
                                                <figure class="author-thumb"><img src="{{ (!empty($item->user->photo)) ? url('upload/agent_images/'.$item->user->photo) : url('upload/no_image.jpg') }}" alt=""></figure>
                                                <h6>{{ $item->user->name }}</h6>
                                              @endif
                                            </div>
                                            <div class="buy-btn pull-right"><a href="javascript:void(0)">For {{ $item->property_status }}</a></div>
                                        </div>
                                        <div class="title-text"><h4><a href="{{ url('property/details/'.$item->id.'/'.$item->property_slug) }}">{{ $item->property_name }}</a></h4></div>
                                        <div class="price-box clearfix">
                                            <div class="price-info pull-left">
                                                <h6>Start From</h6>
                                                <h4>NPR {{ $item->lowest_price }}</h4>
                                            </div>
                                            <ul class="other-option pull-right clearfix">
                                                <li><a aria-label="Compare" class="action-btn" id="{{ $item->id }}" onclick="addToCompare(this.id)"><i class="icon-12"></i></a></li>
                                                <li><a aria-label="Add to wishlist" class="action-btn" id="{{ $item->id }}" onclick="addToWishList(this.id)"><i class="icon-13"></i></a></li>
                                            </ul>
                                        </div>
                                        <p>{{ $item->short_descp }}</p>
                                        <ul class="more-details clearfix">
                                            <li><i class="icon-14"></i>{{ $item->bedrooms }} Beds</li>
                                            <li><i class="icon-15"></i>{{ $item->bathrooms }} Baths</li>
                                            <li><i class="icon-16"></i>{{ $item->property_size }} Sq Ft</li>
                                        </ul>
                                        <div class="btn-box"><a href="{{ url('property/details/'.$item->id.'/'.$item->property_slug) }}" class="theme-btn btn-two">See Details</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="no-suggestions-container text-center py-5">
                        <div class="no-suggestions-icon mb-4">
                            <i class="fas fa-search fa-4x text-muted"></i>
                        </div>
                        <h3 class="mb-3">No properties found in {{ $property->state_name }}</h3>
                        <p class="mb-4">We couldn't find any similar properties in this state at the moment.</p>
                        <div class="btn-box">
                            <a href="{{ route('all.property.list') }}" class="theme-btn btn-one mb-2">Browse All Properties</a>
                            @if(!Auth::check())
                            <p class="mt-3">
                                <a href="{{ route('login') }}" class="text-primary">Sign in</a> to get personalized property recommendations based on your preferences.
                            </p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>
        <!-- property-details end -->


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