@extends('layouts.client')

@section('title', 'Quotation Detail List')

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
                            <h5 class="mb-sm-0">Add Quotation Detail
                            
                            <a href="{{ route('quotation.index') }}" style="float: right;"
                                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    Back
                                </a>
                            </h5>
                          
                               <hr> 
                               
                            </div>
    <div class="card-body">


            {{-- Alert Messages --}}
           
            <!-- DataTales Example -->
                    <div style="display: flex;
                justify-content: space-between;">
                        <h6 class="m-0 font-weight-bold text-primary">
                          Company Name : {{ $CompanyName->company_name ?? '-' }}
                        </h6>
                        <h6 class="m-0 font-weight-bold text-primary">
                          Party Name : {{ $CompanyName->strPartyName ?? '-' }}
                        </h6>
                    </div>
                    
                    <div style="display: flex;
                justify-content: space-between;margin-top:20px;">
                    <h6 class="m-0 font-weight-bold text-primary">
                      Year : {{ $CompanyName->strYear ?? '-' }}
                    </h6>
                    <h6 class="m-0 font-weight-bold text-primary">
                      Quotation No : {{ $CompanyName->iQuotationNo ?? '-' }}
                    </h6>
                    <h6 class="m-0 font-weight-bold text-primary">
                      Date :
                      @if(!empty($CompanyName?->entryDate))
                        {{ \Carbon\Carbon::parse($CompanyName->entryDate)->format('d-m-Y') }}
                      @else
                        -
                      @endif
                    </h6>
                        </div>
                </div>
                <form method="POST" action="{{ route('quotationdetails.create') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">

                            <input type="hidden" name="quotationID" value={{ $id }}>
                            
                            <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                                <span style="color:red;"></span>Product Name / Service Name</label>
                                <select class="form-control form-control-user js-service" id="getproductID" name="productID">
                                  <option selected disabled>Select Product Name</option>
                                  <option value="other">Other Product</option>
                                  @foreach ($Product as $product)
                                    <option value="{{ $product->service_id }}"
                                      {{ old('productID') == $product->service_id ? 'selected' : '' }}>
                                      {{ $product->service_name }}
                                    </option>
                                  @endforeach
                                </select>

                            </div>
                            {{-- Shown only when Product = "other" --}}
                            <div id="other-product-fields" class="col-sm-6 mb-3 mt-3 mb-sm-0" style="display:none;">
                                        <span style="color:red;"></span> Service Name / Product Name</label>
                                        <input type="text" class="form-control" name="service_name" id="service_name" placeholder="Enter new service name">
                            </div>


                            <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                                Description</label>
                                <textarea style="width: 100%;" class="form-control" name="description" rows="7" id="fetchdescription"></textarea>
                            </div>

                            @if($CompanyName->GST != null)
                            <div class="col-sm-4 mb-3 mt-3 mb-sm-0">
                                <span style="color:red;">*</span>UOM / HSN</label>
                                <input class="form-control" id="HSN" name="uom" type="text"
                                    placeholder="Enter UOM" value="{{ old('uom') }}" required>
                            </div>
                            @endif
                            <div class="col-sm-4 mb-3 mt-3 mb-sm-0">
                                <span style="color:red;">*</span>Quantity</label>
                                <input class="form-control" id="quantity" name="quantity" type="text"
                                    placeholder="Enter Quantity" value="{{ old('quantity') }}" onchange="AmountTotal();"
                                    required>
                            </div>

                            <div class="col-sm-4 mb-3 mt-3 mb-sm-0">
                                <span style="color:red;">*</span>Unit Rate</label>
                                <input class="form-control" id="rate" name="rate" type="text"
                                    placeholder="Enter Unit Rate" value="{{ old('rate') }}" onchange="AmountTotal();"
                                    required>
                            </div>
                            @if($CompanyName->GST != null)

                            <div class="col-sm-4 mb-3 mt-3 mb-sm-0">
                                <div class="form-group">
                                    <span style="color:red;">*</span>GST %</label>
                                    <input type="text" name="iGstPercentage" class="form-control" id="iGstPercentage"
                                        required>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-4 mb-3 mt-3 mb-sm-0">
                                <span style="color:red;">*</span>Net Amount</label>
                                <input class="form-control" id="NetAmount" name="netAmount" type="text"
                                    placeholder="Enter Net Amount" value="" required readonly>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-user float-right mb-3">Save</button>
                        <a class="btn btn-primary float-right mr-3 mb-3"
                            href="{{ route('quotationdetails.index',$id) }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
</div>
        <!-- DataTales Example -->
         <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-0">Quotation Details List
                                </h5>
                                <hr>
                            </div>
                            <div class="card-body">
                             <h6 >Apply Discount to This Quotation</h6>

                                <form action="{{ route('quotationdetails.applyDiscount', $id) }}" method="POST" class="row  mb-3 g-3" id="discountForm">
                                  @csrf
                                  <div class="col-sm-3">
                                    <label class="form-label"><span class="text-danger">*</span> Mode</label>
                                    <select name="mode" id="discountMode" class="form-control" required>
                                      <option value="percent">Percentage (%)</option>
                                      <option value="amount">Flat Amount (₹)</option>
                                    </select>
                                  </div>
                                  <div class="col-sm-3">
                                    <label class="form-label"><span class="text-danger">*</span> Value</label>
                                    <div class="input-group">
                                      <span class="input-group-text d-none" id="rupeePrefix"><span class="rupee"></span></span>
                                      <input type="number" step="0.01" min="0" name="value" id="discountValue" class="form-control" placeholder="Enter %" required>
                                    </div>
                                  </div>
                                  <div class="col-sm-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success">Apply Discount</button>
                                    @isset($summary)
                                      <span class="ms-3 small {{ ($summary['gst_uniform'] ?? false) ? 'text-success' : 'text-warning' }}">
                                        GST {{ ($summary['gst_uniform'] ?? false) ? 'uniform across items' : 'varies across items' }}
                                      </span>
                                    @endisset
                                  </div>
                                </form>

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                             {{-- Current totals (optional, from $summary) --}}
                                @isset($summary)
                                  <div class="row mb-3 small text-muted">
                                    <div class="col-3 col-md-3">Sub Total: <strong><span class="rupee"></span>{{ number_format($summary['sub_total'] ?? 0, 2) }}</strong></div>
                                    <div class="col-3 col-md-3">Total Discount: <strong><span class="rupee"></span>{{ number_format($summary['total_discount'] ?? 0, 2) }}</strong></div>
                                    <div class="col-3 col-md-3">Taxable After Discount: <strong><span class="rupee"></span>{{ number_format($summary['taxable_after_discount'] ?? 0, 2) }}</strong></div>
                                    <div class="col-3 col-md-3">GST Total: <strong><span class="rupee"></span>{{ number_format($summary['gst_total'] ?? 0, 2) }}</strong></div>
                                    <div class="col-3 col-md-3">Grand Total: <strong><span class="rupee"></span>{{ number_format($summary['grand_total'] ?? 0, 2) }}</strong></div>
                                  </div>
                                @endisset
                            <tr>
                                <th >Sr No.</th>
                                <th >Product</th>
                                <th >Description</th>
                                <th >UOM</th>
                                <th >Quantity</th>
                                <th >Unit Rate</th>
                                <th >Amount</th>
                                <th >Discount</th>
                                <th >GST %</th>
                                <th >Net Amount</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach ($QuotationDetail as $detail)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $detail->productName }}</td>
                                    <td>{{ $detail->description }}</td>
                                    <td>{{ $detail->uom }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ $detail->rate }}</td>
                                    <td>{{ $detail->amount }}</td>
                                    <td>{{ $detail->discount }}</td>
                                    <td>{{ $detail->iGstPercentage }}</td>
                                    <td>{{ $detail->totalAmount }}</td>

                                    <td style="align-items: center;">
                                        <a class="m-2"  
                                                data-toggle="modal"
                                                data-target="#editQuotationDetailModal"
                                                onclick="return editdata({{ $detail->quotationdetailsId }});">
                                          <i class="fa fa-edit"></i>
                                        </a>
                                            <a class="m-2" href="#" data-bs-toggle="modal"
                                                    title="Delete" data-bs-target="#deleteRecordModal"
                                                    onclick="deleteData(<?= $detail->quotationdetailsId ?>);">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a>


                                       <!--  <form action="{{ route('quotationdetails.delete', $detail->quotationdetailsId) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you Sure You wanted to Delete?');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="mt-2"><i
                                                    class="fa fa-trash"></i></button>
                                        </form> -->
                                    </td>
                                </tr>
                                <?php $i++; ?>


                                <div class="modal fade" id="editQuotationDetailModal" tabindex="-1" role="dialog" aria-labelledby="editQDLabel" aria-hidden="true">
                                  <div class="modal-dialog modal-lg" role="document"><!-- bigger -->
                                    <div class="modal-content">

                                      <div class="modal-header">
                                        <h5 class="modal-title" id="editQDLabel">Edit Quotation Detail</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>

                                      <form id="editQDForm" method="post" action="{{ route('quotationdetails.update', ['Id' => 0]) }}">
                                        @csrf
                                        @method('post')
                                        <input type="hidden" name="quotationID" value="{{ $id }}">
                                        <input type="hidden" name="quotationdetailsId" id="quotationdetailsId" value="">

                                        <div class="modal-body">
                                          <div class="row">
                                            <div class="col-md-12 mb-3">
                                              <label><span class="text-danger">*</span> Product Name</label>
                                              <select class="form-control form-control-user js-service" name="productID" id="EditproductID" required>
                                                <option selected disabled value="">Select Product Name</option>
                                                @foreach ($Product as $product)
                                                  <option value="{{ $product->service_id ?? $product->productId }}">
                                                    {{ $product->service_name ?? $product->productName }}
                                                  </option>
                                                @endforeach
                                              </select>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                              <label><span class="text-danger">*</span> Description</label>
                                              <textarea class="form-control" id="Editdescription" name="description" rows="6" required></textarea>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                              <label><span class="text-danger">*</span> UOM</label>
                                              <input type="text" name="uom" class="form-control" id="Edituom" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                              <label><span class="text-danger">*</span> Quantity</label>
                                              <input type="number" step="any" name="quantity" class="form-control" id="Editquantity" required oninput="EditAmountTotal()">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                              <label><span class="text-danger">*</span> Unit Rate</label>
                                              <input type="number" step="any" name="rate" class="form-control" id="Editrate" required oninput="EditAmountTotal()">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                              <label><span class="text-danger">*</span> GST %</label>
                                              <input type="number" step="any" name="iGstPercentage" class="form-control" id="EditIGstPercentage" required>
                                            </div>

                                            <div class="col-md-6 mb-1">
                                              <label><span class="text-danger">*</span> Net Amount</label>
                                              <input type="number" step="any" name="netAmount" class="form-control" id="EditnetAmount" required readonly>
                                              <small class="text-muted">Auto = Quantity × Unit Rate</small>
                                            </div>
                                          </div>
                                        </div>

                                        <div class="modal-footer">
                                          <button type="submit" id="save" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                      </form>

                                    </div>
                                  </div>
                                </div>


    
                            @endforeach
                        </tbody>
                    </table>
                    {{ $QuotationDetail->links() }}
               </div>
           </div>
           </div>
       </div>
   </div>
</div>
</div>
</div>


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
                        <form action="{{ route('quotationdetails.delete', $detail->quotationdetailsId ?? '') }}" id="user-delete-form"
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

@endsection
@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<style>
  .select2-container { width: 100% !important; }
  .select2-container .select2-dropdown { z-index: 2055; } /* above BS5 modal */
</style>

<script>
 function deleteData(id) {
        $("#deleteid").val(id);
    }

(function () {
  /* ================= helpers ================= */

  // Safely show/hide the "other" block if it exists
  function showOtherBlock(show, presetText) {
    const $wrap = $('#other-product-fields');
    const $inp  = $('#service_name');
    if (!$wrap.length) return; // if you removed that block entirely
    if (show) {
      $wrap.show();
      $inp.attr('required', true).val(presetText || '');
    } else {
      $wrap.hide();
      $inp.removeAttr('required').val('');
    }
  }

  // Fill description + HSN/UOM from payload (service)
  function applyServicePayload($scope, svc) {
    if (!svc) return;
    // Your API returns service_description + HSN
    const desc = svc.service_description || svc.description || '';
    const hsn  = svc.HSN || '';

    if ($scope.attr('id') === 'getproductID') {
      if (desc) $('#fetchdescription').val(desc);
      if (hsn)  $('#HSN').val(hsn);
    } else if ($scope.attr('id') === 'EditproductID') {
      if (desc) $('#Editdescription').val(desc);
      if (hsn)  $('#Edituom').val(hsn); // map HSN into your Edit UOM field (as per your UI)
    }
  }

  // Build a Select2 config (AJAX + tags) with correct dropdownParent
  function buildSelect2Config(dropdownParentEl) {
    return {
      width: '100%',
      placeholder: 'Type to search service...',
      allowClear: true,
      tags: true, // allow creating new services
      minimumInputLength: 1,
      dropdownParent: $(dropdownParentEl || document.body),
      ajax: {
        delay: 250,
        url: "{{ route('quotationdetails.services.lookup') }}",
        dataType: 'json',
        data: params => ({ q: params.term || '' }),
        processResults: data => ({ results: (data && data.results) ? data.results : [] })
      },
      createTag: params => {
        const term = (params.term || '').trim();
        if (!term) return null;
        return { id: '__new__:' + term, text: term, isNew: true };
      },
      templateResult: item => {
        if (item.loading) return item.text;
        if (item.isNew)   return `➕ Add: ${item.text}`;
        // Show HSN inline when available
        return item.HSN ? `${item.text} (HSN: ${item.HSN})` : item.text;
      }
    };
  }

  // Destroy (if any) and init Select2; wire up select handler
  function ensureInitSelect2($el, dropdownParentEl) {
    if (!$el.length) return;

    try { if ($el.data('select2')) $el.select2('destroy'); } catch (_) {}

    $el.select2(buildSelect2Config(dropdownParentEl));

    // When a service is picked/typed
    $el.off('select2:select.__svc').on('select2:select.__svc', function (e) {
      const data = e.params.data;
      if (data && !data.isNew && data.id !== '__new__') {
        applyServicePayload($el, data);
        showOtherBlock(false);
      } else {
        // a brand-new value typed
        showOtherBlock(true, data.text || '');
        if ($el.attr('id') === 'getproductID') {
          $('#fetchdescription').val('');
          $('#HSN').val('');
        } else {
          $('#Editdescription').val('');
          $('#Edituom').val('');
        }
      }
    });
  }

  // Inject a one-off option and select it
  function preselectSelect2($el, id, text) {
    if (!id) return;
    if (!$el.find('option[value="' + id + '"]').length) {
      $el.append(new Option(text || 'Selected', id, true, true));
    }
    $el.val(id).trigger('change');
  }

  /* ================ bootstrapping ================ */

  // 1) Add form: init Select2 once (outside modal)
  $(document).ready(function () {
    ensureInitSelect2($('#getproductID'), document.body);
  });

  // 2) Edit modal: init Select2 when modal becomes visible (BS5 event)
  const $modal = $('#editQuotationDetailModal');

  $modal.on('shown.bs.modal', function () {
    ensureInitSelect2($('#EditproductID'), this);
    // If we queued a preselect before modal was shown, apply it now
    if (window.__pendingEditSvc) {
      const { id, text } = window.__pendingEditSvc;
      preselectSelect2($('#EditproductID'), id, text);
      window.__pendingEditSvc = undefined;
    }
  });

  // Clean up when hidden (optional)
  $modal.on('hidden.bs.modal', function () {
    try { $('#EditproductID').select2('destroy'); } catch (_) {}
  });

  /* ================ edit flow ================ */

  // Called by your action button in the table
  window.editdata = function (id) {
    const url = "{{ route('quotationdetails.edit', ':id') }}".replace(':id', id);

    $.ajax({
      url,
      type: 'GET',
      dataType: 'json',
      headers: { 'Accept': 'application/json' },
      success: function (obj) {
        // Fill non-select fields
        $('#Editdescription').val(obj.service_description || obj.description || '');
        $('#Edituom').val(obj.uom || '');
        $('#Editquantity').val(obj.quantity || '');
        $('#Editrate').val(obj.rate || '');
        $('#EditnetAmount').val(obj.netAmount || '');
        $('#EditIGstPercentage').val(obj.iGstPercentage || '');
        $('#quotationdetailsId').val(id);

        // Open modal first (so dropdownParent is correct)
        $modal.modal('show');

        // Prepare preselection (do it after select2 is ready)
        const svcId   = obj.productID;
        const svcText = obj.productName || obj.service_name || obj.product_name || 'Selected Service';

        // If select2 is already initialized (modal visible), preselect immediately,
        // else queue it to run on 'shown.bs.modal'
        const $sel = $('#EditproductID');
        if ($sel.data('select2')) {
          preselectSelect2($sel, svcId, svcText);
        } else {
          window.__pendingEditSvc = { id: svcId, text: svcText };
        }
      },
      error: function (xhr) {
        console.error('Edit fetch failed', xhr);
        alert('Failed to load the selected item.');
      }
    });
  };

  /* ================ amounts (unchanged) ================ */
  window.AmountTotal = function () {
    const quantity = +$('#quantity').val() || 0;
    const rate     = +$('#rate').val() || 0;
    $('#NetAmount').val(quantity * rate);
  };
  window.EditAmountTotal = function () {
    const q = +$('#Editquantity').val() || 0;
    const r = +$('#Editrate').val() || 0;
    $('#EditnetAmount').val(q * r);
  };

})();

</script>
<style>
  .rupee:before { content: "₹"; } /* works in normal HTML; for PDF use DejaVu Sans and \20B9 if needed */
</style>

<script>
  (function () {
    const modeSel = document.getElementById('discountMode');
    const valInp  = document.getElementById('discountValue');
    const rupee   = document.getElementById('rupeePrefix');

    function syncPlaceholder() {
      if (!modeSel || !valInp || !rupee) return;
      if (modeSel.value === 'amount') {
        valInp.placeholder = 'Enter amount';
        rupee.classList.remove('d-none');
      } else {
        valInp.placeholder = 'Enter %';
        rupee.classList.add('d-none');
      }
    }

    if (modeSel) {
      modeSel.addEventListener('change', syncPlaceholder);
      syncPlaceholder();
    }
  })();
</script>

@endsection
