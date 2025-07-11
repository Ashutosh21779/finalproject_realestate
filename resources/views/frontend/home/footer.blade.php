
   @php
   $setting = App\Models\SiteSetting::find(1);
   $blog = App\Models\BlogPost::latest()->limit(2)->get();
   @endphp

 <footer class="main-footer">
            <div class="footer-top bg-color-2">
                <div class="auto-container">
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                            <div class="footer-widget about-widget">
                                <div class="widget-title">
                                    <h3>About</h3>
                                </div>
                                <div class="text">
                                    <p>Lorem ipsum dolor amet consetetur adi pisicing elit sed eiusm tempor in cididunt ut labore dolore magna aliqua enim ad minim venitam</p>
                                    <p>Quis nostrud exercita laboris nisi ut aliquip commodo.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                            <div class="footer-widget links-widget ml-70">
                                <div class="widget-title">
                                    <h3>Company</h3>
                                </div>
                                <div class="links-inner">
                                    <ul>
                                        <li><a href="{{ url('/#chooseus-section') }}">How It Works</a></li>
                                        <!-- <li><a href="{{ url('/about') }}">About Us</a></li> -->
                                        <li><a href="{{ route('all.property.list') }}">Listing</a></li>
                                        @if(isset($property) && $property && !is_a($property, 'Illuminate\Pagination\LengthAwarePaginator') && !is_a($property, 'Illuminate\Database\Eloquent\Collection') && property_exists($property, 'id'))
                                            <li><a href="{{ route('user.chat.property', ['propertyId' => $property->id]) }}" class="theme-btn btn-one">Chat With Agent</a></li>
                                        @endif
                                        <!-- <li><a href="index.html">Our Services</a></li> -->
                                        <li><a href="{{ route('blog.list') }}">Our Blog</a></li>
                                        <!-- <li><a href="index.html">Contact Us</a></li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                            <div class="footer-widget post-widget">
                                <div class="widget-title">
                                    <h3>Top News</h3>
                                </div>
                                <div class="post-inner">

     @foreach($blog as $item)
    <div class="post">
        <figure class="post-thumb"><a href="{{ url('blog/details/'.$item->post_slug) }}"><img src="{{ asset($item->post_image) }}" alt=""></a></figure>
        <h5><a href="{{ url('blog/details/'.$item->post_slug) }}">{{ $item->post_title }}</a></h5>
        <p>{{ $item->created_at->format('M d Y') }}</p>
    </div>
    @endforeach

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                            <div class="footer-widget contact-widget">
                                <div class="widget-title">
                                    <h3>Contacts</h3>
                                </div>
                                <div class="widget-content">
    <ul class="info-list clearfix">
        @if($setting && $setting->company_address)
            <li><i class="fas fa-map-marker-alt"></i>{{ $setting->company_address }}</li>
        @endif
        @if($setting && $setting->support_phone)
            <li><i class="fas fa-microphone"></i><a href="tel:23055873407">+{{ $setting->support_phone }}</a></li>
        @endif
        @if($setting && $setting->email)
            <li><i class="fas fa-envelope"></i><a href="mailto:info@example.com">{{ $setting->email }}</a></li>
        @endif
    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="auto-container">
                    <div class="inner-box clearfix">
                        <!-- <figure class="footer-logo"><a href="index.html"><img src="{{ asset('frontend/assets/images/footer-logo.png') }}" alt=""></a></figure> -->
                        <div class="copyright pull-left">
                            <p><a href="index.html">{{ $setting && $setting->copyright ? $setting->copyright : '© ' . date('Y') . ' Real Estate. All rights reserved.' }}</p>
                        </div>
                        <ul class="footer-nav pull-right clearfix">
                            <li><a href="index.html">Terms of Service</a></li>
                            <li><a href="index.html">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>