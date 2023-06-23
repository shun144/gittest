
  <div class="modal fade text-left" id="edit_schedule" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button form="form_edit_schedule" type="submit" class="btn btn-success btn_edit_schedule">スケジュール編集</button>

          <form id="form_del_schedule" action="{{route('schedule.del')}}" method="post" onSubmit="return submitDeleteSchedule(event)" class="">
            {{-- @csrf --}}
            <input id="delScheduleCsrfToken" type="hidden" name="_token" value="{{csrf_token()}}">
            <button form="form_del_schedule" type="submit" class="btn btn_del">
              <input type="hidden" name='message_id' class='msg_id'>
              {{-- <input type="hidden" name='eventid' class='eventid'> --}}
              {{-- <p class="eventid" style="display:none"></p> --}}
              <i class="fas fa-trash-alt text-muted"></i>
            </button>
          </form>
          

          

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <form id="form_edit_schedule" action="{{ route('schedule.edit') }}" method="post" enctype="multipart/form-data" onSubmit="return submitEditSchedule(event)">
            @csrf
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
            </div>
          </form>
          
        </div>
      </div>
    </div>
  </div>

<script>
  function submitEditSchedule(e){
    const msg = 'スケジュールを編集してよろしいですか?'
    if(window.confirm(msg)){
      return true;
    }
    else {
      return false;
    }
  };

  // function submitDeleteSchedule(e){
  //   const msg = 'スケジュールを削除してよろしいですか?'
  //   if(window.confirm(msg)){
  //     return true;
  //   }
  //   else {
  //     return false;
  //   }
  // };  
  </script>

