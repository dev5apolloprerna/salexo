@extends('layouts.client')

@section('title', 'Edit Employee')

@section('content')


    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                

                 <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-0">Edit Employee
                           
                                <a href="{{ route('employee.index') }}" style="float: right;"
                                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    Back
                                </a>
                            <hr>
                            </h5>
                      
                                <div class="live-preview">

                                    <form action="{{ route('employee.update', $employee->emp_id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row gy-4">
                                            @include('company_client.employee.form', [
                                                'employee' => $employee,
                                            ])
                                        </div>
                                        <div class="card-footer mt-2">
                                            <div class="mb-3" style="float: right;">
                                                <button type="submit"
                                                    class="btn btn-primary btn-user float-right mb-3 mx-2">Update</button>
                                                <a class="btn btn-primary float-right mr-3 mb-3 mx-2"
                                                    href="{{ route('company-client.index') }}">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
