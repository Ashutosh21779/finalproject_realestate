@extends('frontend.frontend_dashboard')
@section('main')

<!--Page Title-->
<section class="page-title-two bg-color-1 centred">
    <div class="pattern-layer">
        <div class="pattern-1" style="background-image: url({{ asset('frontend/assets/images/shape/shape-9.png') }});"></div>
        <div class="pattern-2" style="background-image: url({{ asset('frontend/assets/images/shape/shape-10.png') }});"></div>
    </div>
    <div class="auto-container">
        <div class="content-box clearfix">
            <h1>Our Agents</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>Agents</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->

<!-- agents-page-section -->
<section class="agents-page-section agents-list">
    <div class="auto-container">
        <div class="row clearfix">
            @foreach($agents as $agent)
            <div class="col-lg-6 col-md-12 col-sm-12 agents-block">
                <div class="agents-block-two">
                    <div class="inner-box">
                        <div class="image-box">
                            <figure class="image">
                                <img src="{{ (!empty($agent->photo)) ? url('upload/agent_images/'.$agent->photo) : url('upload/no_image.jpg') }}" alt="" style="width:270px; height:330px;">
                            </figure>
                        </div>
                        <div class="content-box">
                            <div class="title-inner">
                                <h4><a href="{{ route('agent.details', $agent->id) }}">{{ $agent->name }}</a></h4>
                                <span class="designation">{{ $agent->email }}</span>
                            </div>
                            <div class="text">
                                <p>{{ $agent->address }}</p>
                            </div>
                            <ul class="info clearfix">
                                @if($agent->phone)
                                <li><i class="fas fa-phone-alt"></i><a href="tel:{{ $agent->phone }}">{{ $agent->phone }}</a></li>
                                @endif
                            </ul>
                            <div class="btn-box">
                                <a href="{{ route('agent.details', $agent->id) }}" class="theme-btn btn-one">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- agents-page-section end -->

@endsection
