@extends('frontend.frontend_dashboard')
@section('main')

@section('title')
  Search Results | RealEstate
@endsection

@push('scripts')
<script>
    // Pass filter values to JavaScript
    var propertyFilters = {
        @if(isset($property_status) && !empty($property_status))
        property_status: "{{ $property_status }}",
        @endif

        @if(isset($stype) && !empty($stype))
        ptype_id: "{{ $stype }}",
        @endif

        @if(isset($sstate) && !empty($sstate))
        state: "{{ $sstate }}",
        @endif

        @if(isset($bedrooms) && !empty($bedrooms))
        bedrooms: "{{ $bedrooms }}",
        @endif

        @if(isset($bathrooms) && !empty($bathrooms))
        bathrooms: "{{ $bathrooms }}",
        @endif

        @if(isset($min_price) && !empty($min_price))
        min_price: "{{ $min_price }}",
        @endif

        @if(isset($max_price) && !empty($max_price))
        max_price: "{{ $max_price }}"
        @endif
    };
</script>
@endpush

<!--Page Title-->
<section class="page-title-two bg-color-1 centred">
    <div class="pattern-layer">
        <div class="pattern-1" style="background-image: url({{ asset('frontend/assets/images/shape/shape-9.png') }});"></div>
        <div class="pattern-2" style="background-image: url({{ asset('frontend/assets/images/shape/shape-10.png') }});"></div>
    </div>
    <div class="auto-container">
        <div class="content-box clearfix">
            <h1>Search Results</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>Property Search Results</li>
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
                    <div class="item-shorting clearfix mb-4">
                        <div class="left-column pull-left">
                            <h5>Found {{ count($property) }} Properties</h5>
                        </div>
                        <div class="right-column pull-right clearfix">
                            <div class="short-box clearfix">
                                <div class="select-box">
                                    <select class="wide" id="view-style">
                                        <option value="list" selected>List View</option>
                                        <option value="grid">Grid View</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Applied Filters Section -->
                    @if(isset($property_status) || isset($stype) || isset($sstate) || isset($bedrooms) || isset($bathrooms) || isset($min_price) || isset($max_price))
                    <div class="applied-filters mb-4">
                        <h6 class="mb-2">Applied Filters:</h6>
                        <div class="filter-tags">
                            @if(isset($property_status) && !empty($property_status))
                                <span class="badge bg-info text-white p-2 me-2">Status: {{ ucfirst($property_status) }}</span>
                            @endif

                            @if(isset($stype) && !empty($stype))
                                <span class="badge bg-info text-white p-2 me-2">Type: {{ $stype }}</span>
                            @endif

                            @if(isset($sstate) && !empty($sstate))
                                <span class="badge bg-info text-white p-2 me-2">Location: {{ $sstate }}</span>
                            @endif

                            @if(isset($bedrooms) && !empty($bedrooms))
                                <span class="badge bg-info text-white p-2 me-2">Bedrooms: {{ $bedrooms }}</span>
                            @endif

                            @if(isset($bathrooms) && !empty($bathrooms))
                                <span class="badge bg-info text-white p-2 me-2">Bathrooms: {{ $bathrooms }}</span>
                            @endif

                            @if(isset($min_price) && !empty($min_price))
                                <span class="badge bg-info text-white p-2 me-2">Min Price: NPR {{ number_format($min_price) }}</span>
                            @endif

                            @if(isset($max_price) && !empty($max_price))
                                <span class="badge bg-info text-white p-2 me-2">Max Price: NPR {{ number_format($max_price) }}</span>
                            @endif

                            <a href="{{ url('/properties') }}" class="btn btn-sm btn-danger">Clear All</a>
                        </div>
                    </div>
                    @endif

                    <div class="wrapper list">
                        <div class="deals-list-content list-item">

                        @if(count($property) > 0)
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
                                        <div class="buy-btn"><a href="#">For {{ $item->property_status }}</a></div>
                                    </div>
                                    <div class="lower-content">
                                        <div class="title-text"><h4><a href="{{ url('property/details/'.$item->id.'/'.$item->property_slug) }}">{{ $item->property_name }}</a></h4></div>
                                        <div class="price-box clearfix">
                                            <div class="price-info pull-left">
                                                <h6>Start From</h6>
                                                <h4>NPR {{ number_format($item->lowest_price) }}</h4>
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
                        @else
                            <div class="deals-block-one">
                                <div class="inner-box text-center p-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <h4>No properties found matching your criteria.</h4>
                                        <p>Try adjusting your filters or <a href="{{ url('/properties') }}" class="alert-link">view all properties</a>.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        </div>
                    </div>

                    {{-- Pagination can be added here if search results are paginated in controller --}}
                    {{-- <div class="pagination-wrapper">
                        {{ $property->links('vendor.pagination.custom') }}
                    </div> --}}

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