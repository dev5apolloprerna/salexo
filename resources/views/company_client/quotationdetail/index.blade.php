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
                                <h5 class="card-title mb-0">Add Quotation Detail
                                    <a href="{{ route('quotation.index') }}"><button type="submit"
                        class="btn btn-success btn-user float-right mb-3">Back</button></a>

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
                                <select class="form-control form-control-user" id="getproductID" name="productID" onchange="productfetch();">
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
                                <span style="color:red;">*</span>Description</label>
                                <textarea style="width: 100%;" class="form-control" name="description" rows="7" id="fetchdescription" required></textarea>
                            </div>

                            <div class="col-sm-4 mb-3 mt-3 mb-sm-0">
                                <span style="color:red;">*</span>UOM / HSN</label>
                                <input class="form-control" id="HSN" name="uom" type="text"
                                    placeholder="Enter UOM" value="{{ old('uom') }}" required>
                            </div>

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


                            <!--<div class="col-sm-4 mb-3 mt-3 mb-sm-0">-->
                            <!--    <span style="color:red;">*</span>Amount</label>-->
                            <!--    <input class="form-control" id="Amount" name="amount" type="text"-->
                            <!--        placeholder="Enter Amount" value="" required readonly>-->
                            <!--</div>-->

                            <!--<div class="col-sm-4 mb-3 mt-3 mb-sm-0">-->
                            <!--    <span style="color:red;"></span>Discount</label>-->
                            <!--    <input class="form-control" id="Discount" name="discount" type="text"-->
                            <!--        onchange="AmountTotal();" placeholder="Enter discount" value="{{ old('discount') }}">-->
                            <!--</div>-->
                            <div class="col-sm-4 mb-3 mt-3 mb-sm-0">
                                <div class="form-group">
                                    <span style="color:red;">*</span>GST %</label>
                                    <input type="text" name="iGstPercentage" class="form-control" id="iGstPercentage"
                                        required>
                                </div>
                            </div>
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

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="15%">Sr No.</th>
                                <th width="15%">Product</th>
                                <th width="20%">Description</th>
                                <th width="15%">UOM</th>
                                <th width="15%">Quantity</th>
                                <th width="15%">Unit Rate</th>
                                <!--<th width="15%">Amount</th>-->
                                <!--<th width="15%">Discount</th>-->
                                <th width="15%">GST %</th>
                                <th width="15%">Net Amount</th>
                                <th width="10%">Action</th>
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
                                    <!--<td>{{ $detail->amount }}</td>-->
                                    <!--<td>{{ $detail->discount }}</td>-->
                                    <td>{{ $detail->iGstPercentage }}</td>
                                    <td>{{ $detail->netAmount }}</td>

                                    <td style="align-items: center;">
                                        <button class="m-2"  
                                                data-toggle="modal"
                                                data-target="#exampleModal"
                                                onclick="return editdata({{ $detail->quotationdetailsId }});">
                                          <i class="fa fa-pen"></i>
                                        </button>


                                        <form action="{{ route('quotationdetails.delete', $detail->quotationdetailsId) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you Sure You wanted to Delete?');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="mt-2"><i
                                                    class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php $i++; ?>


                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                 aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit Quotation</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>

                                  {{-- Keep your existing POST handler if that’s what your update uses --}}
                                  <form method="post" action="{{ route('quotationdetails.update', ['Id' => 0]) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('post')

                                    <div class="modal-body">
                                      <div class="row">
                                        <input type="hidden" name="quotationID" value="{{ $id }}">
                                        <input type="hidden" name="quotationdetailsId" id="quotationdetailsId" value="">

                                        <div class="col-md-12">
                                          <div class="form-group">
                                            <label><span class="text-danger">*</span> Product Name</label>
                                            <select class="form-control form-control-user" name="productID" id="EditproductID">
                                              <option selected disabled>Select Product Name</option>
                                              @foreach ($Product as $product)
                                                <option value="{{ $product->productId }}">{{ $product->productName }}</option>
                                              @endforeach
                                            </select>
                                          </div>
                                        </div>

                                        <div class="col-md-12">
                                          <div class="form-group">
                                            <label><span class="text-danger">*</span> Description</label>
                                            <textarea class="form-control" id="Editdescription" name="description" rows="7" required></textarea>
                                          </div>
                                        </div>

                                        <div class="col-md-6">
                                          <div class="form-group">
                                            <label><span class="text-danger">*</span> UOM</label>
                                            <input type="text" name="uom" class="form-control" id="Edituom" required>
                                          </div>
                                        </div>

                                        <div class="col-md-6">
                                          <div class="form-group">
                                            <label><span class="text-danger">*</span> Quantity</label>
                                            <input type="text" name="quantity" class="form-control" id="Editquantity" required>
                                          </div>
                                        </div>

                                        <div class="col-md-6">
                                          <div class="form-group">
                                            <label><span class="text-danger">*</span> Unit Rate</label>
                                            <input type="text" name="rate" class="form-control" id="Editrate" required>
                                          </div>
                                        </div>

                                        <div class="col-md-6">
                                          <div class="form-group">
                                            <label><span class="text-danger">*</span> GST %</label>
                                            <input type="text" name="iGstPercentage" class="form-control" id="EditIGstPercentage" required>
                                          </div>
                                        </div>

                                        <div class="col-md-6">
                                          <div class="form-group">
                                            <label><span class="text-danger">*</span> Net Amount</label>
                                            <input type="text" name="netAmount" class="form-control" id="EditnetAmount" required readonly>
                                          </div>
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


@endsection

@section('scripts')

    <script>
      function toggleOtherFields() {
    const val = $('#getproductID').val();
    const wrap = $('#other-product-fields');
    if (val === 'other') {
      wrap.show();
      $('#service_name').attr('required', true);
    } else {
      wrap.hide();
      $('#service_name').removeAttr('required');
    }
  }

  $(document).ready(function () {
    $('#getproductID').on('change', toggleOtherFields);
    toggleOtherFields();
  });

  function productfetch() {
    const product = $('#getproductID').val();

    $.ajax({
      type: 'GET',
      url: "{{ route('quotationdetails.productfetch') }}",
      data: { product },
      dataType: 'json',
      headers: { 'Accept': 'application/json' },
      success: function (obj) {
        $('#fetchdescription').val(obj.productDescription || '');
        $('#HSN').val(obj.HSN || '');
      },
      error: function (xhr, status, err) {
        let msg = 'Failed to fetch product';
        if (xhr.responseJSON?.message) {
          msg = xhr.responseJSON.message;
        } else if (xhr.responseText) {
          try {
            const parsed = JSON.parse(xhr.responseText);
            if (parsed?.message) msg = parsed.message;
          } catch {
            if (xhr.status) msg = `${xhr.status} ${xhr.statusText}`;
          }
        }
        console.error('Product fetch failed:', msg);
        $('#fetchdescription').val('');
      }
    });
  }

  function editdata(id) {
    const url = "{{ route('quotationdetails.edit', ':id') }}".replace(':id', id);
    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'json',
      headers: { 'Accept': 'application/json' },
      success: function (obj) {
        $('#EditproductID').val(obj.productID);
        $('#Editdescription').val(obj.description);
        $('#Edituom').val(obj.uom);
        $('#Editquantity').val(obj.quantity);
        $('#Editrate').val(obj.rate);
        $('#EditnetAmount').val(obj.netAmount);
        $('#EditIGstPercentage').val(obj.iGstPercentage);
        $('#quotationdetailsId').val(id);
        $('#exampleModal').modal('show');
      },
      error: function (xhr) {
        console.error('Edit fetch failed', xhr);
      }
    });
  }

  function AmountTotal() {
    const quantity = +$('#quantity').val() || 0;
    const rate = +$('#rate').val() || 0;
    $('#NetAmount').val(quantity * rate);
  }

  function EditAmountTotal() {
    const q = +$('#Editquantity').val() || 0;
    const r = +$('#Editrate').val() || 0;
    $('#EditnetAmount').val(q * r);
  }

    </script>


@endsection
