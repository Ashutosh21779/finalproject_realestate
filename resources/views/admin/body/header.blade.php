@php
  // Fetch latest messages directly in the header
  // Consider using a View Composer for better practice if this data is needed often
  $headerMessages = App\Models\PropertyMessage::latest()->take(5)->get();
  $messageCount = $headerMessages->count(); // Get count for the badge
@endphp

 <nav class="navbar">
        <a href="#" class="sidebar-toggler">
          <i data-feather="menu"></i>
        </a>
        <div class="navbar-content">
          <form class="search-form">
            <div class="input-group">
              <div class="input-group-text">
                <i data-feather="search"></i>
              </div>
              <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
            </div>
          </form>
          <ul class="navbar-nav">
         
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="appsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i data-feather="grid"></i>
              </a>
              <div class="dropdown-menu p-0" aria-labelledby="appsDropdown">
                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                  <p class="mb-0 fw-bold">Web Apps</p>
                  <a href="javascript:;" class="text-muted">Edit</a>
                </div>
                <div class="row g-0 p-1">
                  <div class="col-3 text-center">
                    <a href="pages/apps/chat.html" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i data-feather="message-square" class="icon-lg mb-1"></i><p class="tx-12">Chat</p></a>
                  </div>
                  <div class="col-3 text-center">
                    <a href="pages/apps/calendar.html" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i data-feather="calendar" class="icon-lg mb-1"></i><p class="tx-12">Calendar</p></a>
                  </div>
                  <div class="col-3 text-center">
                    <a href="pages/email/inbox.html" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i data-feather="mail" class="icon-lg mb-1"></i><p class="tx-12">Email</p></a>
                  </div>
                  <div class="col-3 text-center">
                    <a href="pages/general/profile.html" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i data-feather="instagram" class="icon-lg mb-1"></i><p class="tx-12">Profile</p></a>
                  </div>
                </div>
                <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                  <a href="javascript:;">View all</a>
                </div>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i data-feather="mail"></i>
                 @if($messageCount > 0)
                    <div class="indicator">
                        <div class="circle"></div>
                    </div>
                 @endif
              </a>
              <div class="dropdown-menu p-0" aria-labelledby="messageDropdown">
                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                  <p>{{ $messageCount }} New Messages</p>
                  {{-- <a href="javascript:;" class="text-muted">Clear all</a> --}} {{-- Clear all might need specific logic --}}
                </div>
                <div class="p-1">
                 @forelse($headerMessages as $msg)
                  <a href="{{ route('admin.property.message') }}" class="dropdown-item d-flex align-items-center py-2">
                    <div class="me-3">
                       <div class="avatar"> 
                          <span class="avatar-title rounded-circle">{{ substr($msg->msg_name ?? 'U', 0, 1) }}</span>
                       </div>
                    </div>
                    <div class="d-flex justify-content-between flex-grow-1">
                      <div class="me-4">
                        <p>{{ $msg->msg_name }}</p>
                        <p class="tx-12 text-muted">{{ Str::limit($msg->message, 25) }}</p>
                      </div>
                      <p class="tx-12 text-muted">{{ $msg->created_at->diffForHumans() }}</p>
                    </div>  
                  </a>
                 @empty
                  <div class="text-center text-muted p-3">
                    No new messages.
                  </div>
                 @endforelse
                 
                </div>
                <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                  <a href="{{ route('admin.property.message') }}">View all</a>
                </div>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i data-feather="bell"></i>
                {{-- <div class="indicator">
                  <div class="circle"></div>
                </div> --}} {{-- Hide indicator until real notifications exist --}}
              </a>
              <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                  <p>Notifications</p>
                  {{-- <a href="javascript:;" class="text-muted">Clear all</a> --}}
                </div>
                <div class="p-1">
                   <div class="text-center text-muted p-3">
                    No new notifications.
                  </div>
                  {{-- Static items removed 
                  <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                    ...
                  </a> 
                  --}}
                </div>
                <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                  {{-- <a href="javascript:;">View all</a> --}} {{-- Link disabled until notifications page exists --}}
                </div>
              </div>
            </li>

          @php

        $id = Auth::user()->id;
        $profileData = App\Models\User::find($id);

          @endphp


            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="wd-30 ht-30 rounded-circle" src="{{ (!empty($profileData->photo)) ? url('upload/admin_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" alt="profile">
              </a>
              <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                  <div class="mb-3">
                    <img class="wd-80 ht-80 rounded-circle" src="{{ (!empty($profileData->photo)) ? url('upload/admin_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" alt="">
                  </div>
                  <div class="text-center">
                    <p class="tx-16 fw-bolder">{{ $profileData->name }}</p>
                    <p class="tx-12 text-muted">{{ $profileData->email }}</p>
                  </div>
                </div>
                <ul class="list-unstyled p-1">
                  <li class="dropdown-item py-2">
      <a href="{{ route('admin.profile') }}" class="text-body ms-0">
                      <i class="me-2 icon-md" data-feather="user"></i>
                      <span>Profile</span>
                    </a>
                  </li>
                  <li class="dropdown-item py-2">
                    <a href="{{ route('admin.change.password') }}" class="text-body ms-0">
                      <i class="me-2 icon-md" data-feather="edit"></i>
                      <span>Change Password</span>
                    </a>
                  </li>
                  <li class="dropdown-item py-2">
                    <a href="javascript:;" class="text-body ms-0">
                      <i class="me-2 icon-md" data-feather="repeat"></i>
                      <span>Switch User</span>
                    </a>
                  </li>
                  <div class="dropdown-divider"></div>
                  <li class="dropdown-item py-2">
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item text-body ms-0" style="background: none; border: none; display: inline; cursor: pointer; padding: var(--bs-dropdown-item-padding-y) var(--bs-dropdown-item-padding-x); color: var(--bs-dropdown-link-color); width: 100%; text-align: left;">
                        <i class="me-2 icon-sm" data-feather="log-out"></i>
                        <span>Log Out</span>
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      </nav>