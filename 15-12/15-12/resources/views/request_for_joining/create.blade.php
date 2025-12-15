<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Company Client</title>

    <!-- Bootstrap CSS (replace with your version if needed) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .page-header {
            background: #0d6efd;
            padding: 40px 0;
            color: #fff;
            text-align: center;
            border-radius: 0 0 15px 15px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header class="page-header">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" height="80">
        <h2 class="mt-3">Add Company Client</h2>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops! Something went wrong:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main class="container my-5">
        {{-- Alert Messages --}}
        @include('common.alert')

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Company Client Form</h4>
        </div>

        <div class="card p-4">
            <form action="{{ route('request_for_joining_store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    <div class="col-lg-4 col-md-6">
                        <label for="company_name">Company Name <span style="color:red;">*</span></label>
                        <input type="text" name="company_name" class="form-control" placeholder="Enter company name"
                            maxlength="255" required value="{{ old('company_name') }}" autofocus autocomplete="off">
                        @if ($errors->has('company_name'))
                            <span class="text-danger">
                                {{ $errors->first('company_name') }}
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="GST">GST </label>
                        <input type="text" name="GST" id="gstInput" class="form-control"
                            placeholder="Enter GST number" maxlength="15" value="{{ old('GST') }}"
                            autocomplete="off">

                        <small id="gstError" class="text-danger d-none">Invalid GST number format</small>

                        @if ($errors->has('GST'))
                            <span class="text-danger">
                                {{ $errors->first('GST') }}
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-6">

                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="contact_person_name">Contact Person <span style="color:red;">*</span></label>
                        <input type="text" name="contact_person_name" class="form-control"
                            placeholder="Enter contact person name" maxlength="100" required
                            value="{{ old('contact_person_name') }}" autocomplete="off">
                        @if ($errors->has('contact_person_name'))
                            <span class="text-danger">
                                {{ $errors->first('contact_person_name') }}
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="mobile">Mobile <span style="color:red;">*</span></label>
                        <input type="text" name="mobile" class="form-control" placeholder="Enter mobile number"
                            required value="{{ old('mobile') }}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');"
                            maxlength="10" minlength="10" autocomplete="off">
                        @if ($errors->has('mobile'))
                            <span class="text-danger">
                                {{ $errors->first('mobile') }}
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="email">Email <span style="color:red;">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email address"
                            maxlength="100" required value="{{ old('email') }}" autocomplete="off">
                        @if ($errors->has('email'))
                            <span class="text-danger">
                                {{ $errors->first('email') }}
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <label for="Address">Address <span style="color:red;">*</span></label>
                        <textarea name="Address" class="form-control" placeholder="Enter address" rows="3" required>{{ old('Address') }}</textarea>
                        @if ($errors->has('Address'))
                            <span class="text-danger">
                                {{ $errors->first('Address') }}
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6">

                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="state_id">State <span style="color:red;">*</span></label>
                        <select name="state_id" class="form-control" required>
                            <option value="">Select State</option>
                            @foreach ($states as $id => $state)
                                <option value="{{ $id }}" {{ old('state_id') == $id ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('state_id'))
                            <span class="text-danger">
                                {{ $errors->first('state_id') }}
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="city">City <span style="color:red;">*</span></label>
                        <input type="text" name="city" class="form-control" placeholder="Enter city name"
                            maxlength="20" required value="{{ old('city') }}" autocomplete="off">
                        @if ($errors->has('city'))
                            <span class="text-danger">
                                {{ $errors->first('city') }}
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="pincode">Pincode <span style="color:red;">*</span></label>
                        <input type="text" name="pincode" class="form-control" placeholder="Enter pincode"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');"
                            maxlength="6" minlength="6" required value="{{ old('pincode') }}" autocomplete="off">
                        @if ($errors->has('pincode'))
                            <span class="text-danger">
                                {{ $errors->first('pincode') }}
                            </span>
                        @endif
                    </div>


                    <div class="col-lg-4 col-md-6">
                        <label for="plan_id">Select Plan <span style="color:red;">*</span></label>
                        <select name="plan_id" id="plan_id" class="form-control" required>
                            <option value="">Select Plan</option>
                            @foreach ($plans as $id => $plan)
                                <option value="{{ $id }}" {{ old('plan_id') == $id ? 'selected' : '' }}>
                                    {{ $plan }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('plan_id'))
                            <span class="text-danger">
                                {{ $errors->first('plan_id') }}
                            </span>
                        @endif

                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="plan_amount">Plan Amount <span style="color:red;">*</span></label>
                        <input type="text" name="plan_amount" id="plan_amount" class="form-control"
                            placeholder="Enter plan amount" maxlength="100" value="{{ old('plan_amount') }}"
                            required autocomplete="off">
                        @if ($errors->has('plan_amount'))
                            <span class="text-danger">
                                {{ $errors->first('plan_amount') }}
                            </span>
                        @endif

                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="plan_days">Plan Days <span style="color:red;">*</span></label>
                        <input type="number" name="plan_days" id="plan_days" class="form-control"
                            placeholder="Enter number of days" value="{{ old('plan_days') }}" required
                            autocomplete="off">
                        @if ($errors->has('plan_daya'))
                            <span class="text-danger">
                                {{ $errors->first('plan_daya') }}
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <label for="subscription_start_date">Subscription Start Date <span
                                style="color:red;">*</span></label>
                        <input type="date" name="subscription_start_date" id="subscription_start_date"
                            class="form-control" value="{{ date('Y-m-d') }}" required autocomplete="off">
                    </div>

                    <!-- Subscription End Date (readonly, auto-calculated) -->
                    <div class="col-lg-6 col-md-6">
                        <label for="subscription_end_date">Subscription End Date <span
                                style="color:red;">*</span></label>
                        <input type="date" name="subscription_end_date" id="subscription_end_date"
                            class="form-control" value="{{ old('subscription_end_date') }}" required readonly>
                    </div>

                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="reset" class="btn btn-secondary">Clear</button>
                </div>
            </form>
        </div>
    </main>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

<script>
    const planDetails = @json($planDetails);

    document.addEventListener('DOMContentLoaded', function() {
        const planSelect = document.getElementById('plan_id');
        const planAmountInput = document.getElementById('plan_amount');
        const planDaysInput = document.getElementById('plan_days');
        const startDateInput = document.getElementById('subscription_start_date');
        const endDateInput = document.getElementById('subscription_end_date');

        function calculateEndDate() {
            const startDate = new Date(startDateInput.value);
            const planDays = parseInt(planDaysInput.value);

            if (!isNaN(startDate.getTime()) && !isNaN(planDays) && planDays > 0) {
                const endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + planDays);
                endDateInput.value = endDate.toISOString().split('T')[0];
            } else {
                endDateInput.value = '';
            }
        }

        planSelect.addEventListener('change', function() {
            const selectedId = this.value;

            if (planDetails[selectedId]) {
                planAmountInput.value = planDetails[selectedId].amount;
                planDaysInput.value = planDetails[selectedId].days;

                // Trigger recalculation of end date when plan days are updated
                calculateEndDate();
            } else {
                planAmountInput.value = '';
                planDaysInput.value = '';
                endDateInput.value = '';
            }
        });

        // Trigger end date calculation when either input changes
        startDateInput.addEventListener('change', calculateEndDate);
        planDaysInput.addEventListener('input', calculateEndDate);

        // Trigger on page load if plan is already selected
        if (planSelect.value) {
            planSelect.dispatchEvent(new Event('change'));
        }

        // Trigger calculation on page load if adding new
        @if (!isset($client->subscription_end_date))
            calculateEndDate();
        @endif
    });

    document.getElementById('gstInput').addEventListener('input', function() {
        let input = this.value.toUpperCase();
        this.value = input.slice(0, 15); // Force max 15 characters, uppercase

        const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        const errorMsg = document.getElementById('gstError');

        if (this.value.length === 15) {
            if (!gstRegex.test(this.value)) {
                errorMsg.classList.remove('d-none');
            } else {
                errorMsg.classList.add('d-none');
            }
        } else {
            errorMsg.classList.add('d-none'); // Hide error when input is incomplete
        }
    });
</script>

</html>
