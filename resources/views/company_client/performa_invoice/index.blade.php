@extends('layouts.client')
@section('title', 'Performa Invoice List')
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
                                <h5 class="card-title mb-0">Performa Invoice List
                                    <a href="{{ route('performainvoice.create') }}" style="float: right;"
                                        class="btn btn-sm btn-primary">
                                        <i class="far fa-plus"></i> Add Performa Invoice
                                    </a>

                                </h5>
                                <hr>
                            </div>
                            <div class="card-body">

                                <form method="GET" action="{{ route('performainvoice.index') }}">

                                  <div class="row">

                                    <div class="col-md-3">
                                      <label>Party</label>
                                      <select name="partyName" class="form-control">
                                        <option value="">All</option>
                                        @foreach($Party as $p)
                                          <option value="{{ $p->partyId }}" {{ $PartyName == $p->partyId ? 'selected' : '' }}>
                                            {{ $p->strPartyName }}
                                          </option>
                                        @endforeach
                                      </select>
                                    </div>

                                    <div class="col-md-3">
                                      <label>From Date</label>
                                      <input type="date" name="fromDate" class="form-control"
                                             value="{{ $fromDate ?? '' }}">
                                    </div>

                                    <div class="col-md-3">
                                      <label>To Date</label>
                                      <input type="date" name="toDate" class="form-control"
                                             value="{{ $toDate ?? '' }}">
                                    </div>

                                    <div class="col-md-3">
                                      <label>&nbsp;</label><br>
                                      <button class="btn btn-primary">Search</button>
                                      <a href="{{ route('performainvoice.index') }}" class="btn btn-secondary">Reset</a>
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
                                                        <th >Party Name</th>
                                                        <th >Party Mobile No</th>
                                                        <th >Year</th>
                                                        <th >Date</th>
                                                        <th >Performa Invoice No</th>
                                                        <!-- <th >Quotation Validity</th> -->
                                                        <!--<th >Mode Of Dispatch</th>-->
                                                        <!--<th >Delivery Terms</th>-->
                                                        <!--<th >Payment Terms</th>-->
                                                        <th >Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                   @foreach ($Invoice as $inv)
                                                    <tr>
                                                        <td>{{ $inv->strPartyName }}</td>
                                                        <td>{{ $inv->iMobile }}</td>
                                                        <td>{{ $inv->strYear }}</td>
                                                        <td>
                                                            <?php $datestore = $inv->entryDate;
                                                            echo date('d-m-Y', strtotime($datestore)); ?>
                                                        </td>
                                                        <td>{{ $inv->iPerformaInvoiceNo }}</td>
                                                        <!-- <td>{{ $inv->quotationValidity }}</td> -->
                                                        <!--<td>{{ $inv->modeOfDespatch }}</td>-->
                                                        <!--<td>{{ $inv->deliveryTerm }}</td>-->
                                                        <!--<td>{{ $inv->paymentTerms }}</td>-->
                                                            <td style=" align-items: center;">
                                                              <a href="{{ route('performainvoice.edit', $inv->performainvoiceId) }}"><i
                                                                        class="fa fa-edit"></i></a>

                                                                      <a class="m-2" href="#" data-bs-toggle="modal"
                                                                        title="Delete" data-bs-target="#deleteRecordModal"
                                                                        onclick="deleteData(<?= $inv->performainvoiceId ?>);">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </a>
                                     
                                        <a href="{{ route('performainvoicedetails.index', $inv->performainvoiceId) }}" title="Add"
                                            class="m-2"> <i class="fa fa-plus"></i></a>

                                        <a href="{{ route('performainvoice.showDetails', $inv->performainvoiceId) }}" title="View" target="blank"
                                            class="m-2"> <i class="fa fa-eye"> </i></a>

                                        <?php
                                            $product=App\Models\PerformaInvoiceDetail::where('performainvoiceId', $inv->performainvoiceId)->count();
                                        ?>
                                        @if(($product) > 0)
                                            <a href="{{ route('performainvoice.DetailPDF', $inv->performainvoiceId) }}" class="m-2" title="Download">
                                                <i class="fa fa-file-pdf"></i>
                                            </a>
                                        @else
                                            <a href="#" class="m-2" title="Download">
                                                <i class="fa fa-file-pdf"></i>
                                            </a>
                                        @endif
                                                
                                                
                                           @php
                                              // Phone must be in international format, without plus, e.g., 9198XXXXXXXX
                                              $phone = preg_replace('/\D/','', $inv->iMobile ?? '');
                                              $pdfUrl = route('performainvoice.DetailPDF', $inv->performainvoiceId, true); // absolute URL
                                              $text   = urlencode("Hello! Here is your invoice PDF:\n{$pdfUrl}");
                                            @endphp

                                                @if(($product) > 0)
                                            <a href="https://wa.me/{{ $phone }}?text={{ $text }}"
                                               target="_blank"
                                               class="m-2"
                                               title="Share on WhatsApp">
                                              <i class="fab fa-whatsapp"></i>
                                            </a>
                                            @else
                                            <a href="#"
                                               target="_blank"
                                               class="m-2"
                                               title="Share on WhatsApp">
                                              <i class="fab fa-whatsapp"></i>
                                            </a>
                                            
                                            @endif
                                            
                                            <a href="{{ route('performainvoice.copy', $inv->performainvoiceId) }}" title="Copy"
                                            class="btn btn-primary btn-sm m-2"> <i class="fa-solid fa-copy"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                     {{ $Invoice->appends(request()->except('page'))->links() }}
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
                        <form action="{{ route('performainvoice.delete', $inv->performainvoiceId ?? '') }}" id="user-delete-form"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="invoice_id" id="deleteid" value="">

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
