@extends('layouts.front')
@section('title', 'Home')
@section('content')

    <!-- Hero -->
    <header class="hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">

                    <h1 class="display-5 fw-bold mb-3">
                        Turn Conversations into <span class="text-info">Conversions</span>.
                    </h1>
                    <p class="lead text-secondary mb-4">
                        Salexo helps you capture every lead, automate follow-ups, and close more deals — all from one
                        powerful,
                        easy-to-use platform.
                    </p>
                    <div class="d-flex gap-2">

                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#demoModal">
                            Schedule a Demo
                        </button>

                    </div>
                    <div class="d-flex gap-3 mt-4 text-secondary small">
                        <span><i class="bi bi-check2"></i> No credit card</span>
                        <span><i class="bi bi-check2"></i> 24×7 support</span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="glass p-3">
                        <img src="{{ asset('assets/front/images/salexo-about.png') }}" class="w-100 rounded"
                            alt="Salexo Dashboard preview" />
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- About -->
    <section id="about" class="section">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h2 class="section-title fw-bold mb-3">Meet Salexo — your pipeline, unified.</h2>
                    <p class="text-secondary">
                        Spreadsheets, sticky notes, and siloed tools slow teams down. Salexo centralizes lead capture,
                        follow-ups,
                        quotes, and analytics — so your sales motion is organized, automated, and measurable.
                    </p>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Capture leads from web &
                            mobile</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Automate reminders & tasks
                        </li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Generate quotations & track
                            deals
                        </li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Real-time dashboards &
                            reports</li>
                    </ul>
                    <a href="#features" class="btn btn-outline-light mt-2">Explore Features</a>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="feature-card p-4 h-100">
                                <div class="pill mb-3"><i class="bi bi-journal-plus"></i></div>
                                <h6 class="mb-2">Lead Entry</h6>
                                <p class="text-secondary small mb-0">Add leads in seconds with required fields and smart
                                    defaults.</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card p-4 h-100">
                                <div class="pill mb-3"><i class="bi bi-bell"></i></div>
                                <h6 class="mb-2">Follow-Ups</h6>
                                <p class="text-secondary small mb-0">Auto-schedule reminders so nothing slips through.</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card p-4 h-100">
                                <div class="pill mb-3"><i class="bi bi-receipt"></i></div>
                                <h6 class="mb-2">Quotations</h6>
                                <p class="text-secondary small mb-0">Create, send, and track quotes with status updates.</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card p-4 h-100">
                                <div class="pill mb-3"><i class="bi bi-graph-up"></i></div>
                                <h6 class="mb-2">Insights</h6>
                                <p class="text-secondary small mb-0">See conversions, ROI, and team performance at a glance.
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-secondary small mt-2 ms-1">Android app available — manage on the go.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof -->
    <section class="section pt-0">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-6">
                    <img src="{{ asset('assets/front/images/bar-chart.png') }}" alt="Salexo CRM Visual"
                        class="w-100 rounded shadow">
                </div>
                <div class="col-lg-6">
                    <div class="cta-band p-4 p-lg-5 h-100 d-flex flex-column justify-content-center">
                        <h3 class="fw-bold mb-2">Ready to organize, automate, and grow?</h3>
                        <p class="mb-4">Track, follow-up, and convert prospects into loyal clients — effortlessly.</p>
                        <ul class="text-secondary mt-3">
                            <li class="mb-2"><i class="bi bi-check2"></i> Built for speed and simplicity</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Actionable analytics — no data jungle</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> World-class support when you need it</li>
                        </ul>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#demoModal">
                                Book a Demo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Everything you need to scale sales</h2>
                <p class="text-secondary text-center">Simple to start. Powerful as you grow.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4">
                        <div class="pill mb-3"><i class="bi bi-ui-checks"></i></div>
                        <h5>Lead Entry, Simplified</h5>
                        <p class="text-secondary">Add leads via web/mobile with mandatory fields, notes, and auto-reminders
                            that
                            reduce manual work.</p>
                        <a href="#cta" class="link-light text-decoration-none">Learn more <i
                                class="bi bi-arrow-right-short"></i></a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4">
                        <div class="pill mb-3"><i class="bi bi-calendar-check"></i></div>
                        <h5>Automated Follow-Ups</h5>
                        <p class="text-secondary">Schedule, track, and mark follow-ups as pending or done — keep your team
                            on the
                            same page.</p>
                        <a href="#cta" class="link-light text-decoration-none">Learn more <i
                                class="bi bi-arrow-right-short"></i></a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4">
                        <div class="pill mb-3"><i class="bi bi-file-earmark-text"></i></div>
                        <h5>Quotations & Deals</h5>
                        <p class="text-secondary">Generate professional quotes, send instantly, and track deal status
                            through every
                            stage.</p>
                        <a href="#cta" class="link-light text-decoration-none">Learn more <i
                                class="bi bi-arrow-right-short"></i></a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4">
                        <div class="pill mb-3"><i class="bi bi-speedometer2"></i></div>
                        <h5>Analytics & Reports</h5>
                        <p class="text-secondary">Visual dashboards show leads generated vs. converted, ROI, and
                            performance trends.
                        </p>
                        <a href="#cta" class="link-light text-decoration-none">Learn more <i
                                class="bi bi-arrow-right-short"></i></a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4">
                        <div class="pill mb-3"><i class="bi bi-phone"></i></div>
                        <h5>Mobile App</h5>
                        <p class="text-secondary">Instant notifications, one-tap updates, and on-the-go analytics for field
                            teams.
                        </p>
                        <a href="#mobile" class="link-light text-decoration-none">See mobile <i
                                class="bi bi-arrow-right-short"></i></a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4">
                        <div class="pill mb-3"><i class="bi bi-cloud-arrow-down"></i></div>
                        <h5>Cloud-Based & Secure</h5>
                        <p class="text-secondary">Fast setup, no servers to manage, and secure infrastructure out of the
                            box.</p>
                        <a href="#cta" class="link-light text-decoration-none">Get started <i
                                class="bi bi-arrow-right-short"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="cta" class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Plans</h2>
                <p class="text-secondary text-center">Transparent pricing that scales with your team.</p>
            </div>

            <div class="row g-4">

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4 h-100">
                        <h5 class="mb-1">{{ $monthly->plan_name }}</h5>
                        <p class="text-secondary small mb-3">For individuals and Exhibitor</p>
                        <h3 class="mb-3">₹{{ $monthly->plan_amount }}<span class="fs-6 text-secondary"> / mo</span></h3>
                        <ul class="text-secondary list-unstyled mb-3">
                            <li class="mb-2"><i class="bi bi-check2"></i> 3 user</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> 1 Admin</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Lead capture & follow-ups</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Basic reports</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Sales Pipeline</li>
                        </ul>

                        <a href="{{ route('front.registration', ['plan' => $monthly->plan_name, 'amount' => $monthly->plan_amount, 'days' => $monthly->plan_days]) }}"
                            class="btn btn-primary w-100">Buy Now</a>

                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4 h-100 border border-2 border-info">
                        <!-- <div class="badge bg-info text-dark mb-2">Popular</div> -->
                        <h5 class="mb-1">{{ $sixmonthly->plan_name }}</h5>
                        <p class="text-secondary small mb-3">For growing sales teams</p>
                        <h3 class="mb-3">₹{{ $sixmonthly->plan_amount }}<span class="fs-6 text-secondary"> / 6 mo</span>
                        </h3>
                        <ul class="text-secondary list-unstyled mb-3">
                            <li class="mb-2"><i class="bi bi-check2"></i> 3 user</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> 1 Admin</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Lead capture & follow-ups</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Basic reports</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Sales Pipeline</li>
                        </ul>

                        <a href="{{ route('front.registration', ['plan' => $sixmonthly->plan_name, 'amount' => $sixmonthly->plan_amount, 'days' => $sixmonthly->plan_days]) }}"
                            class="btn btn-primary w-100">Buy Now</a>

                    </div>
                </div>

                <div class="col-md-12 col-lg-4">
                    <div class="feature-card p-4 h-100">
                        <h5 class="mb-1">{{ $yearly->plan_name }}</h5>
                        <p class="text-secondary small mb-3">For advanced security & scale</p>
                        <h3 class="mb-3">₹{{ $yearly->plan_amount }}<span class="fs-6 text-secondary"> / yr</span></h3>
                        <ul class="text-secondary list-unstyled mb-3">
                            <li class="mb-2"><i class="bi bi-check2"></i> 3 user</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> 1 Admin</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Lead capture & follow-ups</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Basic reports</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Sales Pipeline</li>
                        </ul>

                        <a href="{{ route('front.registration', ['plan' => $yearly->plan_name, 'amount' => $yearly->plan_amount, 'days' => $yearly->plan_days]) }}"
                            class="btn btn-primary w-100">Buy Now</a>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section id="how" class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">How it works</h2>
                <p class="text-secondary text-center">A streamlined workflow from lead to loyal customer.</p>
            </div>

            <!-- Steps Rail -->
            <div class="row g-4 how-rail">
                <!-- Step 1 -->
                <div class="col-lg-4">
                    <div class="step step-card">
                        <div class="num">1</div>
                        <div>
                            <div class="step-kicker text-uppercase fw-semibold">Capture</div>
                            <h5 class="mb-1 d-flex align-items-center gap-2">
                                <!-- <i class="bi bi-journal-plus text-primary-emphasis"></i> -->
                                Add Leads
                            </h5>
                            <p class="text-secondary mb-0">
                                Capture new prospects via web forms or the mobile app. Log notes, tags, and source in one
                                place.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-lg-4">
                    <div class="step step-card">
                        <div class="num">2</div>
                        <div>
                            <div class="step-kicker text-uppercase fw-semibold">Automate</div>
                            <h5 class="mb-1 d-flex align-items-center gap-2">
                                <!-- <i class="bi bi-calendar-check text-primary-emphasis"></i> -->
                                Schedule Follow-Ups
                            </h5>
                            <p class="text-secondary mb-0">
                                Automate reminders, assign owners, and track status so you never miss the moment.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-lg-4">
                    <div class="step step-card">
                        <div class="num">3</div>
                        <div>
                            <div class="step-kicker text-uppercase fw-semibold">Convert</div>
                            <h5 class="mb-1 d-flex align-items-center gap-2">
                                <!-- <i class="bi bi-graph-up text-primary-emphasis"></i> -->
                                Analyze & Convert
                            </h5>
                            <p class="text-secondary mb-0">
                                Use real-time analytics to focus on high-intent leads and close with confidence.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Steps Rail -->
        </div>
    </section>

    <!-- Mobile App -->
    <section id="mobile" class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Salexo for Android</h2>
                <p class="text-secondary text-center">Work anywhere with instant notifications, quick lead entry, and
                    pocket-sized
                    analytics.</p>
            </div>

            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <img src="{{ asset('assets/front/images/app.jpg') }}" class="w-100 rounded glass"
                        alt="Salexo Mobile App" style="height: 400px; object-fit: cover;" />
                </div>
                <div class="col-lg-6">
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="bi bi-bell me-2 text-dark"></i><strong>Instant
                                notifications:</strong> act on hot
                            leads immediately.</li>
                        <li class="mb-3"><i class="bi bi-journal-plus me-2 text-dark"></i><strong>Fast lead
                                entry:</strong> capture
                            essentials in seconds.</li>
                        <li class="mb-3"><i class="bi bi-graph-up me-2 text-dark"></i><strong>On-the-go
                                analytics:</strong> check
                            performance anytime.</li>
                    </ul>
                    <a href="https://play.google.com/store/apps/details?id=com.apollo.salexo" target="_blank"
                        rel="noopener">
                        <img src="{{ asset('assets/front/images/dashboard.png') }}" alt="Get it on Google Play"
                            style="height:60px; width:auto;">
                    </a>

                </div>
            </div>
        </div>
    </section>



    <!-- Contact -->
    <section id="contact" class="section">
        <div class="container">
            <div class="row g-4">

                <!-- Left: creative contact panel (replaces the old form block only) -->
                <div class="col-lg-6">
                    <div class="contact-panel-dark">
                        <!-- angled ribbon -->
                        <span class="ribbon">Let’s connect</span>

                        <h2 class=" fw-bold mb-1">Talk to Salexo</h2>
                        <p class="mt-2 mb-2">Follow, call, or email — we’ll get back fast.</p>

                        <!-- Socials -->
                        <div class="social-stack mb-2">
                            <a href="https://www.instagram.com/salexo.leadcrm/" target="_blank" rel="noopener"
                                class="social-badge" title="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="https://www.facebook.com/salexo.leadcrm" target="_blank" rel="noopener"
                                class="social-badge" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                        </div>

                        <!-- Contact chips -->
                        <div class="d-flex flex-column flex-md-row gap-2 mb-2">
                            <a href="tel:+91 63567 63136" class="contact-chip">
                                <span class="chip-icon"><i class="bi bi-telephone"></i></span>
                                <div class="chip-body">
                                    <span class="chip-label">Phone</span>
                                    <span class="chip-value">+91 63567 63136</span>
                                </div>
                            </a>

                            <a href="mailto:info@salexo.in" class="contact-chip">
                                <span class="chip-icon"><i class="bi bi-envelope"></i></span>
                                <div class="chip-body">
                                    <span class="chip-label">Email</span>
                                    <span class="chip-value">info@salexo.in</span>
                                </div>
                            </a>
                        </div>

                        <!-- CTA -->
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#demoModal">
                            <span class="btn-cta-main">Contact us for demo</span>
                        </button>
                    </div>
                </div>


                <div class="col-lg-6">
                    <div class="feature-card p-4 h-100">
                        <h5>Why teams choose Salexo</h5>
                        <ul class="text-secondary mt-3">
                            <li class="mb-2"><i class="bi bi-check2"></i> Built for speed and simplicity</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> Actionable analytics — no data jungle</li>
                            <li class="mb-2"><i class="bi bi-check2"></i> World-class support when you need it</li>
                        </ul>
                        <hr class="border-secondary-subtle" />
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-shield-lock fs-3"></i>
                            <div class="small text-secondary">
                                Your data is encrypted in transit and at rest. We never sell or share your data.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule a Demo — Modal -->
    <div class="modal fade" id="demoModal" tabindex="-1" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title fw-semibold" id="demoModalLabel">Schedule a Demo</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-2">
                    <!-- helper text -->
                    <p class="text-secondary small mb-3">
                        Tell us a bit about your company so we can tailor the demo. Fields marked <span
                            class="text-danger">*</span>
                        are required.
                    </p>

                    <form method="POST" action="{{ route('front.request_for_demo') }}">
                        @csrf

                        <div class="row g-3">
                            <!-- Left column -->
                            <div class="col-md-6">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company_name"
                                    placeholder="Enter company name" required value="{{ old('company_name') }}"
                                    autocomplete="off">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="contact_person_name"
                                    placeholder="Enter contact person name" value="{{ old('contact_person_name') }}"
                                    required autocomplete="off">
                                @error('contact_person_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mobile"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    value="{{ old('mobile') }}" maxlength="10" minlength="10"
                                    placeholder="Enter mobile number" required autocomplete="off">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Situable time <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="situable_time"
                                    value="{{ old('situable_time') }}" placeholder="Enter Situable time" required
                                    autocomplete="off">
                                @error('situable_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- note -->
                        <div class="form-text mt-2">
                            We’ll use this info to tailor your demo and get you set up quickly.
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Request Demo</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Thank You Modal -->
    <div class="modal fade" id="thankYouModal" tabindex="-1" aria-labelledby="thankYouModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold" id="thankYouModalLabel">Thank You!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-2 pb-3">
                    <p class="text-center mb-0">Your demo request has been received successfully. </p>
                    <p class="text-center">Our team will contact you shortly.</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Thank You Modal -->
    <div class="modal fade" id="paymentthankYouModal" tabindex="-1" aria-labelledby="paymentthankYouModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold" id="paymentthankYouModalLabel">🎉 Payment Successful!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-2 pb-3">
                    <p class="text-center mb-0">Thank you for your payment,
                    </p>
                    <p class="text-center">Your subscription has been activated successfully.</p>
                    <p class="text-center">You can now access all premium features.</p>
                    <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal">Start Exploring</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Payment Failed Modal -->
    <div class="modal fade" id="paymentFailModal" tabindex="-1" aria-labelledby="paymentFailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold text-danger" id="paymentFailModalLabel">⚠️ Payment Failed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-2 pb-3">
                    <p class="text-center mb-2 text-muted">Unfortunately, your payment could not be processed.</p>
                    <p class="text-center text-secondary">Please try again, or contact our support team if the issue
                        persists.</p>
                    <button type="button" class="btn btn-danger mt-3" data-bs-dismiss="modal">Try Again</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@if (session('demo_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var thankYouModal = new bootstrap.Modal(document.getElementById('thankYouModal'));
            thankYouModal.show();
        });
    </script>
@endif

@if (session('payment_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var paymentthankYouModal = new bootstrap.Modal(document.getElementById('paymentthankYouModal'));
            paymentthankYouModal.show();
        });
    </script>
@endif

@if (session('payment_fail'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var paymentFailModal = new bootstrap.Modal(document.getElementById('paymentFailModal'));
            paymentFailModal.show();
        });
    </script>
@endif
