@extends('layouts.front')
@section('title', 'Home')
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <body class="register-page">
        <!-- Page hero -->
        <header class="page-hero">
            <div class="container">
                <span class="kicker">Create Account</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Registration</h1>
                <p class="text-secondary mb-0">Fill your company details.</p>
            </div>
        </header>

        <!-- Registration content -->
        <main class="register-wrap">
            <div class="container">

                {{--  <form id="regForm" method="POST" action="{{ route('front.registration_store') }}">  --}}
                <form id="regForm">
                    @csrf
                    <div class="row g-4">
                        <!-- Left: Form (col-8) -->
                        <div class="col-lg-8">
                            <div class="register-card">
                                <h2 class="h4 mb-3">Add Company Client</h2>

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_name"
                                            placeholder="Enter company name" required value="{{ old('company_name') }}"
                                            autocomplete="off" autofocus>
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">GST</label>
                                        <input type="text" class="form-control" name="gst"
                                            placeholder="Enter GST number" value="{{ old('gst') }}" autocomplete="off">
                                        @error('gst')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Row 2 -->
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="contact_person_name"
                                            placeholder="Enter contact person name" required
                                            value="{{ old('contact_person_name') }}" autocomplete="off">
                                        @error('contact_person_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mobile"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            maxlength="10" minlength="10" placeholder="Enter mobile number" required
                                            value="{{ old('mobile') }}" autocomplete="off">
                                        @error('mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Row 3 (Email only on left) -->
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email"
                                            placeholder="Enter email address" required value="{{ old('email') }}"
                                            autocomplete="off">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 d-none d-md-block">
                                    </div>

                                    <!-- Row 4 -->
                                    <div class="col-md-6">
                                        <label class="form-label">Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="address" rows="2" placeholder="Enter address" required>{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Pincode <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="pincode"
                                            placeholder="Enter pincode" required value="{{ old('pincode') }}"
                                            autocomplete="off">
                                        @error('pincode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Row 5 -->
                                    <div class="col-md-6">
                                        <label class="form-label">City <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="city"
                                            placeholder="Enter city name" required value="{{ old('city') }}"
                                            autocomplete="off">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">State <span class="text-danger">*</span></label>
                                        <select class="form-select" name="state_id" required>
                                            <option value="">Select State</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->stateId }}"
                                                    {{ old('state_id') == $state->stateId ? 'selected' : '' }}>
                                                    {{ $state->stateName }}</option>
                                            @endforeach
                                        </select>
                                        @error('state_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="register-actions d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg">Go To Payment</button>
                                </div>

                                <!-- Success (demo) -->
                                <div id="regSuccess" class="alert alert-success d-none mt-3 mb-0" role="alert">
                                    Registration submitted! (Demo message — connect to backend to save)
                                </div>
                            </div>
                        </div>


                        <!-- Right: Plan & Billing (col-4) -->
                        <div class="col-lg-4">
                            <div class="plan-card">
                                <h5 class="mb-3">Plan &amp; Billing</h5>

                                <div class="mb-2 d-flex align-items-baseline justify-content-between">
                                    <label class="form-label fs-5 mb-0">Plan Name</label>
                                    <span class="fw-semibold" id="planNameText">{{ $plan }}</span>
                                    <!-- keep plan_name posting to backend -->
                                    <input type="hidden" name="plan_name" id="planName" value="{{ $plan }}">
                                </div>

                                <!-- Duration in days only (unit removed) -->
                                <div class="mb-2 d-flex align-items-center justify-content-between">
                                    <label class="form-label">Duration (days)</label>
                                    <span class="fw-semibold" id="amountValue">{{ $days }} days</span>
                                    <input type="hidden" name="duration_in_days" id="days"
                                        value="{{ $days }}">
                                </div>

                                <!-- Amount: label + value on same line -->
                                <div class="mb-2 d-flex align-items-center justify-content-between">
                                    <span class="form-label mb-0">Amount</span>
                                    <span class="fw-semibold" id="amountValue">₹{{ $amount }}</span>
                                    <input type="hidden" name="amount" id="planAmount" value="{{ $amount }}">
                                </div>

                                <!-- GST -->
                                @php
                                    $gst = round($amount * 0.18, 2); // 18% GST
                                @endphp
                                <div class="mb-2 d-flex align-items-center justify-content-between">
                                    <span class="form-label mb-0">GST(18%)</span>
                                    <span class="fw-semibold" id="gstValue">{{ $gst }}</span>
                                    <!-- keep gst posting to backend -->
                                    <input type="hidden" name="gst_amount" id="gst_amount"
                                        value="{{ $gst }}">
                                    <input type="hidden" name="gst_percentage" id="gstRate" value="18">
                                </div>

                                @php
                                    $total = $amount + $gst;
                                @endphp
                                <div class="total-box mt-3 d-flex align-items-center justify-content-between">
                                    <span>Total Payable</span>
                                    <span class="amount" id="totalAmount">₹{{ $total }}</span>
                                    <input type="hidden" name="net_amount" value="{{ $total }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </body>


    <!-- Razorpay Loader Overlay -->
    <div class="overlay" id="overlay"
        style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
        <div class="loader"
            style="border: 8px solid #f3f3f3; border-top: 8px solid #402d52; border-radius: 50%; width: 50px; height: 50px; animation: spin 2s linear infinite;">
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="processingModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center p-4">
                <!--<h4>Thank you!</h4>-->
                <p>Your order is being processed. Please wait...</p>
                <div class="spinner-border text-primary mx-auto" role="status"></div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        // ✅ CSRF Setup for all AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function showLoader() {
            document.getElementById('overlay').style.display = 'flex';
        }

        function hideLoader() {
            document.getElementById('overlay').style.display = 'none';
        }

        $('#regForm').submit(function(e) {
            e.preventDefault();
            showLoader();

            $.ajax({
                url: "{{ route('front.registration_store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {

                        // Show modal
                        $('#processingModal').modal('show');

                        const options = {
                            "key": "{{ config('app.razorpay_key') }}",
                            "amount": response.amount * 100,
                            "currency": "INR",
                            "order_id": response.razorpay_order_id,
                            "name": "Salexo",
                            "description": "Order Payment",
                            "handler": function(r) {
                                $.post("{{ route('razprpay.success') }}", {
                                    razorpay_payment_id: r.razorpay_payment_id,
                                    razorpay_order_id: r.razorpay_order_id,
                                    razorpay_signature: r.razorpay_signature,
                                    orderId: response.order_id
                                }, function(res) {
                                    // Use res.id instead of res directly
                                    {{--  window.location.href =
                                        "{{ route('razorpay.thankyou', ':id') }}"
                                        .replace(':id', res.id);  --}}
                                    if (res.id && res.id != 0) {
                                        // Redirect to thank you page
                                        window.location.href =
                                            "{{ route('razorpay.thankyou', ':id') }}"
                                            .replace(':id', res.id);
                                    } else {
                                        alert(
                                            'Payment verification failed. Please contact support.'
                                        );
                                        window.location.href =
                                            "{{ route('front.registration') }}";
                                    }
                                });
                            },
                            "prefill": {
                                "name": response.customer_name,
                                "email": response.email,
                                "contact": response.mobile
                            },
                            "theme": {
                                "color": "#1D2B4F"
                            },
                            modal: {
                                ondismiss: function() {
                                    // Hide the processing modal
                                    $('#processingModal').modal('hide');
                                    // Mark payment as failed
                                    $.post("{{ route('razorpay.payment_cancel_by_user') }}", {
                                        orderId: response.order_id,
                                    }, function() {
                                        window.location.href =
                                            "{{ route('razorpay.RazorFail') }}";
                                    }).fail(function() {
                                        hideLoader();
                                    });
                                }
                            }
                        };
                        const rzp = new Razorpay(options);
                        rzp.open();
                        hideLoader();
                    } else {
                        alert('Something went wrong.');
                        hideLoader();
                    }
                },
                error: function(err) {
                    alert('Checkout failed. Please try again.');
                    hideLoader();
                }
            });
        });
    </script>

@endsection
