@extends('layouts.client')

@section('title', 'Add User')

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
                                <h5 class="card-title mb-0">
                                    <h5 class="mb-sm-0">Add User

                                        <a href="{{ route('employee.index') }}" style="float: right;"
                                            class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                            Back
                                        </a>
                                    </h5>

                                    <hr>



                                    <div class="live-preview">

                                        <form action="{{ route('employee.store') }}" method="POST">
                                            @csrf
                                            <div class="row gy-4">
                                                @include('company_client.employee.form')
                                            </div>
                                            <div class="card-footer mt-2">
                                                <div class="mb-3" style="float: right;">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-user float-right mb-3 mx-2">Save</button>
                                                    <button type="reset"
                                                        class="btn btn-primary float-right mr-3 mb-3 mx-2">Clear</button>
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
