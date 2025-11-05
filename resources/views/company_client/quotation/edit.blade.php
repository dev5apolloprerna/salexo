@extends('layouts.client')

@section('title', 'Edit Quotation')

@section('content')
@php
    $company_id = Auth::guard('web_employees')->user()->company_id ?? '0';
@endphp

<div class="main-content">
  <div class="page-content">
    <div class="container-fluid">

      {{-- Alerts --}}
      @include('common.alert')

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-0">
                Edit Quotation
                <a href="{{ route('quotation.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" style="float:right;">
                  Back
                </a>
              </h5>
              <hr>

              <div class="live-preview">
                <form method="POST" action="{{ route('quotation.update', $Data->quotationId) }}" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  <div class="card-body">
                    <div class="form-group row">
                     <input type="hidden" name="companyId" value="{{ $company_id }}" id="companyId">

                      {{-- Company (locked to logged-in user’s company) --}}
                     <!--  <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="mappingCompany"><span style="color:red;">*</span> Company</label>
                        <select id="mappingCompany" name="iCompanyId" class="form-control form-control-user @error('iCompanyId') is-invalid @enderror" required>
                          @if($Company)
                            <option value="{{ $Company->company_id }}" selected>{{ $Company->company_name }}</option>
                          @else
                            <option value="" selected disabled>Company not found</option>
                          @endif
                        </select>
                        @error('iCompanyId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div> -->

                      {{-- Party (Select2 autosuggest or static) --}}
                      <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="mappingParty"><span style="color:red;">*</span> Party Name</label>
                        <select
                          class="form-control form-control-user @error('iPartyId') is-invalid @enderror"
                          id="mappingParty"
                          name="iPartyId"
                          required
                          style="width:100%;"
                        >
                          @php
                            $selectedPartyId = old('iPartyId', $Data->iPartyId);
                          @endphp
                          @foreach ($Party as $party)
                            <option value="{{ $party->partyId }}" @selected($selectedPartyId == $party->partyId)>{{ $party->strPartyName }}</option>
                          @endforeach
                        </select>
                        @error('iPartyId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>

                      {{-- Year --}}
                      <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="year"><span style="color:red;">*</span> Year</label>
                        <select class="form-control form-control-user @error('iYearId') is-invalid @enderror" id="year" name="iYearId" required>
                          @foreach ($Year as $year)
                            <option value="{{ $year->year_id }}" @selected(old('iYearId', $Data->iYearId) == $year->year_id)>{{ $year->strYear }}</option>
                          @endforeach
                        </select>
                        @error('iYearId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>

                      {{-- Date (d-m-Y UI) --}}
                      <?php $date = date('Y-m-d',strtotime($Data->entryDate)) ?>
                      <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="datepicker"><span style="color:red;">*</span> Date</label>
                        <input
                          type="date"
                          class="form-control form-control-user @error('entryDate') is-invalid @enderror"
                          id="datepicker"
                          placeholder="Select Date"
                          name="entryDate"
                          value="{{ old('entryDate', $date ?? '') }}"
                          required
                        >
                        @error('entryDate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>

                      {{-- Quotation No (readonly; auto-changes when company changes) --}}
                      <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="iQuotationNo"><span style="color:red;">*</span> Quotation No</label>
                        <input
                          class="form-control @error('iQuotationNo') is-invalid @enderror"
                          id="iQuotationNo"
                          name="iQuotationNo"
                          type="text"
                          placeholder="Enter Quotation No"
                          value="{{ old('iQuotationNo', $Data->iQuotationNo) }}"
                          readonly
                        >
                        @error('iQuotationNo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>

                      <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="quotationValidity">Quotation Validity</label>
                        <input class="form-control" id="quotationValidity" name="quotationValidity" type="text"
                               placeholder="Enter Quotation Validity" value="{{ old('quotationValidity', $Data->quotationValidity) }}">
                      </div>

                      <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="modeOfDespatch">Mode Of Dispatch</label>
                        <input class="form-control" id="modeOfDespatch" name="modeOfDespatch" type="text"
                               placeholder="Enter Mode Of Dispatch" value="{{ old('modeOfDespatch', $Data->modeOfDespatch) }}">
                      </div>

                      <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="deliveryTerm">Delivery Terms</label>
                        <input class="form-control" id="deliveryTerm" name="deliveryTerm" type="text"
                               placeholder="Enter Delivery Terms" value="{{ old('deliveryTerm', $Data->deliveryTerm) }}">
                      </div>

                      <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="paymentTerms">Payment Terms</label>
                        <input class="form-control" id="paymentTerms" name="paymentTerms" type="text"
                               placeholder="Enter Payment Terms" value="{{ old('paymentTerms', $Data->paymentTerms) }}">
                      </div>

                      {{-- GST Type --}}
                      <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="iGstType"><span style="color:red;">*</span> GST Type</label>
                        <select class="form-control form-control-user @error('iGstType') is-invalid @enderror" id="iGstType" name="iGstType" required>
                          <option value="">Select GST Type</option>
                          <option value="1" @selected(old('iGstType', $Data->iGstType) == 1)>GST</option>
                          <option value="2" @selected(old('iGstType', $Data->iGstType) == 2)>IGST</option>
                        </select>
                        @error('iGstType')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>

                      {{-- Terms & Conditions (CKEditor) --}}
                      <div class="col-sm-12 mb-3 mt-3 mb-sm-0">
                        <label for="fetchtermcondition">Terms & Condition</label>
                        <textarea class="form-control @error('strTermsCondition') is-invalid @enderror" id="fetchtermcondition" name="strTermsCondition">{{ old('strTermsCondition', $Data->strTermsCondition) }}</textarea>
                        @error('strTermsCondition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>

                    </div>
                  </div>

                  <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-user float-right mb-3">Save</button>
                    <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('quotation.index') }}">Cancel</a>
                  </div>
                </form>
              </div> {{-- live-preview --}}
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@section('scripts')
  {{-- jQuery UI datepicker --}}
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

  {{-- CKEditor --}}
  <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>

  {{-- Select2 (optional for Party autosuggest) --}}
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
    // CKEditor on the correct textarea id
    CKEDITOR.replace('fetchtermcondition');

    // Datepicker with d-m-yy
    $(function () {
      $("#datepicker").datepicker({ dateFormat: 'd-m-yy' });
      // If entryDate is empty, set today; otherwise keep existing value
      if (!$("#datepicker").val()) {
        $("#datepicker").datepicker("setDate", new Date());
      }
    });

    // Company -> get next quotation no
  /*  $('#mappingCompany').on('change', function () {
      var companyId = $(this).val();
      if (companyId) {
        $.ajax({
          url: "{{ route('quotation.getNextNo', ':companyId') }}".replace(':companyId', companyId),
          type: 'GET',
          success: function (data) {
            $('input[name="iQuotationNo"]').val(data);
          },
          error: function (xhr) { console.error(xhr.responseText); }
        });
      } else {
        $('input[name="iQuotationNo"]').val('');
      }
    });*/

 $('#EditcompanyID').change(function() {
            mapping();
        });

        function mapping() {
            var company = $("#EditcompanyID").val();

            var url = "{{ route('quotation.mapping', ':company') }}";
            url = url.replace(":company", company);
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    company: company,
                },
                success: function(data) {

                    $("#EditiPartyId").html('');

                    $("#EditiPartyId").append(data);

                    // $("#EditiPartyId").multiselect('rebuild');
                }
            });
        }
   
$(function () 
{
    const COMPANY_ID = Number($('#companyId').val() || 0);
    const QuotationNo = $('#iQuotationNo').val();

    function setQuotationNo() 
    {
      if (!COMPANY_ID) {
        $('input[name="iQuotationNo"]').val('');
        return;
      }
      if(!QuotationNo)
      {
      $.ajax({
        url: "{{ route('quotation.getNextNo', ':companyId') }}".replace(':companyId', COMPANY_ID),
        type: 'GET',
        success: function (data) {
          $('input[name="iQuotationNo"]').val(data);
        },
        error: function (xhr) { console.error(xhr.responseText); }
      });
        }
    }
  </script>
@endsection
