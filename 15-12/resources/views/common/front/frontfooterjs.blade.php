<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar shadow on scroll
    const nav = document.querySelector('.navbar');
    const backToTop = document.getElementById('backToTop');
    const onScroll = () => {
        if (window.scrollY > 8) nav.classList.add('scrolled');
        else nav.classList.remove('scrolled');

        if (window.scrollY > 300) backToTop.classList.add('show');
        else backToTop.classList.remove('show');
    };
    document.addEventListener('scroll', onScroll);
    onScroll();

    // Back to top
    backToTop.addEventListener('click', () => window.scrollTo({
        top: 0,
        behavior: 'smooth'
    }));
    {{--
    // Contact form (demo-only)
    (function() {
        const form = document.getElementById('contactForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            // Demo success UI
            document.getElementById('formSuccess').classList.remove('d-none');
            form.reset();
            form.classList.remove('was-validated');
        }, false);
    })();

    // Year
    document.getElementById('year').textContent = new Date().getFullYear();
    --}}
</script>

{{--  <script>
    // Demo modal form validation + success UI
    (function() {
        const form = document.getElementById('demoForm');
        const success = document.getElementById('demoFormSuccess');

        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // HTML5 validation
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                success.classList.add('d-none');
                return;
            }

            // TODO: send to your backend via fetch/axios here.
            // For now, just show success UI:
            form.classList.remove('was-validated');
            success.classList.remove('d-none');

            // Optionally clear inputs
            // form.reset();
        }, false);
    })();
</script>  --}}
