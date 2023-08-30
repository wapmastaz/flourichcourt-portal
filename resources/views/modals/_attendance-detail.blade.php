<section>
  <div class="student-meta-box">
    <div class="">
      <h3>Showing Attendance Detail for: {{ date('l jS \o\f F Y', strtotime($current_day)) }}</h3>
      <div class="row">
        <div class="col-lg-12">
          <div class="single-meta mt-20">
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <div class="value text-left">
                  @lang('common.name')
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="name">
                  {{ $student_detail->full_name }}
                </div>
              </div>

            </div>
          </div>
          <div class="single-meta">
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <div class="value text-left">
                  @lang('student.admission_no')
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="name">
                  {{ $student_detail->admission_no }}
                </div>
              </div>
            </div>
          </div>
          <div class="single-meta">
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <div class="value text-left">
                  Status
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="name">
                  {{ $attendance->attendance_type }}
                </div>
              </div>
            </div>
          </div>
          <div class="single-meta">
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <div class="value text-left">
                  Note
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="name">
                  {{ $attendance->notes }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
