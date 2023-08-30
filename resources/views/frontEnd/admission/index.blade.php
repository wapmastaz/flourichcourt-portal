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
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/jquery-ui.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/jquery.data-tables.css">
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/rowReorder.dataTables.min.css">
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/responsive.dataTables.min.css">
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/bootstrap-datepicker.min.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/bootstrap-datetimepicker.min.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/themify-icons.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/flaticon.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/font-awesome.min.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/nice-select.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/magnific-popup.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/fastselect.min.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/toastr.min.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/js/select2/select2.css" />
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/fullcalendar.min.css">
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/css/daterangepicker.css">

  <link rel="stylesheet" href="{{ asset('public/chat/css/notification.css') }}">
  <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/vendors/editor/summernote-bs4.css">

  @if (request()->route()->getPrefix() == '/chat')
    <link rel="stylesheet" href="{{ asset('public/chat/css/style.css') }}">
  @endif
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

<body class="admin"
  style=" @if (@activeStyle()->id == 1) {{ $css }} @else background:{{ @activeStyle()->dashboardbackground }} !important; @endif ">
  <div class="main-wrapper justify-contents-center" style="min-height: 600px">
    <input type="hidden" id="nodata" value="@lang('common.no_data_available_in_table')">
    <!-- Sidebar  -->

    <!-- Page Content  -->
    <div id="main-content">
      <input type="hidden" name="url" id="url" value="{{ url('/') }}">
      <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
          <div class="row">
            <div class="col-lg-12 col-sm-12">
              <div class="main-title text-center xs_mt_0 mt_0_sm">
                <h1 class="mb-4"> Flourish Court Admission Form</h1>
              </div>
            </div>
          </div>
          {{ Form::open(['class' => 'form-horizontal studentadmission', 'files' => true, 'route' => 'admission.save', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'student_form']) }}
          <div class="row">
            <div class="col-lg-12">
              <div class="white-box">
                <div class="">
                  <div class="row">
                    <div class="col-lg-12 text-center">
                      @if ($errors->any())
                        @foreach ($errors->all() as $error)
                          @if ($error == 'The email address has already been taken.')
                            <div class="error text-danger ">
                              {{ 'The email address has already been taken, You can find out in student list or disabled student list' }}
                            </div>
                          @endif
                          <p class="text-danger">{{ $error }}</p>
                        @endforeach
                      @endif
                      @if ($errors->any())
                        <div class="error text-danger ">{{ 'Something went wrong, please try again' }}</div>
                      @endif
                    </div>
                    <div class="col-lg-12">
                      <div class="main-title">
                        <h4 class="stu-sub-head">Information Of The Child:</h4>
                      </div>
                    </div>
                  </div>

                  <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                  <div class="row mb-40 mt-30">
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <select
                          class="niceSelect w-100 bb form-control{{ $errors->has('session') ? ' is-invalid' : '' }}"
                          name="session" id="academic_year">
                          <option data-display="@lang('common.academic_year') *" value="">@lang('common.academic_year') *</option>
                          @foreach ($sessions as $session)
                            <option value="{{ $session->id }}"
                              {{ old('session') == $session->id ? 'selected' : '' }}>
                              {{ date('Y', strtotime($session->starting_date)) }}/{{ date('Y', strtotime($session->ending_date)) }}[{{ $session->title }}]
                            </option>
                          @endforeach
                        </select>
                        <span class="focus-border"></span>
                        @if ($errors->has('session'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('session') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20" id="class-div">
                        <select
                          class="niceSelect w-100 bb form-control{{ $errors->has('class') ? ' is-invalid' : '' }}"
                          name="class" id="classSelectStudent">
                          <option data-display="@lang('common.class') *" value="">@lang('common.class') *</option>
                        </select>
                        <div class="pull-right loader loader_style" id="select_class_loader">
                          <img class="loader_img_style" src="{{ asset('public/backEnd/img/demo_wait.gif') }}"
                            alt="loader">
                        </div>
                        <span class="focus-border"></span>
                        @if ($errors->has('class'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('class') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>

                    {{-- <div class="col-lg-3">
                      <div class="input-effect sm2_mb_20 md_mb_20" id="section-div">
                        <select
                          class="niceSelect w-100 bb form-control{{ $errors->has('section') ? ' is-invalid' : '' }}"
                          name="section" id="sectionSelectStudent">
                          <option data-display="@lang('common.section') *" value="">@lang('common.section') *</option>
                        </select>
                        <div class="pull-right loader loader_style" id="select_section_loader">
                          <img class="loader_img_style" src="{{ asset('public/backEnd/img/demo_wait.gif') }}"
                            alt="loader">
                        </div>
                        <span class="focus-border"></span>
                        @if ($errors->has('section'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('section') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div> --}}

                    {{-- <div class="col-lg-4">
                      <div class="input-effect">
                        <input
                          class="primary-input  form-control{{ $errors->has('admission_number') ? ' is-invalid' : '' }}"
                          type="text" onkeyup="GetAdmin(this.value)" name="admission_number"
                          value="{{ $max_admission_id != '' ? $max_admission_id + 1 : 1 }}">

                        <label>@lang('student.admission_number')</label>
                        <span class="focus-border"></span>
                        <span class="invalid-feedback" id="Admission_Number" role="alert">
                        </span>
                        @if ($errors->has('admission_number'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('admission_number') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div> --}}

                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input class="primary-input" type="text" id="admission_number" name="admission_number"
                          value="{{ old('admission_number') != null ? old('admission_number') : getNumber(5) }}"
                          readonly>
                        <label>admission_number <span class="text-danger">*</span></label>
                        <span class="focus-border"></span>
                        <span class="text-danger" id="admission_number" role="alert">
                          <strong></strong>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-40">
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input class="primary-input form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                          type="text" name="first_name" value="{{ old('first_name') }}">
                        <label>@lang('student.first_name') <span class="text-danger">*</span> </label>
                        <span class="focus-border"></span>
                        @if ($errors->has('first_name'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('first_name') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input class="primary-input form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                          type="text" name="last_name" value="{{ old('last_name') }}">
                        <label>@lang('student.last_name') <span class="text-danger">*</span></label>
                        <span class="focus-border"></span>
                        @if ($errors->has('last_name'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('last_name') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input
                          class="primary-input form-control{{ $errors->has('middle_name') ? ' is-invalid' : '' }}"
                          type="text" name="middle_name" value="{{ old('middle_name') }}">
                        <label>Middle Name (optional)</label>
                        <span class="focus-border"></span>
                        @if ($errors->has('middle_name'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('middle_name') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>

                  </div>
                  <div class="row mb-40">

                    <div class="col-lg-4">
                      <div class="no-gutters input-right-icon">
                        <div class="col">
                          <div class="input-effect sm2_mb_20 md_mb_20">
                            <input
                              class="primary-input date form-control{{ $errors->has('date_of_birth') ? ' is-invalid' : '' }}"
                              id="startDate" type="text" name="date_of_birth" value="{{ old('date_of_birth') }}"
                              autocomplete="off">
                            <label>@lang('common.date_of_birth') <span class="text-danger">*</span></label>
                            <span class="focus-border"></span>
                            @if ($errors->has('date_of_birth'))
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('date_of_birth') }}</strong>
                              </span>
                            @endif
                          </div>
                        </div>
                        <div class="col-auto">
                          <button class="" type="button">
                            <i class="ti-calendar" id="start-date-icon"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <select
                          class="niceSelect w-100 bb form-control{{ $errors->has('religion') ? ' is-invalid' : '' }}"
                          name="religion">
                          <option data-display="@lang('student.religion') *" value="">@lang('student.religion') *</option>
                          @foreach ($religions as $religion)
                            <option value="{{ $religion->id }}"
                              {{ old('religion') == $religion->id ? 'selected' : '' }}>
                              {{ $religion->base_setup_name }}
                            </option>
                          @endforeach

                        </select>
                        <span class="focus-border"></span>
                        @if ($errors->has('religion'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('religion') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>

                    {{-- <div class="col-lg-2">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input class="primary-input" type="text" name="caste" value="{{ old('caste') }}">
                        <label>@lang('student.caste')</label>
                        <span class="focus-border"></span>
                      </div>
                    </div> --}}
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input oninput="emailCheck(this)"
                          class="primary-input form-control{{ $errors->has('email_address') ? ' is-invalid' : '' }}"
                          type="text" name="email_address" value="{{ old('email_address') }}">
                        <label>@lang('common.email_address') <span class="text-danger">*</span></label>
                        <span class="focus-border"></span>
                        @if ($errors->has('email_addr ss'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email_address') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    {{-- <div class="col-lg-3">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input oninput="phoneCheck(this)"
                          class="primary-input form-control{{ $errors->has('phone_number') ? ' is-invalid' : '' }}"
                          type="tel" name="phone_number" value="{{ old('phone_number') }}">
                        <label>@lang('student.phone_number')</label>
                        <span class="focus-border"></span>
                        @if ($errors->has('phone_number'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('phone_number') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div> --}}
                  </div>
                  {{-- <div class="row mb-40"> --}}
                  {{-- <div class="col-lg-2">
                      <div class="no-gutters input-right-icon">
                        <div class="col">
                          <div class="input-effect sm2_mb_20 md_mb_20">
                            <input class="primary-input date" id="" type="text" name="admission_date"
                              value="{{ old('admission_date') != '' ? old('admission_date') : date('m/d/Y') }}"
                              autocomplete="off">
                            <label>@lang('student.admission_date')</label>
                            <span class="focus-border"></span>
                          </div>
                        </div>
                        <div class="col-auto">
                          <button class="" type="button">
                            <i class="ti-calendar" id="admission-date-icon"></i>
                          </button>
                        </div>
                      </div>
                    </div> --}}


                  {{-- <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <select
                            class="niceSelect w-100 bb form-control{{ $errors->has('student_category_id') ? ' is-invalid' : '' }}"
                            name="student_category_id">
                            <option data-display="@lang('student.category')" value="">@lang('student.category')</option>
                            @foreach ($categories as $category)
                              <option value="{{ $category->id }}"
                                {{ old('student_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}</option>
                            @endforeach

                          </select>
                          <span class="focus-border"></span>
                          @if ($errors->has('student_category_id'))
                            <span class="invalid-feedback invalid-select" role="alert">
                              <strong>{{ $errors->first('student_category_id') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                    </div> --}}
                  {{-- <div class="col-lg-2">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <select
                            class="niceSelect w-100 bb form-control{{ $errors->has('student_group_id') ? ' is-invalid' : '' }}"
                            name="student_group_id">
                            <option data-display="@lang('student.group')" value="">@lang('student.group')</option>
                            @foreach ($groups as $group)
                              <option value="{{ $group->id }}"
                                {{ old('student_group_id') == $group->id ? 'selected' : '' }}>{{ $group->group }}
                              </option>
                            @endforeach

                          </select>
                          <span class="focus-border"></span>
                          @if ($errors->has('student_group_id'))
                            <span class="invalid-feedback invalid-select" role="alert">
                              <strong>{{ $errors->first('student_group_id') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                    </div> --}}
                  {{-- <div class="col-lg-2">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input class="primary-input" type="text" name="height" value="{{ old('height') }}">
                        <label>@lang('student.height_in')) <span></span> </label>
                        <span class="focus-border"></span>
                      </div>
                    </div> --}}
                  {{-- <div class="col-lg-2">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input class="primary-input" type="text" name="weight" value="{{ old('weight') }}">
                        <label>@lang('student.weight_kg') <span></span> </label>
                        <span class="focus-border"></span>
                      </div>
                    </div> --}}

                  {{-- </div> --}}

                  <div class="row mb-40">
                    <div class="col-lg-6">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <select
                          class="niceSelect w-100 bb form-control{{ $errors->has('gender') ? ' is-invalid' : '' }}"
                          name="gender">
                          <option data-display="@lang('common.gender') *" value="">@lang('common.gender') *</option>
                          @foreach ($genders as $gender)
                            <option value="{{ $gender->id }}" {{ old('gender') == $gender->id ? 'selected' : '' }}>
                              {{ $gender->base_setup_name }}</option>
                          @endforeach

                        </select>
                        <span class="focus-border"></span>
                        @if ($errors->has('gender'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('gender') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="row no-gutters input-right-icon">
                        <div class="col">
                          <div class="input-effect sm2_mb_20 md_mb_20">
                            <input class="primary-input" type="text" id="placeholderPhoto"
                              placeholder="@lang('common.student_photo')" readonly="">
                            <span class="focus-border"></span>

                            @if ($errors->has('file'))
                              <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ @$errors->first('file') }}</strong>
                              </span>
                            @endif

                          </div>
                        </div>
                        <div class="col-auto">
                          <button class="primary-btn-small-input" type="button">
                            <label class="primary-btn small fix-gr-bg" for="photo">@lang('common.browse')</label>
                            <input type="file" class="d-none" value="{{ old('photo') }}" name="photo"
                              id="photo">
                          </button>
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="row mb-40">
                    <div class="col-lg-7 text-right">
                      <div class="row">
                        <div class="col-lg-8 text-left" id="parent_info">
                          <input type="hidden" name="parent_id" value="">
                        </div>
                        <div class="col-lg-4">
                          <button class="primary-btn-small-input primary-btn small fix-gr-bg" type="button"
                            data-toggle="modal" data-target="#editStudent">
                            <span class="ti-plus pr-2"></span>
                            Add Parents </button>
                        </div>
                      </div>

                    </div>
                  </div>
                  <!-- Start Sibling Add Modal -->
                  <div class="modal fade admin-query" id="editStudent">
                    <div class="modal-dialog small-modal modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title">@lang('student.select_sibling')</h4>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                          <div class="container-fluid">
                            <form action="">
                              <div class="row">
                                <div class="col-lg-12">

                                  <div class="row">
                                    <div class="col-lg-12" id="sibling_required_error">

                                    </div>
                                  </div>
                                  <div class="row mt-25">
                                    <div class="col-lg-12" id="sibling_class_div">
                                      <select class="niceSelect w-100 bb" name="sibling_class"
                                        id="select_sibling_class">
                                        <option data-display="@lang('student.class') *" value="">@lang('student.class')
                                          *</option>
                                        {{-- @foreach ($classes as $class)
                                          <option value="{{ $class->id }}"
                                            {{ old('sibling_class') == $class->id ? 'selected' : '' }}>
                                            {{ $class->class_name }}</option>
                                        @endforeach --}}
                                      </select>
                                    </div>
                                  </div>

                                  <div class="row mt-25">
                                    <div class="col-lg-12" id="sibling_section_div">
                                      <select class="niceSelect w-100 bb" name="sibling_section"
                                        id="select_sibling_section">
                                        <option data-display="@lang('common.section') *" value="">@lang('common.section')
                                          *</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="row mt-25">
                                    <div class="col-lg-12" id="sibling_name_div">
                                      <select class="niceSelect w-100 bb" name="select_sibling_name"
                                        id="select_sibling_name">
                                        <option data-display="@lang('student.sibling') *" value="">@lang('student.sibling')
                                          *</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-lg-12 text-center mt-40">
                                  <div class="mt-40 d-flex justify-content-between">
                                    <button type="button" class="primary-btn tr-bg"
                                      data-dismiss="modal">@lang('common.cancel')</button>

                                    <button class="primary-btn fix-gr-bg" id="save_button_parent"
                                      data-dismiss="modal" type="button">@lang('common.save_information')</button>
                                  </div>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                  <!-- End Sibling Add Modal -->
                  <div class="parent_details" id="parent_details">

                    <div class="row mt-40">
                      <div class="col-lg-12">
                        <div class="main-title">
                          <h4 class="stu-sub-head">@lang('student.parents_and_guardian_info') </h4>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-30 mt-30">
                      <div class="col-lg-12">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <label for="relation_with_student">Relationship with student <span
                              class="text-danger">*</span></label>
                          <select
                            class="niceSelect w-100 bb form-control{{ $errors->has('relation_with_student') ? ' is-invalid' : '' }}"
                            name="relation_with_student" id="relation_with_student">
                            <option value="">
                              --select option--
                            </option>
                            @foreach (getRelations() as $item)
                              <option value="{{ $item }}"
                                {{ old('relation_with_student') == $item ? 'selected' : '' }}>
                                {{ ucfirst($item) }}
                              </option>
                            @endforeach

                          </select>
                          <span class="focus-border"></span>
                          @if ($errors->has('relation_with_student'))
                            <span class="invalid-feedback invalid-select" role="alert">
                              <strong>{{ $errors->first('relation_with_student') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                    </div>
                    {{-- father --}}
                    <div class="row mb-40 mt-30" id="father_details">
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input
                            class="primary-input form-control{{ $errors->has('fathers_name') ? ' is-invalid' : '' }}"
                            type="text" name="fathers_name" id="fathers_name" value="{{ old('fathers_name') }}">
                          <label>@lang('student.father_name') <span></span> </label>
                          <span class="focus-border"></span>
                          @if ($errors->has('fathers_name'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('fathers_name') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input class="primary-input" type="text" name="fathers_occupation"
                            id="fathers_occupation" value="{{ old('fathers_occupation') }}">
                          <label>@lang('student.occupation')</label>
                          <span class="focus-border"></span>
                          @if ($errors->has('fathers_occupation'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('fathers_occupation') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input oninput="phoneCheck(this)"
                            class="primary-input form-control{{ $errors->has('fathers_phone') ? ' is-invalid' : '' }}"
                            type="text" name="fathers_phone" id="fathers_phone"
                            value="{{ old('fathers_phone') }}">
                          <label>@lang('student.father_phone')</label>
                          <span class="focus-border"></span>
                          @if ($errors->has('fathers_phone'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('fathers_phone') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input oninput="emailCheck(this)"
                            class="primary-input form-control{{ $errors->has('fathers_email_address') ? ' is-invalid' : '' }}"
                            type="text" name="fathers_email_address" id="fathers_email_address"
                            value="{{ old('fathers_email_address') }}">
                          <label>@lang('common.email_address')</label>
                          <span class="focus-border"></span>
                          @if ($errors->has('fathers_email_address'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('fathers_email_address') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                    </div>
                    {{-- mother --}}
                    <div class="row mb-30" id="mother_details">
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input
                            class="primary-input form-control{{ $errors->has('mothers_name') ? ' is-invalid' : '' }}"
                            type="text" name="mothers_name" id="mothers_name"
                            value="{{ old('mothers_name') }}">
                          <label>@lang('student.mother_name') <span></span> </label>
                          <span class="focus-border"></span>
                          @if ($errors->has('mothers_name'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('mothers_name') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input class="primary-input" type="text" name="mothers_occupation"
                            id="mothers_occupation" value="{{ old('mothers_occupation') }}">
                          <label>@lang('student.occupation')</label>
                          <span class="focus-border"></span>
                          @if ($errors->has('mothers_occupation'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('mothers_occupation') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input oninput="phoneCheck(this)"
                            class="primary-input form-control{{ $errors->has('mothers_phone') ? ' is-invalid' : '' }}"
                            type="text" name="mothers_phone" id="mothers_phone"
                            value="{{ old('mothers_phone') }}">
                          <label>@lang('student.mother_phone')</label>
                          <span class="focus-border"></span>
                          @if ($errors->has('mothers_phone'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('mothers_phone') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input oninput="emailCheck(this)"
                            class="primary-input form-control{{ $errors->has('mothers_email_address') ? ' is-invalid' : '' }}"
                            type="text" name="mothers_email_address" id="mothers_email_address"
                            value="{{ old('mothers_email_address') }}">
                          <label>@lang('common.email_address')</label>
                          <span class="focus-border"></span>
                          @if ($errors->has('mothers_email_address'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('mothers_email_address') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                    </div>

                    {{-- <div class="row mb-40">
                      <div class="col-lg-12 d-flex flex-wrap">
                        <p class="text-uppercase fw-500 mb-10">@lang('student.relation_with_guardian')</p>
                        <div class="d-flex radio-btn-flex ml-40">
                          <div class="mr-30">
                            <input type="radio" name="relationButton" id="relationFather" value="F"
                              class="common-radio relationButton"
                              {{ old('relationButton') == 'F' ? 'checked' : '' }}>
                            <label for="relationFather">@lang('student.father')</label>
                          </div>
                          <div class="mr-30">
                            <input type="radio" name="relationButton" id="relationMother" value="M"
                              class="common-radio relationButton"
                              {{ old('relationButton') == 'M' ? 'checked' : '' }}>
                            <label for="relationMother">@lang('student.mother')</label>
                          </div>
                          <div>
                            <input type="radio" name="relationButton" id="relationOther" value="O"
                              class="common-radio relationButton"
                              {{ old('relationButton') != '' ? (old('relationButton') == 'O' ? 'checked' : '') : 'checked' }}>
                            <label for="relationOther">@lang('student.Other')</label>
                          </div>
                        </div>
                      </div>
                    </div> --}}
                    {{-- guardian  --}}
                    <div class="row mb-40" id="guardian_details">
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input
                            class="primary-input form-control{{ $errors->has('guardians_name') ? ' is-invalid' : '' }}"
                            type="text" name="guardians_name" id="guardians_name"
                            value="{{ old('guardians_name') }}">
                          <label>@lang('student.guardian_name') <span></span> </label>
                          <span class="focus-border"></span>
                          @if ($errors->has('guardians_name'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('guardians_name') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input class="primary-input" type="text" name="guardians_occupation"
                            id="guardians_occupation" value="{{ old('guardians_occupation') }}">
                          <label>@lang('student.guardian_occupation')</label>
                          <span class="focus-border"></span>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input
                            class="primary-input form-control{{ $errors->has('guardians_phone') ? ' is-invalid' : '' }}"
                            type="text" name="guardians_phone" id="guardians_phone"
                            value="{{ old('guardians_phone') }}">
                          <label>@lang('student.guardian_phone')</label>
                          <span class="focus-border"></span>
                        </div>
                      </div>
                      {{-- <div class="col-lg-4">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input class="primary-input read-only-input" type="text" placeholder="Relation"
                            name="relation" id="relation" value="Other" readonly>
                          <label>@lang('student.relation_with_guardian') </label>
                          <span class="focus-border"></span>
                        </div>
                      </div> --}}
                      <div class="col-lg-3">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input oninput="emailCheck(this)"
                            class="primary-input form-control{{ $errors->has('guardians_email') ? ' is-invalid' : '' }}"
                            type="text" name="guardians_email" id="guardians_email"
                            value="{{ old('guardians_email') }}">
                          <label>@lang('student.guardian_email')</span> </label>
                          <span class="focus-border"></span>
                          @if ($errors->has('guardians_email'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('guardians_email') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="row mb-40 mt-40">

                      <div class="col-lg-12">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <input class="primary-input" type="text" name="emergency_contact"
                            id="emergency_contact" value="{{ old('emergency_contact') }}">
                          <label>Emergency Phone Number <span class="text-danger">*</span> </label>
                          <span class="focus-border"></span>
                          @if ($errors->has('emergency_contact'))
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('guardians_email') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="row mb-40 mt-40">
                      {{-- <div class="col-lg-12">
                        <div class="input-effect sm2_mb_20 md_mb_20">
                          <textarea class="primary-input form-control" cols="0" rows="3" name="guardians_address"
                            id="guardians_address">{{ old('guardians_address') }}</textarea>
                          <label>@lang('student.guardian_address') <span></span> </label>
                          <span class="focus-border textarea"></span>
                          @if ($errors->has('guardians_address'))
                            <span class="invalid-feedback">
                              <strong>{{ $errors->first('guardians_address') }}</strong>
                            </span>
                          @endif
                        </div>
                      </div> --}}
                      <div class="col-lg-12 mb-40 mt-30 sm2_mb_20 md_mb_20">
                        <input type="checkbox" name="permission" id="permission" value="1"
                          class="common-checkbox relationButton"
                          {{ old('permission') != '' ? (old('permission') == '1' ? 'checked' : '') : 'checked' }}>
                        <label for="permission">I give permission for my child's picture to be used for class
                          projects/website
                          <!--<span class="text-danger">*</span>-->
                        </label>
                        @if ($errors->has('permission'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('permission') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>


                  <div class="row mt-40">
                    <div class="col-lg-12">
                      <div class="main-title">
                        <h4 class="stu-sub-head">@lang('student.student_address_info')</h4>
                      </div>
                    </div>
                  </div>

                  <div class="row mb-30 mt-30">
                    <div class="col-lg-6">

                      <div class="input-effect sm2_mb_20 md_mb_20 mt-20">
                        <textarea class="primary-input form-control{{ $errors->has('current_address') ? ' is-invalid' : '' }}"
                          cols="0" rows="3" name="current_address" id="current_address">{{ old('current_address') }}</textarea>
                        <label>@lang('student.current_address')<span class="text-danger">*</span> </label>
                        <span class="focus-border textarea"></span>
                        @if ($errors->has('current_address'))
                          <span class="invalid-feedback">
                            <strong>{{ $errors->first('current_address') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    <div class="col-lg-6">

                      <div class="input-effect sm2_mb_20 md_mb_20 mt-20">
                        <textarea class="primary-input form-control{{ $errors->has('current_address') ? ' is-invalid' : '' }}"
                          cols="0" rows="3" name="permanent_address" id="permanent_address">{{ old('permanent_address') }}</textarea>
                        <label>@lang('student.permanent_address') <span class="text-danger">*</span> </label>
                        <span class="focus-border textarea"></span>
                        @if ($errors->has('permanent_address'))
                          <span class="invalid-feedback">
                            <strong>{{ $errors->first('permanent_address') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>
                  {{-- Medical Details --}}
                  <div class="row mt-40">
                    <div class="col-lg-12 mb-40">
                      <div class="main-title">
                        <h4 class="stu-sub-head">Medical Details</h4>
                      </div>
                    </div>

                    {{-- Family doctor name --}}
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input
                          class="primary-input form-control{{ $errors->has('doctor_name') ? ' is-invalid' : '' }}"
                          type="text" name="doctor_name" value="{{ old('doctor_name') }}">
                        <label>Family Doctor Name <span>(optional)</span> </label>
                        <span class="focus-border"></span>
                        @if ($errors->has('doctor_name'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('doctor_name') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    {{-- Family  doctor tel --}}
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input
                          class="primary-input form-control{{ $errors->has('doctor_phone') ? ' is-invalid' : '' }}"
                          type="text" name="doctor_phone" value="{{ old('doctor_phone') }}">
                        <label>Family Health Issurance <span>(optional)</span> </label>
                        <span class="focus-border"></span>
                        @if ($errors->has('doctor_phone'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('doctor_phone') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    {{-- family health issurance --}}
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input
                          class="primary-input form-control{{ $errors->has('family_health_ins') ? ' is-invalid' : '' }}"
                          type="text" name="family_health_ins" value="{{ old('family_health_ins') }}">
                        <label>Family Doctor Phone <span>(optional)</span> </label>
                        <span class="focus-border"></span>
                        @if ($errors->has('family_health_ins'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('family_health_ins') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    <div class="col-lg-8 mt-40">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <label for="health_condition">Health Conditions <span class="text-danger">*</span></label>
                        <select multiple
                          class="niceSelect w-100 bb form-control{{ $errors->has('health_condition') ? ' is-invalid' : '' }}"
                          name="health_condition" id="health_condition">

                          <option data-display="" value="">Select Health</option>
                          @foreach (healthCondition() as $list)
                            <option value="{{ $list }}" {{ old('list') == $list ? 'selected' : '' }}>
                              {{ $list }}
                            </option>
                          @endforeach
                        </select>
                        <span class="focus-border"></span>
                        @if ($errors->has('health_condition'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('health_condition') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    <div class="col-lg-4 mt-60">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <select
                          class="niceSelect w-100 bb form-control{{ $errors->has('blood_group') ? ' is-invalid' : '' }}"
                          name="blood_group">
                          <option data-display="@lang('common.blood_group')" value="">@lang('common.blood_group')</option>
                          @foreach ($blood_groups as $blood_group)
                            <option value="{{ $blood_group->id }}"
                              {{ old('blood_group') == $blood_group->id ? 'selected' : '' }}>
                              {{ $blood_group->base_setup_name }}</option>
                          @endforeach
                        </select>
                        <span class="focus-border"></span>
                        @if ($errors->has('blood_group'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('blood_group') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="row mb-40 mt-40">
                    <div class="col-lg-12 d-flex align-items-center flex-wrap">
                      <p class="text-uppercase fw-500 mb-10">Is Your Child On Any Prescribed Medication? <span
                          class="text-danger">*</span></p>
                      <div class="d-flex radio-btn-flex ml-30">
                        <div class="mr-30">
                          <input type="radio" name="prescribed_medication" id="p_yes" value="p_yes"
                            {{ old('prescribed_medication') == 'p_yes' ? 'checked' : '' }}
                            class="common-radio prescribed_medication">
                          <label for="p_yes">Yes</label>
                        </div>
                        <div class="mr-30">
                          <input type="radio" name="prescribed_medication" id="p_no" value="p_no"
                            {{ old('prescribed_medication') != '' ? (old('prescribed_medication') == 'p_no' ? 'checked' : '') : 'checked' }}
                            class="common-radio prescribed_medication">
                          <label for="p_no">No</label>
                        </div>
                      </div>
                      @if ($errors->has('prescribed_medication'))
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $errors->first('prescribed_medication') }}</strong>
                        </span>
                      @endif
                    </div>

                  </div>
                  <div class="row mb-40 mt-40">
                    <div class="col-lg-12 d-flex align-items-center flex-wrap">
                      <p class="text-uppercase fw-500 mb-10">Is the School allowed to administer the
                        medication?</p>
                      <div class="d-flex radio-btn-flex ml-30">
                        <div class="mr-30">
                          <input type="radio" name="allowed_medical" id="a_yes" value="a_yes"
                            {{ old('allowed_medical') == 'a_yes' ? 'checked' : '' }} checked
                            class="common-radio allowed_medical">
                          <label for="a_yes">Yes</label>
                        </div>
                        <div class="mr-30">
                          <input type="radio" name="allowed_medical" id="a_no" value="a_no"
                            {{ old('allowed_medical') == 'a_no' ? 'checked' : '' }}
                            class="common-radio allowed_medical">
                          <label for="a_no">No</label>
                        </div>
                      </div>
                      @if ($errors->has('allowed_medical'))
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $errors->first('allowed_medical') }}</strong>
                        </span>
                      @endif
                    </div>
                  </div>
                  <div class="row mb-40 mt-40">
                    <div class="col-lg-12">
                      <div class="input-effect sm2_mb_20 md_mb_20 mt-20">
                        <textarea class="primary-input form-control{{ $errors->has('condition_info') ? ' is-invalid' : '' }}" cols="0"
                          rows="3" name="condition_info" id="condition_info">{{ old('condition_info') }}</textarea>
                        <label>if there is any other information regarding your child's well being
                          <span>(optional)</span>
                        </label>
                        <span class="focus-border textarea"></span>
                        @if ($errors->has('condition_info'))
                          <span class="invalid-feedback">
                            <strong>{{ $errors->first('condition_info') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>

                  {{-- <div class="row mt-40">
                    <div class="col-lg-12">
                      <div class="main-title">
                        <h4 class="stu-sub-head">@lang('student.transport')</h4>
                      </div>
                    </div>
                  </div>

                  <div class="row mb-40 mt-30">
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <select
                          class="niceSelect w-100 bb form-control{{ $errors->has('route') ? ' is-invalid' : '' }}"
                          name="route" id="route">
                          <option data-display="@lang('student.route_list')" value="">@lang('student.route_list')</option>
                          @foreach ($route_lists as $route_list)
                            <option value="{{ $route_list->id }}"
                              {{ old('route') == $route_list->id ? 'selected' : '' }}>{{ $route_list->title }}
                            </option>
                          @endforeach
                        </select>
                        <span class="focus-border"></span>
                        @if ($errors->has('route'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('route') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20" id="select_vehicle_div">
                        <select
                          class="niceSelect w-100 bb form-control{{ $errors->has('vehicle') ? ' is-invalid' : '' }}"
                          name="vehicle" id="selectVehicle">
                          <option data-display="@lang('student.vehicle_number')" value="">@lang('student.vehicle_number')</option>
                        </select>
                        <div class="pull-right loader loader_style" id="select_transport_loader">
                          <img class="loader_img_style" src="{{ asset('public/backEnd/img/demo_wait.gif') }}"
                            alt="loader">
                        </div>
                        <span class="focus-border"></span>
                        @if ($errors->has('vehicle'))
                          <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('vehicle') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="input-effect sm2_mb_20 md_mb_20">
                        <input class="primary-input form-control{{ $errors->has('landmark') ? ' is-invalid' : '' }}"
                          type="text" name="landmark" value="{{ old('landmark') }}">
                        <label>Landmark <span>(optional)</span> </label>
                        <span class="focus-border"></span>
                        @if ($errors->has('landmark'))
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('landmark') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                  </div> --}}

                  <div class="row mb-40 mt-30">
                    <div class="col-lg-12 sm2_mb_20 md_mb_20">
                      <input type="checkbox" name="terms" id="terms" value="1"
                        class="common-checkbox relationButton"
                        {{ old('terms') != '' ? (old('terms') == '1' ? 'checked' : '') : 'checked' }}>
                      <label for="terms">TERMS AND CONDITIONS: I have read the school's terms and conditions
                        surrounding the admission which I seek for my child at Flourish Court School. I shall endeavour
                        to comply.</label>
                      @if ($errors->has('terms'))
                        <span class="invalid-feedback">
                          <strong>{{ $errors->first('terms') }}</strong>
                        </span>
                      @endif
                    </div>
                  </div>


                  {{-- Custom Filed Start --}}

                  {{-- Custom Filed End --}}
                  <div class="row mt-40">
                    <div class="col-lg-12 text-center">
                      <button class="primary-btn fix-gr-bg submit" id="_submit_btn_admission" data-toggle="tooltip"
                        title="">
                        <span class="ti-check"></span>
                        Submit Form
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{ Form::close() }}
        </div>
      </section>
      {{-- student photo --}}
      <input type="text" id="STurl" value="{{ route('student_admission_pic') }}" hidden>
      <div class="modal" id="LogoPic">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">@lang('student.crop_image_and_upload')</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
              <div id="resize"></div>
              <button class="btn rotate float-lef" data-deg="90">
                <i class="ti-back-right"></i></button>
              <button class="btn rotate float-right" data-deg="-90">
                <i class="ti-back-left"></i></button>
              <hr>

              <a href="javascript:;" class="primary-btn fix-gr-bg pull-right" id="upload_logo">@lang('student.crop')</a>
            </div>
          </div>
        </div>
      </div>
      {{-- end student photo --}}

      {{-- father photo --}}

      <div class="modal" id="FatherPic">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">@lang('student.crop_image_and_upload')</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
              <div id="fa_resize"></div>
              <button class="btn rotate float-lef" data-deg="90">
                <i class="ti-back-right"></i></button>
              <button class="btn rotate float-right" data-deg="-90">
                <i class="ti-back-left"></i></button>
              <hr>

              <a href="javascript:;" class="primary-btn fix-gr-bg pull-right"
                id="FatherPic_logo">@lang('student.crop')</a>
            </div>
          </div>
        </div>
      </div>
      {{-- end father photo --}}
      {{-- mother photo --}}

      <div class="modal" id="MotherPic">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Crop Image And Upload</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
              <div id="ma_resize"></div>
              <button class="btn rotate float-lef" data-deg="90">
                <i class="ti-back-right"></i></button>
              <button class="btn rotate float-right" data-deg="-90">
                <i class="ti-back-left"></i></button>
              <hr>

              <a href="javascript:;" class="primary-btn fix-gr-bg pull-right" id="Mother_logo">Crop</a>
            </div>
          </div>
        </div>
      </div>
      {{-- end mother photo --}}
      {{-- mother photo --}}

      <div class="modal" id="GurdianPic">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Crop Image And Upload</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
              <div id="Gu_resize"></div>
              <button class="btn rotate float-lef" data-deg="90">
                <i class="ti-back-right"></i></button>
              <button class="btn rotate float-right" data-deg="-90">
                <i class="ti-back-left"></i></button>
              <hr>
              <a href="javascript:;" class="primary-btn fix-gr-bg pull-right" id="Gurdian_logo">Crop</a>
            </div>
          </div>
        </div>
      </div>
      {{-- end mother photo --}}
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

  <!-- ================End Footer Area ================= -->
  <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery-ui.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery.data-tables.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/dataTables.buttons.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/buttons.flash.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/jszip.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/pdfmake.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/vfs_fonts.js"></script>
  <script src="{{ asset('public/backEnd/js/vfs_fonts.js') }}"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/buttons.html5.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/buttons.print.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/dataTables.rowReorder.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/dataTables.responsive.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/buttons.colVis.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/popper.js"></script>
  {{-- <script src="{{asset('public/backEnd/')}}/vendors/js/bootstrap.min.js"> --}}
  {{-- </script> --}}
  <script src="{{ asset('public/backEnd/') }}/css/rtl/bootstrap.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/nice-select.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/jquery.magnific-popup.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/fastselect.standalone.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/raphael-min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/morris.min.js"></script>
  <script type="text/javascript" src="{{ asset('public/backEnd/') }}/vendors/js/toastr.min.js"></script>
  <script type="text/javascript" src="{{ asset('public/backEnd/') }}/vendors/js/moment.min.js"></script>
  <script src="{{ asset('public/backEnd/vendors/editor/ckeditor/ckeditor.js') }}"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/bootstrap_datetimepicker.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/bootstrap-datepicker.min.js"></script>
  <script type="text/javascript" src="{{ asset('public/backEnd/') }}/vendors/js/fullcalendar.min.js"></script>
  <script src="{{ asset('public/backEnd/vendors/js/fullcalendar-locale-all.js') }}"></script>
  <script type="text/javascript" src="{{ asset('public/backEnd/') }}/js/jquery.validate.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/select2/select2.min.js"></script>
  <script src="{{ asset('public/backEnd/') }}/js/main.js"></script>
  <script src="{{ asset('public/backEnd/') }}/js/lesson_plan.js"></script>
  {{-- <script src="{{ asset('public/backEnd/') }}/js/custom.js"></script> --}}
  <script src="{{ asset('public/') }}/js/registration_custom.js"></script>
  <script src="{{ asset('public/backEnd/') }}/js/developer.js"></script>
  <script src="{{ asset('public/backEnd/') }}/vendors/js/daterangepicker.min.js"></script>

  <script src="{{ asset('public/backEnd/') }}/vendors/editor/summernote-bs4.js"></script>
  <script src="{{ url('Modules\Wallet\Resources\assets\js\wallet.js') }}"></script>

  <script src="{{ asset('public/backEnd/') }}/js/lesson_plan.js"></script>
  {{-- <script src="{{asset('public/backEnd/')}}/saas/js1/custom.js"></script> --}}
  <script src="{{ asset('public/backEnd/') }}/js/search.js"></script>
  {{-- <script src="{{asset('public/landing/js/toastr.js')}}"></script> --}}

  {!! Toastr::message() !!}

  {{-- @if (request()->route()->getPrefix() == '/chat') --}}
  <script src="{{ asset('public/js/app.js') }}"></script>
  {{-- <script src="{{ asset('public/chat/js/custom.js') }}"></script> --}}
  {{-- @endif --}}

  {{-- <script src="{{asset('Modules/Saas/Resources/assets/saas/')}}/js/main.js"></script> --}}
  {{-- <script src="{{asset('Modules/Saas/Resources/assets/saas/')}}/js/saas.js"></script> --}}
  {{-- <script src="{{asset('Modules/Saas/Resources/assets/saas/')}}/js/developer.js"></script>
  <script src="{{asset('Modules/Saas/Resources/assets/saas/')}}/js/search.js"></script> --}}
  @yield('script')
  @stack('script')
  @stack('scripts')
  <script src="{{ asset('public/backEnd/') }}/js/croppie.js"></script>
  <script src="{{ asset('public/backEnd/') }}/js/st_addmision.js"></script>
  <script>
    $(document).ready(function() {

      $(document).on('change', '.cutom-photo', function() {
        let v = $(this).val();
        let v1 = $(this).data("id");
        console.log(v, v1);
        getFileName(v, v1);

      });

      const prescribed_medication = $('input[name=prescribed_medication]');
      const prescribed_medication_allow = $('.allowed_medical');

      prescribed_medication_allow.closest('.row').hide()

      $(document).on('change', '.prescribed_medication', function() {
        let value = $(this).val();
        console.log(value);
        if (value == "p_yes") {
          prescribed_medication_allow.closest('.row').fadeIn()
        } else {
          prescribed_medication_allow.closest('.row').hide()
        }
      })

      function getFileName(value, placeholder) {
        if (value) {
          var startIndex = (value.indexOf('\\') >= 0 ? value.lastIndexOf('\\') : value.lastIndexOf('/'));
          var filename = value.substring(startIndex);
          if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
            filename = filename.substring(1);
          }
          $(placeholder).attr('placeholder', '');
          $(placeholder).attr('placeholder', filename);
        }
      }

      // select class
      $(document).ready(function() {
        $("#academic_year").on("change", function() {
          var url = $("#url").val();
          var formData = {
            year: $(this).val(),
          };

          // get section for student
          $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/admission/" + "ajax-get-class",
            beforeSend: function() {
              $('#select_class_loader').addClass('pre_loader');
              $('#select_class_loader').removeClass('loader');
            },
            success: function(data) {
              console.log(data);
              $('#select_class_loader').addClass('loader');
              $('#select_class_loader').removeClass('pre_loader');
              $('#classSelectStudent')
                .empty()
                .append(
                  $('<option>', {
                    value: '',
                    text: 'select class *',
                  })
                );

              if (data['classes'].length) {
                $.each(data['classes'], function(i, className) {
                  $('#classSelectStudent').append(
                    $('<option>', {
                      value: className.id,
                      text: className.class_name,
                    })
                  );

                  $('#select_sibling_class').append(
                    $('<option>', {
                      value: className.id,
                      text: className.class_name,
                    })
                  );
                });
              }

              $('#classSelectStudent').niceSelect('update');
              $('#classSelectStudent').trigger('change');

              $('#select_sibling_class').niceSelect('update');
              $('#select_sibling_class').trigger('change');

            },
            error: function(data) {
              console.log("Error:", data);
            },
          });
        });
      });

      $(document).ready(function() {
        $("#classSelectStudent").on("change", function() {
          var url = $("#url").val();
          var formData = {
            id: $(this).val(),
          };

          // get section for student
          $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/admission/" + "ajax-get-section",
            beforeSend: function() {
              $('#select_section_loader').addClass('pre_loader');
              $('#select_section_loader').removeClass('loader');
            },
            success: function(data) {
              console.log(data);

              $('#select_section_loader').addClass('loader');
              $('#select_section_loader').removeClass('pre_loader');
              $('#sectionSelectStudent')
                .empty()
                .append(
                  $('<option>', {
                    value: '',
                    text: 'select class *',
                  })
                );

              if (data[0].length) {
                $.each(data[0], function(i, sectionName) {
                  $('#sectionSelectStudent').append(
                    $('<option>', {
                      value: sectionName.id,
                      text: sectionName.section_name,
                    })
                  );
                });
              }
              $('#sectionSelectStudent').niceSelect('update');
              $('#sectionSelectStudent').trigger('change');
            },
            error: function(data) {
              console.log("Error:", data);
            },
          });
        });
      });

      // 
      $(document).ready(function() {
        $('#route').on('change', function() {
          var url = $('#url').val();
          var i = 0;
          if ($(this).val() == '') {
            $('#select_vehicle_div .current').html('SELECT VEHICLE');
            $('#selectVehicle').find('option').not(':first').remove();
            $('#select_vehicle_div ul').find('li').not(':first').remove();
            return false;
          }

          var formData = {
            id: $(this).val(),
          };
          // get section for student
          $.ajax({
            type: 'GET',
            data: formData,
            dataType: 'json',
            url: url + '/admission/' + 'ajax-get-vehicle',
            beforeSend: function() {
              $('#select_transport_loader').addClass('pre_loader');
              $('#select_transport_loader').removeClass('loader');
            },
            success: function(data) {
              console.log(data);
              var a = '';
              $.each(data, function(i, item) {
                if (item.length) {
                  $('#selectVehicle').find('option').not(':first').remove();
                  $('#select_vehicle_div ul').find('li').not(':first').remove();

                  $.each(item, function(i, vehicle) {
                    $('#selectVehicle').append(
                      $('<option>', {
                        value: vehicle.id,
                        text: vehicle.vehicle_no,
                      })
                    );

                    $('#select_vehicle_div ul').append(
                      "<li data-value='" +
                      vehicle.id +
                      "' class='option'>" +
                      vehicle.vehicle_no +
                      '</li>'
                    );
                  });
                } else {
                  $('#select_vehicle_div .current').html('SELECT VEHICLE');
                  $('#selectVehicle').find('option').not(':first').remove();
                  $('#select_vehicle_div ul').find('li').not(':first').remove();
                }
              });
            },
            error: function(data) {
              console.log('Error:', data);
            },
            complete: function() {
              i--;
              if (i <= 0) {
                $('#select_transport_loader').removeClass('pre_loader');
                $('#select_transport_loader').addClass('loader');
              }
            },
          });
        });
      });

      // student section select sction for sibling
      $(document).ready(function() {
        $('#select_sibling_class').on('change', function() {
          var url = $('#url').val();
          var formData = {
            id: $(this).val(),
            academic_year: $('#academic_year').val()
          };
          // get section for student
          $.ajax({
            type: 'GET',
            data: formData,
            dataType: 'json',
            url: url + '/admission/' + 'ajaxSectionSibling',
            success: function(data) {
              var a = '';
              $.each(data, function(i, item) {
                if (item.length) {
                  $('#select_sibling_section')
                    .find('option')
                    .not(':first')
                    .remove();
                  $('#sibling_section_div ul').find('li').not(':first').remove();

                  $.each(item, function(i, section) {
                    $('#select_sibling_section').append(
                      $('<option>', {
                        value: section.id,
                        text: section.section_name,
                      })
                    );

                    $('#sibling_section_div ul').append(
                      "<li data-value='" +
                      section.id +
                      "' class='option'>" +
                      section.section_name +
                      '</li>'
                    );
                  });
                } else {
                  $('#sibling_section_div .current').html('SECTION *');
                  $('#select_sibling_section')
                    .find('option')
                    .not(':first')
                    .remove();
                  $('#sibling_section_div ul').find('li').not(':first').remove();
                }
              });
              console.log(a);
            },
            error: function(data) {
              console.log('Error:', data);
            },
          });
        });
      });

      // student section sibling info get
      $(document).ready(function() {
        $('#select_sibling_section').on('change', function() {
          var url = $('#url').val();
          var id = $('#id').val();

          if (typeof id === 'undefined') {
            id = '';
          } else {
            id = id;
          }

          var formData = {
            id: id,
            section_id: $(this).val(),
            class_id: $('#select_sibling_class').val(),
            academic_year: $('#academic_year').val()
          };
          // get section for student
          $.ajax({
            type: 'GET',
            data: formData,
            dataType: 'json',
            url: url + '/admission/' + 'ajaxSiblingInfo',
            success: function(data) {
              console.log(data);
              if (data.length) {
                $('#select_sibling_name').find('option').not(':first').remove();
                $('#sibling_name_div ul').find('li').not(':first').remove();

                $.each(data, function(i, sibling) {
                  $('#select_sibling_name').append(
                    $('<option>', {
                      value: sibling.id,
                      text: sibling.first_name + ' ' + sibling.last_name,
                    })
                  );

                  $('#sibling_name_div ul').append(
                    "<li data-value='" +
                    sibling.id +
                    "' class='option'>" +
                    sibling.first_name +
                    ' ' +
                    sibling.last_name +
                    '</li>'
                  );
                });
              } else {
                $('#sibling_name_div .current').html('Student *');
                $('#select_sibling_name').find('option').not(':first').remove();
                $('#sibling_name_div ul').find('li').not(':first').remove();
              }
            },
            error: function(data) {
              // console.log("Error:", data);
            },
          });
        });
      });

      // student section sibling info get detail
      $(document).ready(function() {
        $('#save_button_parent').on('click', function() {
          var select_sibling_name = $('#select_sibling_name').val();
          if (select_sibling_name == '') {
            $('#sibling_required_error div').remove();
            $('#sibling_required_error').append(
              "<div class='alert alert-danger'>No sibling Selected</div>"
            );
            return false;
          } else {
            $('#sibling_required_error div').remove();
          }

          var url = $('#url').val();

          var formData = {
            id: $('#select_sibling_name').val(),
          };
          // get section for student
          $.ajax({
            type: 'GET',
            data: formData,
            dataType: 'json',
            url: url + '/admission/' + 'ajaxSiblingInfoDetail',
            success: function(data) {
              console.log(data);
              var fathers_name = (data[1].fathers_name) ? data[1].fathers_name : 'not set';
              var parent_id = data[0].parent_id;

              var mothers_name = (data[1].mothers_name) ? data[1].mothers_name : 'not set';
              var guardians_name = (data[1].guardians_name) ? data[1].guardians_name : 'not set';


              $('#parent_info div').remove();
              $('#parent_info').append(
                "<div class='alert alert-success primary-btn small parent_remove' id='parent_remove'>×<strong> Guardian: " +
                guardians_name +
                ', father: ' +
                fathers_name +
                ', mother: ' +
                mothers_name +
                '</strong></div>'
              );
              $('#parent_info input').val(parent_id);
              $('#parent_details').fadeOut();

              // if($("#sibling_id").val() != 0){
              //     $("#sibling_id").val(2);
              // }
            },
            error: function(data) {
              // console.log("Error:", data);
            },
          });
        });
      });

      $(document).ready(function() {

        const fatherDetails = $("#father_details");
        const motherDetails = $("#mother_details");
        const guardianDetails = $("#guardian_details");

        fatherDetails.hide();
        motherDetails.hide();
        guardianDetails.hide();

        $('#relation_with_student').on('change', function() {
          let value = $(this).val();
          if (value == "parent") {
            fatherDetails.fadeIn()
            motherDetails.fadeIn()
            guardianDetails.hide();
          } else if (value == "father") {
            fatherDetails.fadeIn()
            motherDetails.hide();
            guardianDetails.hide();
          } else if (value == "mother") {
            motherDetails.fadeIn()
            fatherDetails.hide();
            guardianDetails.hide();
          } else if (value == "guardian") {
            guardianDetails.fadeIn();
            motherDetails.hide()
            fatherDetails.hide();
          } else {
            fatherDetails.hide();
            motherDetails.hide();
            guardianDetails.hide();
          }
        })


        $('.relationButton').on('click', function() {
          if ($(this).val() == 'F') {
            $('#guardians_name').val($('#fathers_name').val());
            $('#guardians_occupation').val($('#fathers_occupation').val());
            $('#guardians_phone').val($('#fathers_phone').val());
            $('#guardians_email').val($('#fathers_email_address').val());
            $('#relation').val('Father');

          } else if ($(this).val() == 'M') {
            $('#guardians_name').val($('#mothers_name').val());
            $('#guardians_occupation').val($('#mothers_occupation').val());
            $('#guardians_phone').val($('#mothers_phone').val());
            $('#guardians_email').val($('#mothers_email_address').val());
            $('#relation').val('Mother');

          } else {
            $('#guardians_name').val('');
            $('#guardians_occupation').val('');
            $('#guardians_phone').val('');
            $('#guardians_email').val('');
            $('#relation').val('Other');
          }

          if ($('#guardians_name').val() != '') {
            $('#guardians_name').focus();
          } else {
            $('#guardians_name').focusout();
          }

          if ($('#guardians_occupation').val() != '') {
            $('#guardians_occupation').focus();
          } else {
            $('#guardians_occupation').focusout();
          }

          if ($('#guardians_phone').val() != '') {
            $('#guardians_phone').focus();
          } else {
            $('#guardians_phone').focusout();
          }

          if ($('#guardians_email').val() != '') {
            $('#guardians_email').focus();
          } else {
            $('#guardians_email').focusout();
          }
        });
      });

    })
  </script>
</body>

</html>
