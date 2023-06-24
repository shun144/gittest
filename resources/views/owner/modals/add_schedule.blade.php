
  <div class="modal fade text-left" id="add_schedule" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button form="form_add_schedule" type="submit" class="btn btn-primary btn_add_schedule">スケジュール作成</button>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <form id="form_add_schedule" action="{{ route('schedule.add') }}" method="post" enctype="multipart/form-data" onSubmit="return submitAddSchedule(event)">
            {{-- @csrf --}}
          <input id="addScheduleCsrfToken" type="hidden" name="_token" value="{{csrf_token()}}">
          <p style="display:none" id="tmp_add_schedule_data" data-temp-id=""  data-view-date=""></p>
          <div class="row mb-5">
            @include('owner.components.message_datatime')
          </div>

          <div class="row mb-3">
            @include('owner.components.message_title')
            <input type="hidden" name='message_id' class='msg_id'>
          </div>

          <div class="row mb-3">
            @include('owner.components.message_content')
          </div>

          <div class="row mb-3">
            @include('owner.components.message_image')
            <input type="hidden" name='image_id' class='img_id'>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
