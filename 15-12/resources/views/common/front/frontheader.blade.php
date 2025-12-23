 <!-- Navbar -->
 <nav class="navbar navbar-expand-lg fixed-top navbar-dark">
     <div class="container">
         <a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="{{ route('front.index') }}">
             <img src="{{ asset('assets/front/images/logo (1).png') }}" alt="Salexo" height="34" class="logo-img">
             <span class="visually-hidden">Salexo</span>
         </a>

         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
             <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse" id="nav">
             <ul class="navbar-nav ms-auto align-items-lg-center">
                 <li class="nav-item"><a class="nav-link" href="{{ route('front.index') }}#about">About</a></li>
                 <li class="nav-item"><a class="nav-link" href="{{ route('front.index') }}#features">Features</a></li>
                 <li class="nav-item"><a class="nav-link" href="{{ route('front.index') }}#how">How it works</a></li>
                 <li class="nav-item"><a class="nav-link" href="{{ route('front.index') }}#mobile">Mobile App</a></li>
                 <li class="nav-item"><a class="nav-link" href="{{ route('front.index') }}#contact">Contact</a></li>
                 <li class="nav-item ms-lg-3 my-2 my-lg-0">
                     <a class="btn btn-sm btn-primary" href="{{ route('user_login') }}">Login</a>
                 </li>
             </ul>
         </div>
     </div>
 </nav>
