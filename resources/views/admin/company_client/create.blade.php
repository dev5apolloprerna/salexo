@extends('layouts.app')

@section('title', 'Add Company Client')

@section('content')


    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Add Company Client</h4>
                            <div class="page-title-right">
                                <a href="{{ route('company-client.index') }}"
                                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="live-preview">
                                    <form action="{{ route('company-client.store') }}" method="POST">
                                        @csrf
                                        <div class="row gy-4">
                                            @include('admin.company_client._form')
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
