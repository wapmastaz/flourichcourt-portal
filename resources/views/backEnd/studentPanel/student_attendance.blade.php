@extends('backEnd.master')
@section('title')
@lang('student.attendance')
@endsection

@section('mainContent')
<style>
    th{
        padding: .5rem !important;
        font-size: 10px !important;
    }
    td{
        padding: .3rem !important;
        font-size: 9px !important;  
    }
</style>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('student.attendance')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="{{route('student_my_attendance')}}">@lang('student.attendance')</a>
            </div>
        </div>
    </div>
</section>
@php
    $year=date('Y');
     
@endphp
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('common.select_criteria')</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                        @if(session()->has('message-success'))
                        <div class="alert alert-success">
                            {{ session()->get('message-success') }}
                        </div>
                        @elseif(session()->has('message-danger'))
                        <div class="alert alert-danger">
                            {{ session()->get('message-danger') }}
                        </div>
                        @endif
                    <div class="white-box">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student_my_attendance', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                
                                
                                <div class="col-lg-6 mt-30-md">
                                    <select class="w-100 niceSelect bb form-control{{ $errors->has('month') ? ' is-invalid' : '' }}" name="month">
                                        <option data-display="Select Month *" value="">@lang('student.select_month') *</option>
                                        <option value="01" {{isset($month)? ( @$month == "01"? 'selected': ''): ''}}>@lang('student.january')</option>
                                        <option value="02" {{isset($month)? ( @$month == "02"? 'selected': ''): ''}}>@lang('student.february')</option>
                                        <option value="03" {{isset($month)? ( @$month == "03"? 'selected': ''): ''}}>@lang('student.march')</option>
                                        <option value="04" {{isset($month)? ( @$month == "04"? 'selected': ''): ''}}>@lang('student.april')</option>
                                        <option value="05" {{isset($month)? ( @$month == "05"? 'selected': ''): ''}}>@lang('student.may')</option>
                                        <option value="06" {{isset($month)? ( @$month == "06"? 'selected': ''): ''}}>@lang('student.june')</option>
                                        <option value="07" {{isset($month)? ( @$month == "07"? 'selected': ''): ''}}>@lang('student.july')</option>
                                        <option value="08" {{isset($month)? ( @$month == "08"? 'selected': ''): ''}}>@lang('student.august')</option>
                                        <option value="09" {{isset($month)? ( @$month == "09"? 'selected': ''): ''}}>@lang('student.september')</option>
                                        <option value="10" {{isset($month)? ( @$month == "10"? 'selected': ''): ''}}>@lang('student.october')</option>
                                        <option value="11" {{isset($month)? ( @$month == "11"? 'selected': ''): ''}}>@lang('student.november')</option>
                                        <option value="12" {{isset($month)? ( @$month == "12"? 'selected': ''): ''}}>@lang('student.december')</option>
                                    </select>
                                    @if ($errors->has('month'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('month') }}</strong>
                                    </span>
                                    @endif
                                </div>
                              
                                <div class="col-lg-6 mt-30-md">
                                    <select class="w-100 bb niceSelect form-control{{ $errors->has('year') ? ' is-invalid' : '' }}" name="year">
                                        <option data-display="Select Year *" value="">@lang('student.select_year') *</option>
                                        @foreach (academicYears() as $academic_year)

                                             <option value="{{ @$academic_year->year }}" {{isset($year)? (@$year == @$academic_year->year? 'selected': ''): ''}}>{{ @$academic_year->year }}</option>
                                             

                                        @endforeach
                                       
                                    </select>
                                    @if ($errors->has('year'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span>
                                        search
                                    </button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>


    </div>
</section>


@if(isset($attendances))

<section class="student-attendance">
    <div class="container-fluid p-0">
        <div class="row mt-40">
            <div class="col-lg-12 mb-40">
                <a href="{{route('my-attendance/print', [@$month, @$year])}}" class="primary-btn small fix-gr-bg pull-right" target="_blank"><i class="ti-printer"> </i> @lang('common.print')</a>
            </div>
        </div>
        <div class="row ">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-0">@lang('student.attendance_result')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive pt-30">
                    <div id="table_id_student_wrapper" class="dataTables_wrapper no-footer">
                <table id="table_id_student" class="display school-table dataTable no-footer pt-5" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th width="3%">P</th>
                            <th width="3%">L</th>
                            <th width="3%">A</th>
                            <th width="3%">H</th>
                            <th width="3%">F</th>
                            <th width="2%">%</th>
                            @for($i = 1;  $i<=@$days; $i++)
                            <th width="3%" class="{{($i<=18)? 'all':'none'}}">
                                {{$i}} <br>
                                @php
                                    @$date = @$year.'-'.@$month.'-'.$i;
                                    @$day = date("D", strtotime(@$date));
                                    echo @$day;
                                @endphp
                            </th>
                            @endfor
                        </tr>
                    </thead>
                    
                    <tbody>
                        @php @$total_attendance = 0; @endphp
                        @php @$count_absent = 0; @endphp
                        <tr>
                            <td>
                                @php $p = 0; @endphp
                                @foreach($attendances as $value)
                                    @if(@$value->attendance_type == 'P')
                                        @php $p++; @$total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$p}}
                            </td>
                            <td>
                                @php $l = 0; @endphp
                                @foreach($attendances as $value)
                                    @if(@$value->attendance_type == 'L')
                                        @php $l++; @$total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$l}}
                            </td>
                            <td>
                                @php $a = 0; @endphp
                                @foreach($attendances as $value)
                                    @if(@$value->attendance_type == 'A')
                                        @php $a++; @$count_absent++; @$total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$a}}
                            </td>
                            <td>
                                @php $h = 0; @endphp
                                @foreach($attendances as $value)
                                    @if(@$value->attendance_type == 'H')
                                        @php $h++; @$total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$h}}
                            </td>
                            <td>
                                @php $f = 0; @endphp
                                @foreach($attendances as $value)
                                    @if(@$value->attendance_type == 'F')
                                        @php $f++; @$total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$f}}
                            </td>
                            <td>  
                               @php
                                 @$total_present = @$total_attendance - @$count_absent;
                                 if(@$count_absent == 0){
                                     echo '100%';
                                 }else{
                                     @$percentage = @$total_present / @$total_attendance * 100;
                                     echo number_format((float)@$percentage, 2, '.', '').'%';
                                 }
                               @endphp

                            </td>
                            @for($i = 1;  $i<=@$days; $i++)
                                @php
                                    @$date = @$year.'-'.@$month.'-'.$i;
                                @endphp
                                <td width="3%" class="{{($i<=18)? 'all':'none'}}">
                                    @foreach($attendances as $value)
                                        @if(strtotime(@$value->attendance_date) == strtotime(@$date))
                                            {{@$value->attendance_type}}
                                        @endif
                                    @endforeach
                                </td>
                               
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
            </div>
        </div>
    </div>
</section>
@endif


@endsection
