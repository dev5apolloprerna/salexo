<input type="hidden" name="iCustomerId" value="{{ auth('web_employees')->user()->company_id }}">
<input type="hidden" name="iemployeeId" value="{{ auth('web_employees')->user()->emp_id }}">

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-lg-4 col-md-6 mt-3">
        <label>Company Name <span style="color:red;"></span></label>
        <input type="text" name="company_name" class="form-control"
            value="{{ old('company_name', $lead->company_name ?? '') }}" placeholder="Enter Company Name"
            autocomplete="off">
    </div>
    <div class="col-lg-4 col-md-6 mt-3">
        <label>GST </label>
        <input type="text" name="GST_No" maxlength="15" id="gstInput" class="form-control"
            value="{{ old('GST_No', $lead->GST_No ?? '') }}" placeholder="Enter GST" autocomplete="off">
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-6 mt-3">
        <label>Contact Person Name <span style="color:red;">*</span></label>
        <input type="text" name="customer_name" class="form-control"
            value="{{ old('customer_name', $lead->customer_name ?? '') }}" maxlength="100"
            placeholder="Enter Contact Person Name" autocomplete="off" required>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-6 mt-3">
        <label>Mobile <span style="color:red;">*</span></label>
        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $lead->mobile ?? '') }}"
            onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" maxlength="10" required
            placeholder="Enter Mobile" autocomplete="off">
    </div>
    <div class="col-lg-4 col-md-6 mt-3">
        <label>Alternate Number </label>
        <input type="text" name="alternative_no" class="form-control"
            value="{{ old('alternative_no', $lead->alternative_no ?? '') }}"
            onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" maxlength="10"
            autocomplete="off" placeholder="Enter Alternate Number">
    </div>
    <div class="col-lg-4 col-md-6 mt-3">
        <label>Email </label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email ?? '') }}"
            maxlength="100" placeholder="Enter Email" autocomplete="off">
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6 mt-3">
        <label>Address <span style="color:red;"></span></label>
        <textarea name="address" class="form-control" rows="3" autocomplete="off">{{ old('address', $lead->address ?? '') }}</textarea>
    </div>
    <div class="col-lg-6 col-md-6 mt-3">
        <label>Remarks <span style="color:red;">*</span></label>
        <textarea name="remarks" class="form-control" rows="3" autocomplete="off" required>{{ old('remarks', $lead->remarks ?? '') }}</textarea>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-6 mt-3">
        <label>Service / Product <span style="color:red;">*</span></label>
        <select name="product_service_id" class="form-control" required>
            <option value="">-- Select Service / Product--</option>
            @foreach ($service as $id => $s)
                <option value="{{ $id }}"
                    {{ isset($lead) && $lead->product_service_id == $id ? 'selected' : '' }}>{{ $s }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-4 col-md-6 mt-3">
        <label>Lead Source <span style="color:red;">*</span></label>
        <select name="LeadSourceId" class="form-control" required>
            <option value="">-- Select Source --</option>
            @foreach ($leadSources as $id => $source)
                <option value="{{ $id }}"
                    {{ isset($lead) && $lead->LeadSourceId == $id ? 'selected' : '' }}>
                    {{ $source }}</option>
            @endforeach
        </select>
    </div>

</div>

@if (isset($lead) && $lead->lead_id)
@else
    <div class="row">
        <div class="col-lg-4 col-md-6 mt-3">
            <label>Initially Contacted ? <span style="color:red;">*</span></label>
            <select class="form-control" name="initially_contacted" id="initially_contacted" required>
                <option value=""> Select Initially Contacted ?</option>
                <option value="Yes" {{ isset($lead) && $lead->initially_contacted == 'Yes' ? 'selected' : '' }}>Yes
                </option>
                <option value="No" {{ isset($lead) && $lead->initially_contacted == 'No' ? 'selected' : '' }}>No
                </option>
            </select>
        </div>

        <div class="col-lg-4 col-md-6 mt-3" id="pipeline_statusDiv" style="display: none;">
            <label>Status <span style="color:red;">*</span></label>
            <select class="form-control" name="status" id="pipeline_status" required>
                <option value="">Select Status</option>
                @foreach ($leadPipeline as $pipeline)
                    <option value="{{ $pipeline->pipeline_id }}" data-followup="{{ $pipeline->followup_needed }}"
                        {{ isset($lead) && $lead->status == $pipeline->pipeline_id ? 'selected' : '' }}>
                        {{ $pipeline->pipeline_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-4 col-md-6 mt-3" id="cancelReasonBox" style="display: none;">
            <label>Cancel Reason List <span style="color:red;">*</span></label>
            <select class="form-control" name="cancel_reason_id" id="cancel_reason_id">
                <option value="">Select Cancel Reason List</option>
                @foreach ($leadCancelList as $list)
                    <option value="{{ $list->lead_cancel_reason_id }}"
                        {{ isset($lead) && $lead->cancel_reason_id == $list->lead_cancel_reason_id ? 'selected' : '' }}>
                        {{ $list->reason }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-4 col-md-6 mt-3" id="follow_up_dateBox" style="display: none;">
            Date <span class="text-danger">*</span>
            <input type="text" id="followup_datetime" name="followup_datetime" class="form-control"
                palaceholder="Select Date and Time"
                value="{{ old('followup_datetime', $lead->next_followup_date ?? '') }}">
        </div>

        <div class="col-lg-4 col-md-6 mt-3" id="amountBox" style="display: none;">
            Amount <span class="text-danger">*</span>
            <input type="text" class="form-control" name="amount" id="Amount"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');"
                placeholder="Enter Amount" maxlength="7" autocomplete="off"
                value="{{ old('amount', $lead->amount ?? '') }}">
        </div>

        <div class="col-lg-4 col-md-6 mt-3" id="commentDiv" style="display: none;">
            Comment <span class="text-danger">*</span>
            <textarea class="form-control" name="comment" id="comment" cols="50" rows="5">{{ old('comments', $lead->comments ?? '') }}</textarea>

            {{--  <input type="text" class="form-control" name="comment" id="comment" placeholder="Enter Comment"
        maxlength="100" autocomplete="off" required value="{{ old('comments', $lead->comments ?? '') }}">  --}}
        </div>
    </div>
@endif
