@extends('layouts.client')

@section('title', 'Add Quotation')

@section('content')
@php
    $company_id = Auth::guard('web_employees')->user()->company_id ?? '0';
@endphp
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
                            <h5 class="mb-sm-0">Add Quotation
                            
                            <a href="{{ route('quotation.index') }}" style="float: right;"
                                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    Back
                                </a>
                            </h5>
                          
                               <hr> 


        <div class="live-preview">

            <form method="POST" action="{{ route('quotation.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                     <input type="hidden" name="companyId" value="{{ $company_id }}" id="companyId">
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                          <label for="mappingParty"><span style="color:red;">*</span> Party Name</label>

                          @php
                            // If you want to preserve old selected value after validation error:
                            $oldPartyId   = old('iPartyId');
                            $oldPartyName = null;
                            if ($oldPartyId) {
                                $oldPartyName = optional(\App\Models\Party::find($oldPartyId))->strPartyName;
                            }
                          @endphp

                          <select
                            class="form-control form-control-user @error('iPartyId') is-invalid @enderror"
                            id="mappingParty"
                            name="iPartyId"
                            style="width: 100%;"
                            required
                          >
                            @if($oldPartyId && $oldPartyName)
                              {{-- Preload the selected option so Select2 shows it --}}
                              <option value="{{ $oldPartyId }}" selected>{{ $oldPartyName }}</option>
                            @endif
                          </select>

                          @error('iPartyId')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>



                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Year</label>
                            <select class="form-control form-control-user" @error('iYearId') is-invalid @enderror
                                name="iYearId" required>
                                <!--<option selected disabled value="">Select Year</option>-->
                                @foreach ($Year as $year)
                                    <option value="{{ $year->year_id }}"
                                        {{ old('iYearId') == $year->year_id ? 'selected' : '' }}>
                                        {{ $year->strYear }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Date</label>
                            <input type="date"
                                class="form-control form-control-user @error('entryDate') is-invalid @enderror"
                                id="datepicker" placeholder="Select Date" name="entryDate" value="{{ old('entryDate') }}"
                                required>
                        </div>

                       
                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Quotation No</label>
                            <input class="form-control" id="basic-form-name" name="iQuotationNo" type="text"
                                   placeholder="Enter Quotation No" value="{{ old('iQuotationNo') }}" readonly>

                        </div>

                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Quotation Validity</label>
                            <input class="form-control" id="basic-form-name" name="quotationValidity" type="text"
                                placeholder="Enter Quotation Validity" value="{{ old('quotationValidity') }}">
                        </div>

                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Mode Of Dispatch</label>
                            <input class="form-control" id="basic-form-name" name="modeOfDespatch" type="text"
                                placeholder="Enter Mode Of Dispatch" value="{{ old('modeOfDespatch') }}">
                        </div>

                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Delivery Terms</label>
                            <input class="form-control" id="basic-form-name" name="deliveryTerm" type="text"
                                placeholder="Enter Delivery Terms" value="{{ old('deliveryTerm') ? old('deliveryTerm') : $Company->delivery_terms }}">
                        </div>

                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Payment Terms</label>
                            <input class="form-control" id="basic-form-name" name="paymentTerms" type="text"
                                placeholder="Enter Payment Terms" value="{{ old('paymentTerms') ? old('paymentTerms') : $Company->payment_terms }}">
                        </div>

                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>GST Type</label>
                            <select class="form-control form-control-user" @error('iGstType') is-invalid @enderror
                                name="iGstType" required>
                                <option value="">Select GST Type</option>
                                <option value="1">GST</option>
                                <option value="2">IGST</option>
                            </select>
                        </div>
                        <div class="col-sm-12 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Terms & Condition</label>
                            <textarea class="form-control" id="fetchtermcondition" name="strTermsCondition"> {{ old('strTermsCondition') ? old('strTermsCondition') : $Company->terms_condition }} </textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-user float-right mb-3">Save</button>
                    <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('quotation.index') }}">Cancel</a>
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

@section('scripts')

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>


    <script>
$(function () {
    $('#mappingParty').select2({
      placeholder: 'Select Party Name',
      allowClear: true,
      width: '100%',
      minimumInputLength: 1, // start searching after 1 char
      ajax: {
        url: '{{ route('party.search') }}',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term || '',
            company_id: $('#mappingCompany').val() || '' // remove if you don’t filter by company
          };
        },
        processResults: function (data) {
          // expects { results: [{id, text}, ...] }
          return data;
        },
        cache: true
      }
    });

    // Optional: if company changes, clear the selected party so user re-picks in new scope
    $('#mappingCompany').on('change', function () {
      $('#mappingParty').val(null).trigger('change');
    });
  });

        CKEDITOR.replace('strTermsCondition');
        $(function() {
            $("#datepicker").datepicker({
                dateFormat: 'd-m-yy',
                // minDate: 0,
            });
            $("#datepicker").datepicker("setDate", new Date());

        });

        $(function() {
            $("#EditentryDate").datepicker({
                dateFormat: 'd-m-yy',
                // minDate: 0
            });
            // $("#EditentryDate").datepicker("setDate", new Date());
        });
    </script>
    
    <script>
        $('#mappingCompany').change(function() {
            mapping();
        });

        function mapping() {
            var company = $("#mappingCompany").val();

            //alert(company);
            var url = "{{ route('quotation.mapping', ':company') }}";
            url = url.replace(":company", company);
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    company: company,
                },
                success: function(data) {
                    // $('#mappingsubject_id').html(data);
                    //alert(data);
                    //$("#mappingsubject_id").multiselect('');
                    $("#mappingParty").html('');

                    //$("#mappingsubject_id").multiselect('');
                    $("#mappingParty").append(data);
                    //$("#mappingsubject_id").append('<option value="option5">Option</option>');
                    $("#mappingParty").multiselect('rebuild');
                }
            });
        }
    </script>
    
    <script>
        function termconditionFetch() {
            var fetchcompany = $('#mappingCompany').val();
            //alert(fetchcompany);
            $.ajax({
                type: 'GET',
                url: "{{ route('quotation.termconditionFetch') }}",
                data: {
                    fetchcompany: fetchcompany,
                },
                success: function(data) {
                    var obj = JSON.parse(data);
                    //alert(data);
                    var des="";
                    $.each(JSON.parse(data), function(i, item) {
                            console.log(item.description);
                                des+=item.description+"<br />";
                                    //CKEDITOR.instances['fetchtermcondition'].setData(item.description);
                                });
                        
                    CKEDITOR.instances['fetchtermcondition'].setData(des);
                    
                }
            });

        }
    </script>
    
    
    <script>
$(function () {
    const COMPANY_ID = Number($('#companyId').val() || 0);

    function setQuotationNo() {
      if (!COMPANY_ID) {
        $('input[name="iQuotationNo"]').val('');
        return;
      }
      $.ajax({
        url: "{{ route('quotation.getNextNo', ':companyId') }}".replace(':companyId', COMPANY_ID),
        type: 'GET',
        success: function (data) {
            console.log(data);
          $('input[name="iQuotationNo"]').val(data);
        },
        error: function (xhr) { console.error(xhr.responseText); }
      });
    }

    // Hydrate on load
    setQuotationNo();
  });

    </script>


@endsection
