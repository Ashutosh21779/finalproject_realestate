@extends('frontend.frontend_dashboard')
@section('main')

@section('title')
  All Properties | RealEstate
@endsection

<!--Page Title-->
<section class="page-title-two bg-color-1 centred">
    <div class="pattern-layer">
        <div class="pattern-1" style="background-image: url({{ asset('frontend/assets/images/shape/shape-9.png') }});"></div>
        <div class="pattern-2" style="background-image: url({{ asset('frontend/assets/images/shape/shape-10.png') }});"></div>
    </div>
    <div class="auto-container">
        <div class="content-box clearfix">
            <h1>All Properties</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>All Properties</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->


<!-- property-page-section -->
<section class="property-page-section property-list">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <div class="property-content-side">
                    <div class="item-shorting clearfix">
                        <div class="left-column pull-left">
                            <p>Showing {{ $property->firstItem() }}-{{ $property->lastItem() }} of {{ $property->total() }} results</p>
                        </div>
                    </div>
                    <div class="wrapper list">
                        <div class="deals-list-content list-item">

                        @foreach($property as $item)
                            <div class="deals-block-one">
                                <div class="inner-box">
                                    <div class="image-box">
                                        <figure class="image"><img src="{{ asset($item->property_thambnail) }}" alt="" style="width:300px; height:350px;"></figure>
                                        <div class="batch"><i class="icon-11"></i></div>
                                        @if($item->featured == 1)
                                        <span class="category">Featured</span>
                                        @else
                                        <span class="category">New</span>
                                        @endif
                                        <div class="buy-btn"><a href="property-details.html">For {{ $item->property_status }}</a></div>
                                    </div>
                                    <div class="lower-content">
         <div class="title-text"><h4><a href="{{ url('property/details/'.$item->id.'/'.$item->property_slug) }}">{{ $item->property_name }}</a></h4></div>
                                        <div class="price-box clearfix">
                                            <div class="price-info pull-left">
                                                <h6>Start From</h6>
                                                <h4>NPR {{ $item->lowest_price }}</h4>
                                            </div>

                                        @if($item->agent_id == Null)
                                        <div class="author-widget pull-right">
                                            <figure class="author-thumb"><img src="{{ url('upload/ariyan.jpg') }}" alt=""></figure><span>Admin</span>
                                         </div>
                                        @else
                                        <div class="author-widget pull-right">
                                            <figure class="author-thumb"><img src="{{ (!empty($item->user->photo)) ? url('upload/agent_images/'.$item->user->photo) : url('upload/no_image.jpg') }}" alt=""></figure><span>{{ $item->user->name }}</span>
                                         </div>
                                        @endif
                                        </div>
                                        <p>{{ $item->short_descp }}</p>
                                        <ul class="more-details clearfix">
                                            <li><i class="icon-14"></i>{{ $item->bedrooms }} Beds</li>
                                            <li><i class="icon-15"></i>{{ $item->bathrooms }} Baths</li>
                                            <li><i class="icon-16"></i>{{ $item->property_size }} Sq Ft</li>
                                        </ul>
      <div class="other-info-box clearfix">
          <div class="btn-box pull-left"><a href="{{ url('property/details/'.$item->id.'/'.$item->property_slug) }}" class="theme-btn btn-two">See Details</a></div>
          <ul class="other-option pull-right clearfix">
              <li><a aria-label="Compare" class="action-btn" id="{{ $item->id }}" onclick="addToCompare(this.id)"><i class="icon-12"></i></a></li>
              <li><a aria-label="Add To Wishlist" class="action-btn" id="{{ $item->id }}" onclick="addToWishList(this.id)" ><i class="icon-13"></i></a></li>
          </ul>
      </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        </div>
                    </div>
                    <div class="pagination-wrapper">
                        {{ $property->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                 @include('frontend.property.property_sidebar')
            </div>
        </div>
    </div>
</section>
<!-- property-page-section end -->

@endsection