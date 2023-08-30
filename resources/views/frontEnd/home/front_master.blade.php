<style>
  .footer-list ul {
    list-style: none;
    padding-left: 0;
    margin-bottom: 50px;
  }

  .footer-list ul li {
    display: block;
    margin-bottom: 10px;
    cursor: pointer;
  }

  .f_title {
    margin-bottom: 40px;
  }

  .f_title h4 {
    color: #415094;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 0px;
  }
</style>
@php
  if (moduleStatusCheck('ParentRegistration')) {
      $reg_setting = Modules\ParentRegistration\Entities\SmRegistrationSetting::where('school_id', $school->id)->first();
      $is_registration_permission = $reg_setting ? $reg_setting->position : null;
  }
  $setting = generalSetting();
  App::setLocale(getUserLanguage());
  $ttl_rtl = userRtlLtl();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @if (isset($ttl_rtl) && $ttl_rtl == 1) dir="rtl" class="rtl" @endif>

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="Infix is 100+ unique feature enable school management software system. It can manage all type of school, academy and any educational institution" />
  <link rel="icon" href="{{ asset($setting->favicon) }}" type="image/png" />
  <title>{{ $setting->site_title ? $setting->site_title : 'Infix Edu ERP' }}</title>
  <meta name="_token" content="{!! csrf_token() !!}" />
  <!-- Bootstrap CSS -->
  @if ($setting->site_title == 1)
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/rtl/bootstrap.min.css" />
  @else
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/bootstrap.css" />
  @endif

  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/jquery-ui.css" />

  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/bootstrap-datepicker.min.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/bootstrap-datetimepicker.min.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/themify-icons.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/font-awesome.min.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/nice-select.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/magnific-popup.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/fastselect.min.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/owl.carousel.min.css" />
  <!-- main css -->

  @if ($setting->site_title == 1)
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/rtl/style.css" />
  @else
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/css/{{ @activeStyle()->path_main_style }}" />
  @endif

  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/fullcalendar.min.css">
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/fullcalendar.print.css">

  <link rel="stylesheet" href="{{ asset('public/') }}/frontend/css/infix.css" />

  <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery-3.2.1.min.js"></script>
  <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
  @stack('css')
</head>

<body class="client light">
  <!--================ Start Header Menu Area =================-->
  <header class="header-area " style="position: absolute">
    <div class="main_menu">
      <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container box-1420">
          <!-- Brand and toggle get grouped for better mobile display -->
          <a class="navbar-brand" href="{{ url('/') }}/home">
            <img class="w-75" src="{{ asset($setting->logo ? $setting->logo : 'public/uploads/settings/logo.png') }}"
              alt="Infix Logo" style="max-width: 250px;">
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="ti-menu"></span>
          </button>

          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
            <ul class="nav navbar-nav menu_nav ml-auto">
              @auth
                @if (auth()->user()->role_id == 1)
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/admin-dashboard') }}" target="_blank">Admin Dashboard</a>
                  </li>
                @endif
                @if (auth()->user()->role_id == 2)
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/student-dashboard') }}" target="_blank">Student Dashboard</a>
                  </li>
                @endif
                @if (auth()->user()->role_id == 3)
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/parent-dashboard') }}" target="_blank">Parent Dashboard</a>
                  </li>
                @endif
                @if (auth()->user()->role_id == 4)
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/teacher-dashboard') }}" target="_blank">Teacher Dashboard</a>
                  </li>
                @endif
              @endauth
              @guest
                <li class="nav-item">
                  <a class="nav-link" href="{{ url('/login') }}" target="_blank">@lang('common.login')</a>
                </li>
              @endguest

              @if (moduleStatusCheck('Saas') and !session('domain'))
                <li class="nav-item">
                  <a class="nav-link" href="{{ url('/institution-register-new') }}"
                    target="_blank">@lang('common.school_signup')</a>
                </li>
              @endif

              @if (moduleStatusCheck('ParentRegistration') && isset($is_registration_permission) && $is_registration_permission == 1)
                <li class="nav-item">
                  <a class="nav-link"
                    href="{{ route('parentregistration/registration', $reg_setting->url) }}">@lang('student.student_registration')</a>
                </li>
              @endif

            </ul>

          </div>
        </div>
      </nav>
    </div>
  </header>
  <!--================ End Header Menu Area =================-->

  @yield('main_content')

  <!--================Footer Area =================-->
  <footer class="footer_area section-gap-top-20">
    <div class="container">
      {{-- <div class="row footer_inner">
        @if (@$custom_link != '')
          <div class="col-lg-3 col-sm-6">
            <div class="footer-widget">
              <div class="f_title">
                <h4>{{ $custom_link->title1 }}</h4>
              </div>
              <div class="footer-list">
                <nav>
                  <ul>
                    @if (moduleStatusCheck('ParentRegistration') == true)
                      @if (isset($is_registration_permission) && $is_registration_permission == 2)
                        <li>
                          <a href="{{ route('parentregistration/registration', $reg_setting->url) }}">
                            @lang('student.student_registration')
                          </a>
                        </li>
                      @endif
                    @endif
                    @if ($custom_link->link_href1 != '')
                      <li>
                        <a href="{{ $custom_link->link_href1 }}">
                          {{ $custom_link->link_label1 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href5 != '')
                      <li>
                        <a href="{{ $custom_link->link_href5 }}">
                          {{ $custom_link->link_label5 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href9 != '')
                      <li>
                        <a href="{{ $custom_link->link_href9 }}">
                          {{ $custom_link->link_label9 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href13 != '')
                      <li>
                        <a href="{{ $custom_link->link_href13 }}">
                          {{ $custom_link->link_label13 }}
                        </a>
                      </li>
                    @endif
                  </ul>
                </nav>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="footer-widget">
              <div class="f_title">
                <h4>{{ $custom_link->title2 }}</h4>
              </div>
              <div class="footer-list">
                <nav>
                  <ul>
                    @if ($custom_link->link_href2 != '')
                      <li>
                        <a href="{{ $custom_link->link_href2 }}">
                          {{ $custom_link->link_label2 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href6 != '')
                      <li>
                        <a href="{{ url($custom_link->link_href6) }}">
                          {{ $custom_link->link_label6 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href10 != '')
                      <li>
                        <a href="{{ $custom_link->link_href10 }}">
                          {{ $custom_link->link_label10 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href14 != '')
                      <li>
                        <a href="{{ $custom_link->link_href14 }}">
                          {{ $custom_link->link_label14 }}
                        </a>
                      </li>
                    @endif
                  </ul>
                </nav>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="footer-widget">
              <div class="f_title">
                <h4>{{ $custom_link->title3 }}</h4>
              </div>
              <div class="footer-list">
                <nav>
                  <ul>
                    @if ($custom_link->link_href3 != '')
                      <li>
                        <a href="{{ $custom_link->link_href3 }}">
                          {{ $custom_link->link_label3 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href7 != '')
                      <li>
                        <a href="{{ $custom_link->link_href7 }}">
                          {{ $custom_link->link_label7 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href11 != '')
                      <li>
                        <a href="{{ $custom_link->link_href11 }}">
                          {{ $custom_link->link_label11 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href15 != '')
                      <li>
                        <a href="{{ $custom_link->link_href15 }}">
                          {{ $custom_link->link_label15 }}
                        </a>
                      </li>
                    @endif
                  </ul>
                </nav>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="footer-widget">
              <div class="f_title">
                <h4>{{ $custom_link->title4 }}</h4>
              </div>
              <div class="footer-list">
                <nav>
                  <ul>
                    @if ($custom_link->link_href4 != '')
                      <li>
                        <a href="{{ $custom_link->link_href4 }}">
                          {{ $custom_link->link_label4 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href8 != '')
                      <li>
                        <a href="{{ $custom_link->link_href8 }}">
                          {{ $custom_link->link_label8 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href12 != '')
                      <li>
                        <a href="{{ $custom_link->link_href12 }}">
                          {{ $custom_link->link_label12 }}
                        </a>
                      </li>
                    @endif
                    @if ($custom_link->link_href16 != '')
                      <li>
                        <a href="{{ $custom_link->link_href16 }}">
                          {{ $custom_link->link_label16 }}
                        </a>
                      </li>
                    @endif
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        @endif
      </div> --}}
      <div class="row single-footer-widget">
        <div class="col-lg-8 col-md-9">
          <div class="copy_right_text">
            @if ($setting->copyright_text)
              <p>{!! $setting->copyright_text !!}</p>
            @else
              Copyright © {{ date('Y') }} All rights reserved | This application is made with by Codethemes
            @endif
          </div>
        </div>
        @if ($social_permission)
          <div class="col-lg-4 col-md-3">
            <div class="social_widget">
              @foreach ($social_icons as $social_icon)
                @if (@$social_icon->url != '')
                  <a href="{{ @$social_icon->url }}">
                    <i class="{{ $social_icon->icon }}"></i>
                  </a>
                @endif
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </footer>
  <!--================End Footer Area =================-->

  {{-- <script src="{{asset('public/backEnd/')}}/vendors/js/jquery-3.2.1.min.js"></script> --}}
  <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery-ui.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/popper.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/bootstrap.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/nice-select.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery.magnific-popup.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/raphael-min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/morris.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/owl.carousel.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/moment.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/print/bootstrap-datetimepicker.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/bootstrap-datepicker.min.js"></script>


  <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDs3mrTgrYd6_hJS50x4Sha1lPtS2T-_JA">
  </script>
  <script src="{{ asset('public/backEnd/') }}/js/main.js"></script>
  <script src="{{ asset('public/backEnd/') }}/js/custom.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/js/developer.js"></script>
  {!! Toastr::message() !!}
  @yield('script')

</body>

</html>
