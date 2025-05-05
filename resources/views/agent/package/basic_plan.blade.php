@extends('agent.agent_dashboard')
@section('agent')

<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('buy.package') }}">Buy Package</a></li>
            <li class="breadcrumb-item active" aria-current="page">Basic Plan Details</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
             {{-- Add Form --}}
             <form method="post" action="{{ route('store.basic.plan') }}">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="container-fluid d-flex justify-content-between">
                        <div class="col-lg-3 ps-0">
                            <a href="#" class="noble-ui-logo logo-light d-block mt-3">Noble<span>UI</span></a>                 
                            <p class="mt-1 mb-1"><b>NobleUI Themes</b></p>
                            <p>108,<br> Great Russell St,<br>London, WC1B 3NA.</p>
                            <h5 class="mt-5 mb-2 text-muted">Agent Info :</h5>
                            {{-- Display agent data passed from controller --}}
                            <p>{{ $data->name ?? 'Agent Name' }},<br> {{ $data->email ?? 'agent@example.com' }}<br> {{ $data->address ?? 'Agent Address' }}</p>
                        </div>
                        <div class="col-lg-3 pe-0">
                            <h4 class="fw-bolder text-uppercase text-end mt-4 mb-2">Basic Plan</h4>
                            <h6 class="text-end mb-5 pb-4">Currently Active / Default Plan</h6>
                            <p class="text-end mb-1">Plan Cost</p>
                            <h4 class="text-end fw-normal">$ 0</h4>
                        </div>
                        </div>
                        <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                        <div class="table-responsive w-100">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Package Name </th>
                                    <th class="text-end">Property Allowance</th>
                                     {{-- Add Credits Column --}}
                                    <th class="text-end">Credits Added</th>
                                    <th class="text-end">Unit cost</th>
                                    <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr class="text-end">
                                    <td class="text-start">1</td>
                                    <td class="text-start">Basic</td>
                                    <td>1</td> {{-- Basic plan allows 1 property --}}
                                    <td>5</td> {{-- Basic plan adds 5 credits --}}
                                    <td>$0</td>
                                    <td>$0</td>
                                </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <div class="container-fluid mt-5 w-100">
                        <p class="text-muted">This is the default plan. Activating adds 5 credits to your account, allowing you to post up to 1 property. To post more properties, please upgrade to a Business or Professional plan.</p>
                        </div>
                        <div class="container-fluid w-100 mt-3">
                             {{-- Add Submit Button --}}
                            <button type="submit" class="btn btn-success float-end ms-2"><i data-feather="check-circle" class="me-2 icon-md"></i>Activate Plan & Add Credits</button>
                            <a href="{{ route('buy.package') }}" class="btn btn-primary float-end ms-2"><i data-feather="package" class="me-2 icon-md"></i>View Other Plans</a>
                            <a href="{{ route('agent.all.property') }}" class="btn btn-secondary float-end"><i data-feather="home" class="me-2 icon-md"></i>View My Properties</a>
                        </div>
                    </div>
                </div>
             </form>
        </div>
    </div>
</div>

@endsection 