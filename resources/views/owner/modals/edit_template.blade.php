
  <div class="modal fade text-left" id="edit_template" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <div class="row w-100">
            <div class="col-10">
              <button form="form_edit_template" type="submit" class="btn btn-success btn_edit_template">定型メッセージ更新</button>
            </div>

            <div class="col-2">

              <div class="row">
                <div class="col-10 text-right">
                  <form id="form_del_template" action="{{route('template.del')}}" method="post" onSubmit="return submitDeleteTemplate(event)" class="">
                    @csrf
                    <button form="form_del_template" type="submit" class="btn btn_del">
                      <input type="hidden" name='message_id' class='msg_id'>
                      <i style="font-size:20px" class="fas fa-trash-alt text-muted"></i>
                    </button>
                  </form>
                </div>

                <div class="col-2">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        
        <div class="modal-body">
          <form id="form_edit_template" action="{{ route('template.edit') }}" method="post" enctype="multipart/form-data" onSubmit="return submitEditTemplate(event)">
            @csrf
            @method('PATCH')
            <p style="display:none" id="tmp_edit_template_data" data-temp-id=""></p>
          

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
function submitEditTemplate(e){
  const msg = '定型メッセージを更新してよろしいですか?\n※スケジュールに登録されたメッセージの内容は更新されません。'
  if(window.confirm(msg)){
    return true;
  }
  else {
    return false;
  }
};

function submitDeleteTemplate(e){
  // e.preventDefault();
  const msg = '定型メッセージを削除してよろしいですか?\n※スケジュールに登録されたメッセージは削除されません。'
  if(window.confirm(msg)){
    return true;
  }
  else {
    return false;
  }
};

</script>