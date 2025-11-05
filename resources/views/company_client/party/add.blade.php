@extends('layouts.client')

@section('title', 'Add Party')

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
                            <h5 class="mb-sm-0">Add Party
                            
                            <a href="{{ route('party.index') }}" style="float: right;"
                                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    Back
                                </a>
                            </h5>
                          
                               <hr> 
                            
                      
                
                                <div class="live-preview">
                                  <div class="card-header card-header-min"><strong>Lead Autofill</strong></div>
                                  <div class="card-body">
                                    <div class="row g-2">
                                      <div class="col-md-6">
                                        <label class="form-label">Mobile to Check</label>
                                        <input type="text" class="form-control" id="lookup_mobile" placeholder="e.g. 9876543210">
                                        <small id="lead_fetch_status" class="text-muted"></small>
                                      </div>
                                      <div class="col-md-2" style="padding-top:30px">
                                            <div class="form-group ">
                                        <button type="button" class="btn btn-primary w-100" id="btn-fetch-lead">Fetch from Lead</button>


                                            </div>
                                        </div>

                                    </div>
                                  </div>
                          <div class="card-header card-header-min"><strong>Party Details</strong></div>
                              <div class="card-body">
                                  <form id="party-create-form" method="POST" action="{{ route('party.store') }}" autocomplete="off">
                                    @csrf
                                    <div class="row g-3 mb-2">
                                      <div class="col-md-4">
                                        <label class="form-label">Party Name</label>
                                        <input type="text" class="form-control" name="strPartyName" value="{{ old('strPartyName') }}" required>
                                      </div>
                                      <div class="col-md-4">
                                        <label class="form-label">GST</label>
                                        <input type="text" class="form-control" name="strGST" value="{{ old('strGST') }}">
                                      </div>
                                      <div class="col-md-4">
                                        <label class="form-label">Entry Date</label>
                                        <input type="date" class="form-control" name="strEntryDate" value="{{ old('strEntryDate', now()->format('Y-m-d')) }}" required>
                                      </div>
                                    </div>

                                    <div class="row g-3 mb-2">
                                      <div class="col-md-4">
                                        <label class="form-label">Mobile</label>
                                        <input type="text" class="form-control" name="iMobile" id="strContactNo" value="{{ old('iMobile') }}">
                                      </div>
                                      <div class="col-md-4">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="strEmail" value="{{ old('strEmail') }}">
                                      </div>
                                      <div class="col-md-4">
                                        <label class="form-label">Address 1</label>
                                        <input type="text" class="form-control" name="address1" value="{{ old('address1') }}">
                                      </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                      <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                                      </div>
                                      <div class="col-md-6">
                                        <label class="form-label">State</label>
                                        <select class="form-control" name="state_id" id="state_id">
                                          <option value="">Select State</option>
                                            @foreach($state as $s)
                                            <option value="{{$s->stateId}}" {{ old('state_id')== $s->stateId ? 'selected' :'' }}> {{ $s->stateName }} </option>
                                            @endforeach
                                        </select>
                                      </div>
                                    </div>
                                    <div class="card-footer mt-2">
                                            <div class="mb-3" style="float: right;">
                                                <button type="submit"
                                                    class="btn btn-primary btn-user float-right mb-3 mx-2">Save</button>
                                                <button type="reset"
                                                    class="btn btn-primary float-right mr-3 mb-3 mx-2">Clear</button>
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
    </div>
@endsection

@section('scripts')
<script>
(function () {
  const fetchBtn = document.getElementById('btn-fetch-lead');
  const mobileIn = document.getElementById('lookup_mobile');
  const statusEl = document.getElementById('lead_fetch_status');
  const LOOKUP_URL = "{{ route('party.lookup-by-mobile') }}";

  function setStatus(msg, type='muted'){ statusEl.className = type==='error'?'text-danger':(type==='success'?'text-success':'text-muted'); statusEl.textContent = msg||''; }
  function fillField(name, val){ const el=document.querySelector(`[name="${name}"]`)||document.getElementById(name); if(!el) return; el.value = val??''; el.dispatchEvent(new Event('input',{bubbles:true})); el.dispatchEvent(new Event('change',{bubbles:true})); }
  function applyPrefill(d){ Object.entries(d||{}).forEach(([k,v])=>fillField(k,v)); }

  async function doLookup(){
    setStatus('');
    let m=(mobileIn.value||'').replace(/\D+/g,'');
    if(m.length<6){ setStatus('Enter a valid mobile number.','error'); mobileIn.focus(); return; }
    try{
      setStatus('Checking lead…');
      const url = new URL(LOOKUP_URL, window.location.origin); url.searchParams.set('mobile', m);
      const res = await fetch(url.toString(), { headers:{'Accept':'application/json'} });
      const json = await res.json();
      if(!res.ok || !json.ok){ setStatus(json.message||'No lead found.','muted'); return; }
      applyPrefill(json.data); fillField('iMobile', m); setStatus('Auto-filled from lead.','success');
    }catch(e){ console.error(e); setStatus('Error while fetching lead.','error'); }
  }
  fetchBtn?.addEventListener('click', doLookup);
  mobileIn?.addEventListener('blur', function(){ const d=(this.value||'').replace(/\D+/g,''); if(d.length>=6) doLookup(); });
})();
</script>
@endsection