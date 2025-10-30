@extends('layouts.client')
@section('title', 'Edit Party')


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
                           
                                <a href="{{ route('party.index') }}" style="float: right;"
                                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    Back
                                </a>
                            <hr>
                            </h5>
                      
                          <div class="live-preview">
                            <form method="POST" action="{{ route('party.update', ['party' => ($party->partyId ?? $party->id)]) }}" autocomplete="off">
                              @csrf @method('PUT')
                              <div class="row g-3 mb-2">
                                <div class="col-md-4">
                                  <label class="form-label">Party Name</label>
                                  <input type="text" class="form-control" name="strPartyName" value="{{ old('strPartyName', $party->strPartyName) }}" required>
                                </div>
                                <div class="col-md-4">
                                  <label class="form-label">GST</label>
                                  <input type="text" class="form-control" name="strGST" value="{{ old('strGST', $party->strGST) }}">
                                </div>
                                <div class="col-md-4">
                                  <label class="form-label">Entry Date</label>
                                  <input type="date" class="form-control" name="strEntryDate"
                                         value="{{ old('strEntryDate', \Carbon\Carbon::parse($party->strEntryDate ?? now())->format('Y-m-d')) }}" required>
                                </div>
                              </div>

                              <div class="row g-3 mb-2">
                                <div class="col-md-4">
                                  <label class="form-label">Mobile</label>
                                  <input type="text" class="form-control" name="iMobile" value="{{ old('iMobile', $party->iMobile) }}">
                                </div>
                                <div class="col-md-4">
                                  <label class="form-label">Email</label>
                                  <input type="email" class="form-control" name="strEmail" value="{{ old('strEmail', $party->strEmail) }}">
                                </div>
                                <div class="col-md-4">
                                  <label class="form-label">Address 1</label>
                                  <input type="text" class="form-control" name="address1" value="{{ old('address1', $party->address1) }}">
                                </div>
                              </div>

                              <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                  <label class="form-label">Address 2</label>
                                  <input type="text" class="form-control" name="address2" value="{{ old('address2', $party->address2) }}">
                                </div>
                                <div class="col-md-6">
                                  <label class="form-label">Address 3</label>
                                  <input type="text" class="form-control" name="address3" value="{{ old('address3', $party->address3) }}">
                                </div>
                              </div>

                              <div class="card-footer mt-2">
                                  <div class="mb-3" style="float: right;">
                                      <button type="submit"
                                          class="btn btn-primary btn-user float-right mb-3 mx-2">Update</button>
                                      <a class="btn btn-primary float-right mr-3 mb-3 mx-2"
                                          href="{{ route('party.index') }}">Cancel</a>
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
