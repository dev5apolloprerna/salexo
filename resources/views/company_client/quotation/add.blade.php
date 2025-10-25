@extends('layouts.client')

@section('title', 'Add Quotation')

@section('content')
<div class="main-content">
  <div class="page-content">
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add Quotation</h1>
        </div>

        {{-- Alert Messages --}}
        @include('common.alert')

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add Quotation</h6>
            </div>
            <form method="POST" action="{{ route('quotation.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                      <!--   <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Company Name</label>
                            <select class="form-control form-control-user" @error('iCompanyId') is-invalid @enderror id="mappingCompany" onchange="termconditionFetch();"
                                name="iCompanyId" required>
                                <option selected disabled value="">Select Company</option>
                                @foreach ($Company as $company)
                                    <option value="{{ $company->company_id }}"
                                        {{ old('iCompanyId') == $company->company_id ? 'selected' : '' }}>
                                        {{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div> -->

                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>Party Name</label>
                            <select class="form-control form-control-user" id="mappingParty" @error('iPartyId') is-invalid @enderror
                                name="iPartyId" required>
                                <option selected disabled value="">Select Party Name</option>
                                @foreach ($Party as $party)
                                    <option value="{{ $party->partyId }}"
                                        {{ old('iPartyId') == $party->partyId ? 'selected' : '' }}>
                                        {{ $party->strPartyName }}</option>
                                @endforeach
                            </select>
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
                            <input type="text"
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
                                placeholder="Enter Delivery Terms" value="{{ old('deliveryTerm') }}">
                        </div>

                        <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;"></span>Payment Terms</label>
                            <input class="form-control" id="basic-form-name" name="paymentTerms" type="text"
                                placeholder="Enter Payment Terms" value="{{ old('paymentTerms') }}">
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
                            <textarea class="form-control" id="fetchtermcondition" name="strTermsCondition"> </textarea>
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

@endsection

@section('scripts')

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
    <script>
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
        $('#mappingCompany').change(function () {
            var companyId = $(this).val();
            if (companyId) {
                $.ajax({
                    url: "{{ route('quotation.getNextNo', ':companyId') }}".replace(':companyId', companyId),
                    type: 'GET',
                    success: function (data) {
                        $('input[name="iQuotationNo"]').val(data);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                $('input[name="iQuotationNo"]').val(''); // Clear the input if no company is selected
            }
        });
    </script>


@endsection
