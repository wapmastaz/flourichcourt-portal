<!DOCTYPE html>
{{-- @php
  $generalSetting = generalSetting();
@endphp --}}
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  @if (!is_null(schoolConfig()))
    <link rel="icon" href="{{ asset(schoolConfig()->favicon) }}" type="image/png" />
  @else
    <link rel="icon" href="{{ asset('public/uploads/settings/favicon.png') }}" type="image/png" />
  @endif

  <!-- <title>{{ @schoolConfig()->school_name ? @schoolConfig()->school_name : 'Infix Edu ERP' }} |
        {{ schoolConfig()->site_title ? schoolConfig()->site_title : 'School Management System' }}
    </title> -->
  <title>{{ @schoolConfig()->school_name ? @schoolConfig()->school_name : 'Infix Edu ERP' }} |
    @yield('title')
  </title>

  <meta name="_token" content="{!! csrf_token() !!}" />
  <!-- Bootstrap CSS -->
  @if (userRtlLtl() == 1)
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/rtl/bootstrap.min.css" />
  @else
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/bootstrap.css" />
  @endif
  <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery-3.2.1.min.js"></script>

  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/themify-icons.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/flaticon.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/font-awesome.min.css" />

  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/toastr.min.css" />


  @yield('css')

  <link rel="stylesheet" href="{{ asset('public/backEnd/css/loade.css') }}" />

  @if (userRtlLtl() == 1)
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/rtl/style.css" />
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/rtl/infix.css" />
  @else
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/{{ activeStyle()->path_main_style }}" />
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/{{ activeStyle()->path_infix_style }}" />
  @endif

  <style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: {{ @activeStyle()->primary_color2 }} !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
      background: {{ @activeStyle()->primary_color2 }} !important;
    }

    ::placeholder {
      color: {{ @activeStyle()->primary_color }} !important;
    }

    .datepicker.datepicker-dropdown.dropdown-menu.datepicker-orient-left.datepicker-orient-bottom {
      z-index: 99999999999 !important;
      background: #fff !important;
    }

    .input-effect {
      float: left;
      width: 100%;
    }

    #main-content {
      margin-left: auto;
      margin-right: auto;
    }

    .stu-sub-head {
      font-size: 18px;
      font-weight: 600;
    }

    body.admission-success {
      height: 100vh;
      display: flex;
      font-size: 14px;
      text-align: center;
      justify-content: center;
      align-items: center;
      flex-direction: column
    }

    .admission-success .wrapperAlert {
      width: 500px;
      height: auto;
      overflow: hidden;
      border-radius: 12px;
      border: thin solid #ddd;
    }

    .admission-success .topHalf {
      width: 100%;
      color: white;
      overflow: hidden;
      min-height: 250px;
      position: relative;
      padding: 40px 0;
      background: rgb(0, 0, 0);
      background: -webkit-linear-gradient(45deg, #019871, #a0ebcf);
    }

    .admission-success .topHalf p {
      margin-bottom: 30px;
    }

    .admission-success svg {
      fill: white;
    }

    .admission-success .topHalf h1 {
      font-size: 2.25rem;
      display: block;
      font-weight: 500;
      letter-spacing: 0.15rem;
      text-shadow: 0 2px rgba(128, 128, 128, 0.6);
    }

    /* Original Author of Bubbles Animation -- https://codepen.io/Lewitje/pen/BNNJjo */
    .admission-success .bg-bubbles {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
    }

    .admission-success li {
      position: absolute;
      list-style: none;
      display: block;
      width: 40px;
      height: 40px;
      background-color: rgba(255, 255, 255, 0.15);
      /* fade(green, 75%);*/
      bottom: -160px;

      -webkit-animation: square 20s infinite;
      animation: square 20s infinite;

      -webkit-transition-timing-function: linear;
      transition-timing-function: linear;
    }

    .admission-success li:nth-child(1) {
      left: 10%;
    }

    .admission-success li:nth-child(2) {
      left: 20%;

      width: 80px;
      height: 80px;

      animation-delay: 2s;
      animation-duration: 17s;
    }

    .admission-success li:nth-child(3) {
      left: 25%;
      animation-delay: 4s;
    }

    .admission-success li:nth-child(4) {
      left: 40%;
      width: 60px;
      height: 60px;

      animation-duration: 22s;

      background-color: rgba(white, 0.3);
      /* fade(white, 25%); */
    }

    .admission-success li:nth-child(5) {
      left: 70%;
    }

    .admission-success li:nth-child(6) {
      left: 80%;
      width: 120px;
      height: 120px;

      animation-delay: 3s;
      background-color: rgba(white, 0.2);
      /* fade(white, 20%); */
    }

    .admission-success li:nth-child(7) {
      left: 32%;
      width: 160px;
      height: 160px;

      animation-delay: 7s;
    }

    .admission-success li:nth-child(8) {
      left: 55%;
      width: 20px;
      height: 20px;

      animation-delay: 15s;
      animation-duration: 40s;
    }

    .admission-success li:nth-child(9) {
      left: 25%;
      width: 10px;
      height: 10px;

      animation-delay: 2s;
      animation-duration: 40s;
      background-color: rgba(white, 0.3);
      /*fade(white, 30%);*/
    }

    .admission-success li:nth-child(10) {
      left: 90%;
      width: 160px;
      height: 160px;

      animation-delay: 11s;
    }

    @-webkit-keyframes square {
      0% {
        transform: translateY(0);
      }

      100% {
        transform: translateY(-500px) rotate(600deg);
      }
    }

    @keyframes square {
      0% {
        transform: translateY(0);
      }

      100% {
        transform: translateY(-500px) rotate(600deg);
      }
    }

    .admission-success .bottomHalf {
      align-items: center;
      padding: 35px;
    }

    .admission-success .bottomHalf p {
      font-weight: 500;
      font-size: 1.05rem;
      margin-bottom: 20px;
    }

    .admission-success a.btn {
      border: none;
      color: white;
      cursor: pointer;
      border-radius: 12px;
      padding: 10px 18px;
      background-color: #019871;
      text-shadow: 0 1px rgba(128, 128, 128, 0.75);
    }

    .admission-success a.btn.success {
      background-color: #019871;
      text-shadow: 0 1px rgba(128, 128, 128, 0.75);
    }

    .admission-success a.btn.seondary {
      background-color: #eee;
      color: rgb(0, 0, 0);
      text-shadow: 0 1px rgba(128, 128, 128, 0.75);
    }

    .admission-success a.btn:hover {
      background-color: #85ddbf;
    }

    .admission-success .footer-area {
      width: 100%;
      margin-left: 0 !important;
      padding-top: 2rem;
    }
  </style>

  <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery-3.2.1.min.js"></script>
  <script>
    window.Laravel = {
      "baseUrl": '{{ url('/') }}' + '/',
      "current_path_without_domain": '{{ request()->path() }}'
    }

    window._locale = '{{ app()->getLocale() }}';
    window._rtl = {{ userRtlLtl() == 1 ? 'true' : 'false' }};
    window._translations = {!! cache('translations') !!};
  </script>
</head>
<?php
if (empty(dashboardBackground())) {
    $css = "background: url('/public/backEnd/img/body-bg.jpg')  no-repeat center; background-size: cover; ";
} else {
    if (!empty(dashboardBackground()->image)) {
        $css = "background: url('" . url(dashboardBackground()->image) . "')  no-repeat center; background-size: cover; ";
    } else {
        $css = 'background:' . dashboardBackground()->color;
    }
}
3;
?>
@php
  
  if (session()->has('homework_zip_file')) {
      $file_path = 'public/uploads/homeworkcontent/' . session()->get('homework_zip_file');
      if (file_exists($file_path)) {
          unlink($file_path);
      }
  }
@endphp

<body class="admin admission-success"
  style=" @if (@activeStyle()->id == 1) {{ $css }} @else background:{{ @activeStyle()->dashboardbackground }} !important; @endif ">
  <input type="hidden" id="nodata" value="@lang('common.no_data_available_in_table')">
  <!-- Sidebar  -->
  <!-- Page Content  -->
  <div class="main-content">
    <div class="wrapperAlert">

      <div class="contentAlert">

        <div class="topHalf">

          <p><svg viewBox="0 0 512 512" width="100" title="check-circle">
              <path
                d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" />
            </svg></p>
          <h1>Congratulations</h1>
          <h3 class="text-white">{{ $student->full_name ?? null }}</h3>

          <ul class="bg-bubbles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
          </ul>
        </div>

        <div class="bottomHalf bg-white">

          <p>Admission Application Submitted Successfully, Please Check Your Mail For Next Step</p>

          <div class="d-flex justify-content-center align-items-center">
            <a href="{{ url('/login') }}" class="btn success  rounded">Login Now</a>

          </div>


        </div>

      </div>

    </div>
  </div>
  <!--================Footer Area ================= -->
  <footer class="footer-area">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          Copyright {{ date('Y') }} All rights reserved by FCS
        </div>
      </div>
    </div>
  </footer>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/popper.js"></script>
  <script src="{{ asset('public/backEnd/') }}/css/rtl/bootstrap.min.js"></script>

  <script src="{{ asset('public/backEnd/') }}/vendors/js/raphael-min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/morris.min.js"></script>
  <script type="text/javascript" src="{{ asset('public/backEnd/') }}/vendors/js/toastr.min.js"></script>


  <script src="{{ asset('public/backEnd/') }}/js/main.js"></script>


  <script src="{{ asset('public/backEnd/') }}/js/developer.js"></script>

  <script src="{{ asset('public/landing/js/toastr.js') }}"></script>

  {!! Toastr::message() !!}
  <script src="{{ asset('public/js/app.js') }}"></script>

  <script src="{{ asset('public/backEnd/') }}/js/croppie.js"></script>
  <script src="{{ asset('public/backEnd/') }}/js/st_addmision.js"></script>

</body>

</html>
