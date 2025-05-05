@extends('frontend.frontend_dashboard')
@section('main')


   <!--Page Title-->
        <section class="page-title centred" style="background-image: url({{ asset('frontend/assets/images/background/page-title-5.jpg') }});">
            <div class="auto-container">
                <div class="content-box clearfix">
                    <h1>User Profile </h1>
                    <ul class="bread-crumb clearfix">
                        <li><a href="index.html">Home</a></li>
                        <li>User Profile </li>
                    </ul>
                </div>
            </div>
        </section>
        <!--End Page Title-->


        <!-- sidebar-page-container -->
        <section class="sidebar-page-container blog-details sec-pad-2">
            <div class="auto-container">
                <div class="row clearfix">




        @php

            $id = Auth::user()->id;
            $userData = App\Models\User::find($id);

        @endphp




        <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
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
                        <div class="blog-details-content">
                            <div class="news-block-one">
                                <div class="inner-box">

                                    <div class="lower-content">
                                        <h3>Property Visit Request</h3>





<div class="row">
<div class="col-lg-4">
    <div class="card-body" style="background-color: #1baf65;">
    <h1 class="card-title" style="color: white; font-weight: bold;">{{ $approvedSchedules }}</h1>
    <h5 class="card-text"style="color: white;"> Approved Visits</h5>

  </div>
</div>

<div class="col-md-4">
    <div class="card-body" style="background-color: #ffc107;">
    <h1 class="card-title" style="color: white; font-weight: bold; ">{{ $pendingSchedules }}</h1>
    <h5 class="card-text"style="color: white;"> Pending Visits</h5>

  </div>
</div>


<div class="col-md-4">
    <div class="card-body" style="background-color: #002758;">
    <h1 class="card-title" style="color: white; font-weight: bold;">{{ $rejectedSchedules }}</h1>
    <h5 class="card-text"style="color: white; "> Rejected Visits</h5>

  </div>
</div>

</div>

<div class="row mt-4">
<div class="col-lg-6">
    <div class="card-body" style="background-color: #8e44ad;">
    <h1 class="card-title" style="color: white; font-weight: bold;">{{ $wishlistCount }}</h1>
    <h5 class="card-text"style="color: white;"> Wishlist Properties</h5>

  </div>
</div>

<div class="col-md-6">
    <div class="card-body" style="background-color: #2980b9;">
    <h1 class="card-title" style="color: white; font-weight: bold; ">{{ $compareCount }}</h1>
    <h5 class="card-text"style="color: white;"> Compare Properties</h5>

  </div>
</div>

</div>

                                    </div>
                                </div>
                            </div>


                        </div>


    <div class="blog-details-content">
                            <div class="news-block-one">
                                <div class="inner-box">

                                    <div class="lower-content">
                                        <h3>Recent Property Visit Requests</h3>
                                      <hr>

          @if(count($recentSchedules) > 0)
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Property</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentSchedules as $schedule)
                <tr>
                  <td>
                    @if($schedule->property)
                      <a href="{{ url('/property/details/'.$schedule->property->id.'/'.$schedule->property->property_slug) }}">
                        {{ $schedule->property->property_name }}
                      </a>
                    @else
                      <span class="text-muted">Property not available</span>
                    @endif
                  </td>
                  <td>{{ $schedule->tour_date }}</td>
                  <td>{{ $schedule->tour_time }}</td>
                  <td>
                    @if($schedule->status == 'approved' || $schedule->status == '1')
                      <span class="badge bg-success">Approved</span>
                    @elseif($schedule->status == 'rejected' || $schedule->status == '2')
                      <span class="badge bg-danger">Rejected</span>
                    @else
                      <span class="badge bg-warning">Pending</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <a href="{{ route('user.schedule.request') }}" class="btn btn-primary">View All Requests</a>
          @else
          <p>You haven't made any property visit requests yet.</p>
          <a href="{{ route('all.property.list') }}" class="btn btn-primary">Browse Properties</a>
          @endif


                                    </div>
                                </div>
                            </div>


                        </div>






                    </div>


                </div>
            </div>
        </section>
        <!-- sidebar-page-container -->

        <!-- subscribe-section -->
        <section class="subscribe-section bg-color-3">
            <div class="pattern-layer" style="background-image: url(assets/images/shape/shape-2.png);"></div>
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