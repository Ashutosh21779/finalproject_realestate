@extends('agent.agent_dashboard')
@section('agent')

@php
$id = Auth::user()->id;
$agentId = App\Models\User::find($id);
$status = $agentId->status;

// Prepare default data
$defaultMonthlyData = [0, 0, 0, 0, 0, 0];
$propertyData = $monthlyPropertyData ?? $defaultMonthlyData;
$messageData = $monthlyMessageData ?? $defaultMonthlyData;
$statusData = $propertyStatusData ?? ['rent' => 0, 'buy' => 0];

// Convert to JSON
$propertyDataJson = json_encode($propertyData);
$messageDataJson = json_encode($messageData);
$statusDataJson = json_encode($statusData);
@endphp

<script>
  // Prepare data for charts
  var agentPropertyData = {!! $propertyDataJson !!};
  var agentMessageData = {!! $messageDataJson !!};
  var propertyStatusData = {!! $statusDataJson !!};


</script>

 <div class="page-content">


    @if($status === 'active')
    <h4>Agent Account Is <span class="text-success">Active </span> </h4>

    @else
 <h4>Agent Account Is <span class="text-danger">Inactive </span> </h4>
 <p class="text-danger"><b> Plz wait admin will check and approve your account</b></p>
    @endif


        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
          <div>
            <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
          </div>
          <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
              <span class="input-group-text input-group-addon bg-transparent border-primary" data-toggle><i data-feather="calendar" class="text-primary"></i></span>
              <input type="text" class="form-control bg-transparent border-primary" placeholder="Select date" data-input>
            </div>
            <button type="button" class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0" onclick="window.print();">
              <i class="btn-icon-prepend" data-feather="printer"></i>
              Print
            </button>
            <a href="{{ route('agent.dashboard.download') }}" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
              <i class="btn-icon-prepend" data-feather="download-cloud"></i>
              Download Report
            </a>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
              <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                      <h6 class="card-title mb-0">New Customers</h6>
                      <div class="dropdown mb-2">
                        <a type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-6 col-md-12 col-xl-5">
                        <h3 class="mb-2">{{ $propertyCount }}</h3>
                        <div class="d-flex align-items-baseline">
                          <p class="text-success">
                            <span>Properties</span>
                            <i data-feather="home" class="icon-sm mb-1"></i>
                          </p>
                        </div>
                      </div>
                      <div class="col-6 col-md-12 col-xl-7">
                        <div id="customersChart" class="mt-md-3 mt-xl-0"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                      <h6 class="card-title mb-0">New Orders</h6>
                      <div class="dropdown mb-2">
                        <a type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-6 col-md-12 col-xl-5">
                        <h3 class="mb-2">{{ $messageCount }}</h3>
                        <div class="d-flex align-items-baseline">
                          <p class="text-info">
                            <span>Messages</span>
                            <i data-feather="message-square" class="icon-sm mb-1"></i>
                          </p>
                        </div>
                      </div>
                      <div class="col-6 col-md-12 col-xl-7">
                        <div id="ordersChart" class="mt-md-3 mt-xl-0"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                      <h6 class="card-title mb-0">Growth</h6>
                      <div class="dropdown mb-2">
                        <a type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-6 col-md-12 col-xl-5">
                        <h3 class="mb-2">{{ $rentProperties + $buyProperties }}</h3>
                        <div class="d-flex align-items-baseline">
                          <p class="text-success">
                            <span>Rent: {{ $rentProperties }} | Buy: {{ $buyProperties }}</span>
                            <i data-feather="home" class="icon-sm mb-1"></i>
                          </p>
                        </div>
                      </div>
                      <div class="col-6 col-md-12 col-xl-7">
                        <div id="growthChart" class="mt-md-3 mt-xl-0"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- row -->




        <div class="row">
          <div class="col-lg-5 col-xl-4 grid-margin grid-margin-xl-0 stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                  <h6 class="card-title mb-0">Recent Messages</h6>
                  <div class="dropdown mb-2">
                    <a type="button" id="dropdownMenuButton6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton6">
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
                    </div>
                  </div>
                </div>
                <div class="d-flex flex-column">
                  @if(isset($recentMessages) && count($recentMessages) > 0)
                    @foreach($recentMessages as $message)
                    <a href="{{ route('agent.property.message') }}" class="d-flex align-items-center border-bottom pb-3">
                      <div class="me-3">
                        @if($message->user && $message->user->photo)
                          <img src="{{ url('upload/user_images/'.$message->user->photo) }}" class="rounded-circle wd-35" alt="user">
                        @else
                          <img src="{{ url('upload/no_image.jpg') }}" class="rounded-circle wd-35" alt="user">
                        @endif
                      </div>
                      <div class="w-100">
                        <div class="d-flex justify-content-between">
                          <h6 class="text-body mb-2">{{ $message->user ? $message->user->name : 'Unknown User' }}</h6>
                          <p class="text-muted tx-12">{{ $message->created_at->diffForHumans() }}</p>
                        </div>
                        <p class="text-muted tx-13">
                          @if($message->property)
                            Re: {{ $message->property->property_name }}
                          @endif
                        </p>
                      </div>
                    </a>
                    @endforeach
                  @else
                    <p class="text-center py-3">No recent messages</p>
                  @endif

                  <div class="text-center mt-3">
                    <a href="{{ route('agent.property.message') }}" class="btn btn-primary btn-sm">View All Messages</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-7 col-xl-8 stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                  <h6 class="card-title mb-0">Recent Properties</h6>
                  <div class="dropdown mb-2">
                    <a type="button" id="dropdownMenuButton7" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
                    </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover mb-0">
                    <thead>
                      <tr>
                        <th class="pt-0">#</th>
                        <th class="pt-0">Property Name</th>
                        <th class="pt-0">Type</th>
                        <th class="pt-0">Status</th>
                        <th class="pt-0">Price</th>
                        <th class="pt-0">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if(isset($recentProperties) && count($recentProperties) > 0)
                        @foreach($recentProperties as $key => $property)
                        <tr>
                          <td>{{ $key + 1 }}</td>
                          <td>
                            <a href="{{ route('agent.details.property', $property->id) }}">
                              {{ $property->property_name }}
                            </a>
                          </td>
                          <td>{{ $property->type ? $property->type->type_name : 'N/A' }}</td>
                          <td>
                            @if($property->status == 'approved')
                              <span class="badge bg-success">Approved</span>
                            @elseif($property->status == 'rejected')
                              <span class="badge bg-danger">Rejected</span>
                            @else
                              <span class="badge bg-warning">Pending</span>
                            @endif
                          </td>
                          <td>${{ number_format($property->lowest_price, 0) }}</td>
                          <td>
                            <a href="{{ route('agent.edit.property', $property->id) }}" class="btn btn-sm btn-primary">Edit</a>
                          </td>
                        </tr>
                        @endforeach
                      @else
                        <tr>
                          <td colspan="6" class="text-center">No properties found</td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- row -->

      </div>

@endsection