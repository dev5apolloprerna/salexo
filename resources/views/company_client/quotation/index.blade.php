@extends('layouts.client')

@section('title', 'Quotation List')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <div class="main-content">
  <div class="page-content">
    <div class="container-fluid">

      {{-- Filters --}}
      <form method="POST" action="{{ route('quotation.index') }}" class="card mb-3">
        @csrf
        <div class="card-body">
          <div class="row filters align-items-end">
            <div class="col-md-3">
              <div class="form-group">
                <label class="mb-1">Company Name</label>
                <select
                  id="mappingCompany"
                  name="companyName"
                  class="form-control @error('companyName') is-invalid @enderror">
                  <option value="" disabled {{ empty($CompanyName) ? 'selected' : '' }}>Select Company</option>
                  @foreach ($Company as $company)
                    <option value="{{ $company->companyId }}" {{ $CompanyName == $company->companyId ? 'selected' : '' }}>
                      {{ $company->company_name }}
                    </option>
                  @endforeach
                </select>
                @error('companyName')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label class="mb-1">Party Name</label>
                <select
                  name="partyName"
                  id="filterParty"
                  class="form-control @error('partyName') is-invalid @enderror">
                  <option value="" disabled {{ empty($PartyName) ? 'selected' : '' }}>Select Party</option>
                  @foreach ($Party as $party)
                    <option value="{{ $party->partyId }}" {{ $PartyName == $party->partyId ? 'selected' : '' }}>
                      {{ $party->strPartyName }}
                    </option>
                  @endforeach
                </select>
                @error('partyName')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label class="mb-1">Product Name</label>
                <select
                  id="getproductID"
                  name="productName"
                  class="form-control @error('productName') is-invalid @enderror">
                  <option value="" disabled {{ empty($ProductName) ? 'selected' : '' }}>Select Product</option>
                  @foreach ($Product as $product)
                    <option value="{{ $product->productId }}" {{ $ProductName == $product->productId ? 'selected' : '' }}>
                      {{ $product->productName }}
                    </option>
                  @endforeach
                </select>
                @error('productName')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group d-flex gap-2">
                <button type="submit" id="search" name="search" value="Search" class="btn btn-primary mr-2">Search</button>
                <a href="{{ route('quotation.index') }}" class="btn btn-outline-secondary">Refresh</a>
              </div>
            </div>
          </div>

          {{-- Alert Messages --}}
          @include('common.alert')
        </div>
      </form>

<div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Quotation List</h6>
          <a href="{{ route('quotation.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Quotation
          </a>
        </div>



            {{-- Alert Messages --}}
            @include('common.alert')

        
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th >Company Name</th>
                                <th >Year</th>
                                <th >Date</th>
                                <th >Party Name</th>
                                <th >Quotation No</th>
                                <th >Quotation Validity</th>
                                <!--<th >Mode Of Dispatch</th>-->
                                <!--<th >Delivery Terms</th>-->
                                <!--<th >Payment Terms</th>-->
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($Quotation as $quotation)
                                <tr>
                                    <td>{{ $quotation->company_name }}</td>
                                    <td>{{ $quotation->strYear }}</td>
                                    <td>
                                        <?php $datestore = $quotation->entryDate;
                                        echo date('d-m-Y', strtotime($datestore)); ?>
                                    </td>
                                    <td>{{ $quotation->strPartyName }}</td>
                                    <td>{{ $quotation->iQuotationNo }}</td>
                                    <td>{{ $quotation->quotationValidity }}</td>
                                    <!--<td>{{ $quotation->modeOfDespatch }}</td>-->
                                    <!--<td>{{ $quotation->deliveryTerm }}</td>-->
                                    <!--<td>{{ $quotation->paymentTerms }}</td>-->

                                    <td style=" align-items: center;">
                                         <button class="btn btn-primary m-2 btn-sm" data-toggle="modal" data-target="#exampleModal" title="Edit"
                                            onclick="return editdata(<?= $quotation->quotationId ?>);">
                                            <i class="fa fa-pen"></i></button>

                                       <!--  <button class="btn btn-primary m-2" data-toggle="modal" data-target="#exampleModal" title="Edit"
                                            onclick="return editdata(<?= $quotation->quotationId ?>);">
                                            <i class="fa fa-pen"></i></button> -->

                                        <form action="{{ route('quotation.delete', $quotation->quotationId) }}" title="Delete"
                                            method="POST" onsubmit="return confirm('Are you Sure You wanted to Delete?');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-sm btn-danger"><i
                                                    class="fa fa-trash"></i></button>
                                        </form>

                                        <a href="{{ route('quotationdetails.index', $quotation->quotationId) }}" title="Add"
                                            class="btn btn-primary m-2 btn-sm"> <i class="fa fa-plus"></i></a>

                                        <a href="{{ route('quotation.showDetails', $quotation->quotationId) }}" title="View" target="blank"
                                            class="btn btn-primary m-2 btn-sm"> <i class="fa fa-eye text-white"> </i></a>

                                        <a href="{{ route('quotation.DetailPDF', $quotation->quotationId) }}"  class="btn btn-primary m-2 btn-sm" title="Download"> 
                                            <!--<i class="fa fa-file-pdf-o" target="blank" style="font-size:48px;color:red"></i> -->
                                            <i class="fa fa-file-pdf-o" ></i>
                                        </a>
                                                
                                                
                                                <a href="{{ route('quotation.copy', $quotation->quotationId) }}" title="Copy"
                                            class="btn btn-primary btn-sm m-2"> <i class="fa-solid fa-copy"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $Quotation->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

    @foreach ($Quotation as $quotation)
        {{-- SINGLE Edit Modal (place it ONCE, after the table) --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editLabel">Edit Quotation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="editForm" method="POST" action="#">
        @csrf
        @method('PUT') {{-- update should be PUT/PATCH --}}
        <div class="modal-body">
          <input type="hidden" name="quotationId" id="quotationId">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><span class="text-danger">*</span>Company Name</label>
                <select class="form-control" name="iCompanyId" id="EditcompanyID" required>
                  <option selected disabled value="">Select Company Name</option>
                  @foreach ($Company as $companydetail)
                    <option value="{{ $companydetail->company_id }}">{{ $companydetail->company_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label><span class="text-danger">*</span>Year</label>
                <select class="form-control" name="iYearId" id="EditiYearId" required>
                  <option selected disabled value="">Select Year</option>
                  @foreach ($Year as $year)
                    <option value="{{ $year->year_id }}">{{ $year->strYear }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label><span class="text-danger">*</span>Date</label>
                <input type="text" name="entryDate" class="form-control" id="EditentryDate" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label><span class="text-danger">*</span>Party</label>
                <select class="form-control" name="iPartyId" id="EditiPartyId" required>
                  <option selected disabled value="">Select Party</option>
                  @foreach ($Party as $party)
                    <option value="{{ $party->partyId }}">{{ $party->strPartyName }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label><span class="text-danger">*</span>Quotation No</label>
                <input type="text" name="iQuotationNo" class="form-control" id="EditiQuotationNo" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Quotation Validity</label>
                <input class="form-control" id="EditquotationValidity" name="quotationValidity" type="text" placeholder="Enter Quotation Validity">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Mode Of Dispatch</label>
                <input class="form-control" id="EditmodeOfDespatch" name="modeOfDespatch" type="text" placeholder="Enter Mode Of Dispatch">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Delivery Terms</label>
                <input class="form-control" id="EditdeliveryTerm" name="deliveryTerm" type="text" placeholder="Enter Delivery Terms">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Payment Terms</label>
                <input class="form-control" id="EditpaymentTerms" name="paymentTerms" type="text" placeholder="Enter Payment Terms">
              </div>
            </div>

            <div class="col-sm-6 mb-3">
              <label><span class="text-danger">*</span>GST Type</label>
              <select class="form-control" name="iGstType" id="iGstType" required>
                <option value="">Select GST Type</option>
                <option value="1">GST</option>
                <option value="2">IGST</option>
              </select>
            </div>

            <div class="col-sm-12 mb-3">
              <label>Terms & Condition</label>
              <textarea class="form-control" id="strTermsCondition" name="strTermsCondition" placeholder="Enter Terms and Condition"></textarea>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" id="save" class="btn btn-primary">Update</button>
        </div>
      </form>

    </div>
  </div>
</div>

    @endforeach
@endsection

@section('scripts')
    <!-- <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script> -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    
    <script>
        // CKEDITOR.replace('strTermsCondition');
        
  function editdata(id) {
    if (!id) return;

    // Build URLs
    var urlEdit   = "{{ route('quotation.edit', ':id') }}".replace(':id', id);
    var urlUpdate = "{{ route('quotation.update', ':id') }}".replace(':id', id);

    $.ajax({
      url: urlEdit,
      type: 'GET',
      dataType: 'json',                 // <-- tell jQuery to expect JSON
      success: function (obj) {         // <-- obj is already an object (NO JSON.parse)
        console.log('edit data:', obj);

        // Set form action and hidden id
        $('#editForm').attr('action', urlUpdate);
        $('#quotationId').val(id);

        // Fill fields
        $('#EditcompanyID').val(obj.iCompanyId).trigger('change');
        $('#EditiYearId').val(obj.iYearId);
        $('#EditiPartyId').val(obj.iPartyId);
        $('#EditiQuotationNo').val(obj.iQuotationNo || '');
        $('#EditquotationValidity').val(obj.quotationValidity || '');
        $('#EditmodeOfDespatch').val(obj.modeOfDespatch || '');
        $('#EditdeliveryTerm').val(obj.deliveryTerm || '');
        $('#EditpaymentTerms').val(obj.paymentTerms || '');
        $('#iGstType').val(obj.iGstType || '');

        // Datepicker: re-init safely
        var $date = $('#EditentryDate');
        if ($date.hasClass('hasDatepicker')) $date.datepicker('destroy');
        $date.val(obj.entryDate || '').datepicker({ dateFormat: 'dd-mm-yy', minDate: 0 });

        // CKEditor: guard for load/instance
        if (window.CKEDITOR) {
          if (!CKEDITOR.instances['strTermsCondition']) {
            CKEDITOR.replace('strTermsCondition');
          }
          CKEDITOR.instances['strTermsCondition'].setData(obj.strTermsCondition || '');
        } else {
          // fallback if CKEditor not loaded yet
          $('#strTermsCondition').val(obj.strTermsCondition || '');
        }

        // Show the single shared modal
        $('#editModal').modal('show');
      },
      error: function (xhr) {
        console.error(xhr.responseText || xhr.statusText);
        alert('Failed to load quotation. Check the network tab / controller.');
      }
    });
  }

    </script>
    
    <script>
        $(function() {
            $("#datepicker").datepicker({
                dateFormat: 'd-m-yy',
                minDate: 0,
            });
            //$("#datepicker").datepicker("setDate", new Date());

        });

        $(function() {
            $("#EditentryDate").datepicker({
                dateFormat: 'd-m-yy',
                minDate: 0
            });
        });
    </script>
    
    <script>
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
    </script>

<script>
        function validateQuotation() {
            var copy = $('#CopyQuotation').val();
            alert(copy);
            $.ajax({
                type: 'GET',
                url: "{{ route('quotationdetails.productfetch') }}",
                data: {
                    product: product,
                },
                success: function(data) {
                    //alert(data);
                    var obj = JSON.parse(data);
                    $('#fetchdescription').val(obj.productDescription);
                    //alert(obj.productDescription);
                }
            });

        }
    </script>
@endsection
