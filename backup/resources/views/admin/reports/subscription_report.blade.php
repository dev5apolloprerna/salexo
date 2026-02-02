@extends('layouts.app')
@section('title', 'Subscription Report List')
@section('content')

<?php $profileId = Request::segment(3);?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Subscription Report List
                                   <!--  <a href="{{ route('company-client.create') }}" style="float: right;" class="btn btn-sm btn-primary">
                                        <i class="far fa-plus"></i> Add New Client
                                    </a> -->

                                </h5>
                                
                            </div> 
                            <div class="card-body">
                            
                            <form method="POST" action="{{ route('reports.subscription') }}" id="myForm">
                                    @csrf
                                     <div class="row"> 
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="name">From Date </label>
                                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="name">To Date </label>
                                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate ?? '' }}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <input class="btn btn-primary" style="margin-top: 15%;" type="submit" value="{{'Search'}}">
                                            <input class="btn btn-primary" style="margin-top: 15%;" type="submit" onclick="myFunction()" value="{{'Reset'}}">

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div> 
                             <div class="row">
                                <div class="col-lg-12">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class=" table table-bordered table-striped table-hover datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Company</th>
                                                            <th>Contact Person</th>
                                                            <th>Mobile</th>
                                                            <th>Email</th>
                                                            <th>State</th>
                                                            <th>City</th>
                                                            <th>Plan</th>
                                                            <th>Subscription End Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($reportData as $company)
                                                            <tr>
                                                                <td>{{ $company->company_name }}</td>
                                                                <td>{{ $company->contact_person }}</td>
                                                                <td>{{ $company->mobile }}</td>
                                                                <td>{{ $company->email }}</td>
                                                                <td>{{ $company->state?->stateName ?? 'N/A' }}</td>
                                                                <td>{{ $company->city }}</td>
                                                                <td>{{ $company->plan->plan_name ?? 'N/A' }}</td>
                                                                <td>{{ date('d-m-Y',strtotime($company->subscription_end_date)) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                             <div class="d-flex justify-content-center mt-3">
                                                {{ $reportData->links() }}
                                            </div>
                                            </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endsection