@extends('layouts.client')
@section('title', 'Quotation List')
@section('content')

    <?php $profileId = Request::segment(3); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-0">Quotation List
                                    <a href="{{ route('quotation.create') }}" style="float: right;"
                                        class="btn btn-sm btn-primary">
                                        <i class="far fa-plus"></i> Add Quotation
                                    </a>

                                </h5>
                                <hr>
                            </div>
                            <div class="card-body">

                                <form method="get" action="{{ route('quotation.index') }}">

                                    <div class="row">
                                        <div class="col-md-3 ">
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
                                        <div class="col-md-3 ">
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
                                        <div class="col-md-3 ">
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

                                        <div class="col-md-3" style="padding-top:30px">
                                            <div class="form-group ">

                                                <input class="btn btn-primary" type="submit" value="{{ 'Search' }}">
                                                <a href="{{ route('quotation.index') }}" class="btn btn-secondary">Reset</a>

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
                                                        <th >Company Name</th>
                                                        <th >Year</th>
                                                        <th >Date</th>
                                                        <th >Party Name</th>
                                                        <th >Party Mobile No</th>
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
                                                        <td>{{ $quotation->iMobile }}</td>
                                                        <td>{{ $quotation->iQuotationNo }}</td>
                                                        <td>{{ $quotation->quotationValidity }}</td>
                                                        <!--<td>{{ $quotation->modeOfDespatch }}</td>-->
                                                        <!--<td>{{ $quotation->deliveryTerm }}</td>-->
                                                        <!--<td>{{ $quotation->paymentTerms }}</td>-->
                                                            <td style=" align-items: center;">
                                                              <a href="{{ route('quotation.edit', $quotation->quotationId) }}"><i
                                                                        class="fa fa-edit"></i></a>

                                                                      <a class="m-2" href="#" data-bs-toggle="modal"
                                                                        title="Delete" data-bs-target="#deleteRecordModal"
                                                                        onclick="deleteData(<?= $quotation->quotationId ?>);">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </a>
                                     
                                        <a href="{{ route('quotationdetails.index', $quotation->quotationId) }}" title="Add"
                                            class="m-2"> <i class="fa fa-plus"></i></a>

                                        <a href="{{ route('quotation.showDetails', $quotation->quotationId) }}" title="View" target="blank"
                                            class="m-2"> <i class="fa fa-eye"> </i></a>

                                        <a href="{{ route('quotation.DetailPDF', $quotation->quotationId) }}"  class="m-2" title="Download"> 
                                            <i class="fa fa-file-pdf" ></i>
                                        </a>
                                                
                                                
                                           @php
                                              // Phone must be in international format, without plus, e.g., 9198XXXXXXXX
                                              $phone = preg_replace('/\D/','', $quotation->iMobile ?? '');
                                              $pdfUrl = route('quotation.DetailPDF', $quotation->quotationId, true); // absolute URL
                                              $text   = urlencode("Hello! Here is your quotation PDF:\n{$pdfUrl}");
                                            @endphp

                                            <a href="https://wa.me/{{ $phone }}?text={{ $text }}"
                                               target="_blank"
                                               class="m-2"
                                               title="Share on WhatsApp">
                                              <i class="fab fa-whatsapp"></i>
                                            </a>
                                            <a href="{{ route('quotation.copy', $quotation->quotationId) }}" title="Copy"
                                            class="btn btn-primary btn-sm m-2"> <i class="fa-solid fa-copy"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                     {{ $Quotation->appends(request()->except('page'))->links() }}
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

    <!--Delete Modal -->
    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>Are you Sure ?</h4>
                            <p class="text-muted mx-4 mb-0">Are you Sure You want to Remove this Record
                                ?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <a class="btn btn-primary mx-2" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('user-delete-form').submit();">
                            Yes,
                            Delete It!
                        </a>
                        <button type="button" class="btn w-sm btn-primary mx-2" data-bs-dismiss="modal">Close</button>
                        <form action="{{ route('quotation.delete', $quotation->quotationId ?? '') }}" id="user-delete-form"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="emp_id" id="deleteid" value="">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Modal -->

   

@endsection

@section('scripts')
    <script>
        function deleteData(id) {
            $("#deleteid").val(id);
        }

        function editpassword(id) {
            $("#GetId").val(id);
        }
    </script>
@endsection
