@extends('backEnd.master')
@section('title')
  @lang('exam.active_exams')
@endsection
@section('mainContent')

  <section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
      <div class="row justify-content-between">
        <h1>@lang('exam.online_exam') </h1>
        <div class="bc-pages">
          <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
          <a href="#">@lang('exam.online_exam')</a>
          <a href="{{ route('student_online_exam') }}">@lang('exam.active_exams')</a>
        </div>
      </div>
    </div>
  </section>
  <section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
      <div class="row">

        <div class="col-lg-12">
          <div class="row">
            <div class="col-lg-4 no-gutters">
              <div class="main-title">
                <h3 class="mb-0">@lang('exam.online_active_exams')</h3>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">

              <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                <thead>
                  <tr>
                    <th>@lang('exam.title')</th>
                    <th>@lang('common.class_Sec')</th>
                    <th>@lang('exam.subject')</th>
                    <th>@lang('exam.exam_date')</th>
                    <th>@lang('exam.duration')</th>
                    {{-- <th>@lang('common.status')</th> --}}
                    <th>@lang('common.action')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($online_exams as $online_exam)
                    @php
                      @$submitted_answer = $student->studentOnlineExam->where('online_exam_id', $online_exam->id)->first();
                      
                    @endphp
                    @if (!in_array(@$online_exam->id, @$marks_assigned))
                      <tr>
                        <td>{{ @$online_exam->title }}</td>
                        <td>{{ @$online_exam->class->class_name . '  (' . @$online_exam->section->section_name . ')' }}</td>
                        <td>{{ @$online_exam->subject != '' ? @$online_exam->subject->subject_name : '' }}</td>
                        <td data-sort="{{ strtotime(@$online_exam->date) }}">
                          {{ @$online_exam->date != '' ? dateConvert(@$online_exam->date) : '' }}

                          <br>
                          Time:
                          {{ date('h:i A', strtotime(@$online_exam->start_time)) . ' - ' . date('h:i A', strtotime(@$online_exam->end_time)) }}
                        </td>
                        @php
                          
                          $totalDuration = $online_exam->end_time != 'NULL' ? Carbon::parse($online_exam->end_time)->diffinminutes(Carbon::parse($online_exam->start_time)) : 0;
                          
                        @endphp
                        <td>
                          {{ $online_exam->end_time != 'NULL' ? gmdate($totalDuration) : 'Unlimited' }} @lang('exam.minutes')
                        </td>
                        <td>
                          {{ $online_exam->total_durations }} @lang('exam.minutes')
                        </td>

                        <td>
                          @php
                            $startTime = strtotime($online_exam->date . ' ' . $online_exam->start_time);
                            $endTime = strtotime($online_exam->date . ' ' . $online_exam->end_time);
                            $now = date('h:i:s');
                            $now = strtotime('now');
                          @endphp
                          @if (!empty($submitted_answer))
                            @if (@$submitted_answer->status == 1)
                              @if ($submitted_answer->student_done == 1)
                                <span class="btn primary-btn small  fix-gr-bg"
                                  style="background:green">@lang('exam.already_submitted')</span>
                              @elseif($startTime <= $now && $now <= $endTime)
                                <a class="btn primary-btn small  fix-gr-bg" style="background:green"
                                  href="{{ route('take_online_exam', [@$online_exam->id]) }}">@lang('exam.take_exam')</a>
                              @elseif($startTime >= $now && $now <= $endTime)
                                <span class="btn primary-btn small  fix-gr-bg" style="background:blue">Waiting</span>
                              @elseif($now >= $endTime)
                                <span class="btn primary-btn small  fix-gr-bg" style="background:#dc3545">Closed</span>
                              @else
                                <span class="btn primary-btn small  fix-gr-bg"
                                  style="background:green">@lang('exam.already_submitted')</span>
                              @endif
                            @endif
                          @else
                            @if ($startTime <= $now && $now <= $endTime)
                              <a class="btn primary-btn small  fix-gr-bg" style="background:green"
                                href="{{ route('take_online_exam', [@$online_exam->id]) }}">@lang('exam.take_exam')</a>
                            @elseif($startTime >= $now && $now <= $endTime)
                              <span class="btn primary-btn small  fix-gr-bg"
                                style="background:blue">@lang('common.waiting')</span>
                            @elseif($now >= $endTime)
                              <span class="btn primary-btn small  fix-gr-bg"
                                style="background:#dc3545">@lang('common.closed')</span>
                            @endif
                          @endif
                        </td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="modal fade admin-query" id="deleteOnlineExam">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">@lang('common.delete_item')</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <div class="text-center">
            <h4>@lang('common.are_you_sure_to_delete')</h4>
          </div>

          <div class="mt-40 d-flex justify-content-between">
            <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
            {{ Form::open(['route' => 'online-exam-delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="id" id="online_exam_id">
            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.delete')</button>
            {{ Form::close() }}
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
