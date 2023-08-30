@extends('frontEnd.home.front_master')

@push('css')
  <link rel="stylesheet" href="{{ asset('public/') }}/frontend/css/new_style.css" />
@endpush

@section('main_content')
  <!--================ Home Banner Area =================-->
  <section class="client">
    <div class="new-home-banner-area">
      <div class="banner-inner">
        <div class="banner-content">
          <h5 style="color: #415094; border-top: 1px solid red;  border-bottom: 1px solid red;">Flourish Court Portal</h5>
          <h2 style="color: 716a6a; font-size: 50px">WELCOME TO FLOURISH COURT <br> DIGITAL SCHOOL </h2>
          <p style="color: #181313;font-size: 1rem;">Quality of life and prosperity depends on well educated student,
            skilled
            professionals, and educational resources.!</p>
          <div class="d-flex justify-content-center align-items-center gap-4 space-x-6">
            <a class="primary-btn fix-gr-bg semi-large mr-4" href="{{ url('/admission') }}">New Admission Request</a>
            @if(auth()->check())
               @if (auth()->user()->role_id == 1)
                  <a class="primary-btn bg-secondary semi-large" style="color: #fff" href="{{ url('/admin-dashboard') }}">Admin Dashboard</a>
                @endif
                @if (auth()->user()->role_id == 2)
                  <a class="primary-btn bg-secondary semi-large" style="color: #fff" href="{{ url('/student-dashboard') }}">Student Dashboard</a>
                @endif
                @if (auth()->user()->role_id == 3)
                  <a class="primary-btn bg-secondary semi-large" style="color: #fff" href="{{ url('/parent-dashboard') }}">Parent Dashboard</a>
                @endif
                @if (auth()->user()->role_id == 4)
                 <a class="primary-btn bg-secondary semi-large" style="color: #fff" href="{{ url('/teacher-dashboard') }}">Teacher Dashboard</a>
                @endif
           
            @else
                <a class="primary-btn bg-secondary semi-large" style="color: #fff" href="{{ url('/login') }}">Login Your
                  Account</a>
            @endif
            
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--================ End Home Banner Area =================-->

  <!--================ End Testimonial Area =================-->
@endsection
