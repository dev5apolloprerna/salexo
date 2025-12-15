@extends('layouts.front')
@section('title', 'Home')
@section('content')

    <body class="terms-page">
        <!-- Page hero -->
        <header class="page-hero">
            <div class="container">
                <span class="kicker">Legal</span>
                <h1 class="display-6 fw-bold mt-2 mb-1">Terms &amp; Conditions</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('front.index') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Terms &amp; Conditions</li>
                    </ol>
                </nav>
            </div>
        </header>

        <!-- Terms content -->
        <main class="terms-wrap">
            <div class="container">
                <div class="terms-card">
                    <h2 class="h4">Terms &amp; Conditions</h2>
                    <p>Welcome to <strong>Salexo</strong>. By accessing or using our website, mobile application, and
                        services, you
                        agree to comply with the following Terms &amp; Conditions. Please read them carefully before booking
                        any
                        service.</p>

                    <h3>1. General</h3>
                    <ul>
                        <li><strong>Salexo</strong> is a service platform that connects customers with verified technicians
                            for
                            household, appliance, and maintenance services.</li>
                        <li>By booking a service, the customer agrees to follow these terms.</li>
                        <li><strong>Salexo</strong> reserves the right to update or modify these terms at any time without
                            prior
                            notice.</li>
                    </ul>

                    <h3>2. Service Booking</h3>
                    <ul>
                        <li>Customers can book services through the <strong>Salexo</strong> website or mobile application.
                        </li>
                        <li>Bookings are confirmed only when the request is accepted by a technician.</li>
                        <li><strong>Salexo</strong> reserves the right to cancel or reschedule a service if necessary (e.g.,
                            unavailability of technicians, safety issues, or unforeseen circumstances).</li>
                    </ul>

                    <h3>3. Payments</h3>
                    <ul>
                        <li>Customers can pay using online methods (UPI, debit/credit cards, wallets) or choose cash payment
                            at the
                            time of service.</li>
                        <li>Prices are transparent and displayed before booking. No hidden charges will be applied.</li>
                        <li>Service charges may vary depending on the type of issue and spare parts required (if any).</li>
                    </ul>

                    <h3>4. Cancellation &amp; Refunds</h3>
                    <ul>
                        <li>If the customer cancels the booking before the technician accepts, a full refund will be
                            processed.</li>
                        <li>Once a technician has accepted the booking, no refund will be issued under any circumstances.
                        </li>
                        <li>Customers can cancel their booking directly through the app or website using the cancellation
                            option.</li>
                        <li>Refunds (if applicable) will be processed within 7–10 business days to the original payment
                            method.</li>
                    </ul>

                    <h3>5. Customer Responsibilities</h3>
                    <ul>
                        <li>Customers must provide accurate details (address, contact number, problem description) when
                            booking a
                            service.</li>
                        <li>Customers must ensure a safe working environment for the technician.</li>
                        <li>Any misuse of the platform, fraudulent bookings, or misbehavior with technicians will result in
                            account
                            suspension.</li>
                    </ul>

                    <h3>6. Technician Verification &amp; Responsibilities</h3>
                    <ul>
                        <li>All <strong>Salexo</strong> technicians undergo background verification before being allowed to
                            provide
                            services.</li>
                        <li>Technicians are required to maintain professional behavior, honesty, and respect toward
                            customers.</li>
                        <li>Technicians must not demand extra charges beyond what is shown on the application unless
                            additional parts
                            or services are required (with customer approval).</li>
                        <li>Any misconduct or fraudulent activity by technicians will lead to permanent removal from
                            <strong>Salexo</strong>'s network.
                        </li>
                    </ul>

                    <h3>7. Safety &amp; Security</h3>
                    <ul>
                        <li><strong>Salexo</strong> takes customer safety seriously. Verified ID and background checks are
                            mandatory
                            for all technicians.</li>
                        <li>Customers are advised not to make direct payments outside of the official
                            <strong>Salexo</strong>
                            platform.
                        </li>
                        <li><strong>Salexo</strong> will not be responsible for any damages caused by unauthorized dealings
                            outside
                            the platform.</li>
                    </ul>

                    <h3>8. Warranty &amp; Service Guarantee</h3>
                    <ul>
                        <li><strong>Salexo</strong> provides a limited service warranty (if applicable) on certain services,
                            which
                            will be clearly mentioned during booking.</li>
                        <li>Warranty does not cover damages caused by misuse, negligence, or third-party intervention after
                            the
                            service is completed.</li>
                    </ul>

                    <h3>9. Limitation of Liability</h3>
                    <ul>
                        <li><strong>Salexo</strong> acts only as a platform connecting customers with technicians.</li>
                        <li><strong>Salexo</strong> is not responsible for any direct, indirect, incidental, or
                            consequential damages
                            that may occur during or after the service.</li>
                        <li>Customers are encouraged to immediately report any issue with technicians so that appropriate
                            action can
                            be taken.</li>
                    </ul>

                    <h3>10. User Account</h3>
                    <ul>
                        <li>Customers and technicians must register with accurate personal details.</li>
                        <li>Users are responsible for maintaining the confidentiality of their login credentials.</li>
                        <li><strong>Salexo</strong> reserves the right to suspend or terminate accounts in case of fraud,
                            misuse, or
                            violation of these terms.</li>
                    </ul>

                    <h3>11. Dispute Resolution</h3>
                    <ul>
                        <li>Any disputes or complaints regarding services must be reported to <strong>Salexo</strong>
                            customer support
                            within 48 hours of service completion.</li>
                        <li><strong>Salexo</strong> will investigate and take appropriate steps to resolve the matter.</li>
                        <li>In case of legal disputes, the jurisdiction will lie within the courts of <em>[Insert
                                City/State, e.g.,
                                Delhi, India]</em>.</li>
                    </ul>

                    <h3>12. Contact Us</h3>
                    <p>For any questions, complaints, or clarifications regarding these Terms &amp; Conditions, you can
                        reach us at:
                    </p>

                    <div class="contact-callout">
                        <div class="contact-item">
                            <span class="contact-icon"><i class="bi bi-envelope"></i></span>
                            <a href="mailto:support@auraclap.com" class="link-dark text-decoration-none">info@salexo.in</a>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon"><i class="bi bi-telephone"></i></span>
                            <a href="tel:+919875134775" class="link-dark text-decoration-none">+91&nbsp;63567
                                &nbsp;63136</a>
                        </div>
                    </div>

                    <p class="mt-3 mb-0"><em>By using <strong>Salexo</strong>, you acknowledge that you have read,
                            understood, and
                            agreed to these Terms &amp; Conditions.</em></p>
                </div>
            </div>
        </main>
    </body>

@endsection
