@php
    $states = App\Models\State::latest()->get();
    $ptypes = App\Models\PropertyType::latest()->get();
@endphp

<div class="default-sidebar property-sidebar">
    <div class="filter-widget sidebar-widget">
        <div class="widget-title">
            <h5>Property</h5>
        </div>
        <div class="widget-content">
            <form action="{{ route('all.property.search') }}" method="post" class="search-form">
                @csrf

                <!-- Property Type -->
                <div class="select-box mb-3">
                    <select name="ptype_id" class="wide">
                        <option value="">All Type</option>
                        @foreach($ptypes as $type)
                            <option value="{{ $type->type_name }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Location/State -->
                <div class="select-box mb-3">
                    <select name="state" class="wide">
                        <option value="">Select Location</option>
                        @foreach($states as $state)
                            <option value="{{ $state->state_name }}">{{ $state->state_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Property Status -->
                <div class="select-box mb-3">
                    <select name="property_status" class="wide">
                        <option value="">Property Status</option>
                        <option value="rent">For Rent</option>
                        <option value="buy">For Buy</option>
                    </select>
                </div>

                <!-- Bedrooms -->
                <div class="select-box mb-3">
                    <select name="bedrooms" class="wide">
                        <option value="">Rooms</option>
                        <option value="1">1 Bedroom</option>
                        <option value="2">2 Bedrooms</option>
                        <option value="3">3 Bedrooms</option>
                        <option value="4">4 Bedrooms</option>
                        <option value="5">5+ Bedrooms</option>
                    </select>
                </div>

                <!-- Bathrooms -->
                <div class="select-box mb-3">
                    <select name="bathrooms" class="wide">
                        <option value="">Bathrooms</option>
                        <option value="1">1 Bathroom</option>
                        <option value="2">2 Bathrooms</option>
                        <option value="3">3 Bathrooms</option>
                        <option value="4">4+ Bathrooms</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="filter-btn mb-4">
                    <button type="submit" class="theme-btn btn-one w-100"><i class="fas fa-filter"></i>&nbsp;Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Price Range Section -->
    <div class="price-filter sidebar-widget">
        <div class="widget-title">
            <h5>Select Price Range</h5>
        </div>
        <div class="range-slider clearfix">
            <form action="{{ route('all.property.search') }}" method="post">
                @csrf
                <div class="price-input d-flex justify-content-between mb-3">
                    <div class="field me-2 w-50">
                        <input type="text" name="min_price" class="input-min form-control" placeholder="Min">
                    </div>
                    <div class="field w-50">
                        <input type="text" name="max_price" class="input-max form-control" placeholder="Max">
                    </div>
                </div>
                <div class="filter-btn">
                    <button type="submit" class="theme-btn btn-one btn-sm w-100">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Property Status Links -->
    <div class="category-widget sidebar-widget">
        <div class="widget-title">
            <h5>Status Of Property</h5>
        </div>
        <ul class="category-list clearfix">
            <li><a href="{{ route('rent.property') }}">For Rent <span>({{ App\Models\Property::where('property_status','rent')->where('status','1')->count() }})</span></a></li>
            <li><a href="{{ route('buy.property') }}">For Buy <span>({{ App\Models\Property::where('property_status','buy')->where('status','1')->count() }})</span></a></li>
        </ul>
    </div>

    <!-- Featured Properties Widget -->
    <div class="featured-widget sidebar-widget">
        <div class="widget-title">
            <h5>Featured Properties</h5>
        </div>
        <div class="widget-content">
            @php
                $featured = App\Models\Property::where('featured', '1')
                                              ->where('status', '1')
                                              ->orderBy('id', 'DESC')
                                              ->limit(3)
                                              ->get();
            @endphp

            @foreach($featured as $item)
            <div class="single-item">
                <div class="image-box">
                    <figure class="image">
                        <img src="{{ asset($item->property_thambnail) }}" alt="" style="width:100px; height:80px;">
                    </figure>
                    <div class="batch"><i class="icon-11"></i></div>
                    <div class="buy-btn"><a href="#">For {{ $item->property_status }}</a></div>
                </div>
                <div class="title-box">
                    <p>${{ number_format($item->lowest_price) }}</p>
                    <h6><a href="{{ url('property/details/'.$item->id.'/'.$item->property_slug) }}">{{ Str::limit($item->property_name, 20) }}</a></h6>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>