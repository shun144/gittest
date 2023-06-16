<form id="form_edit_template" action="{{ route('template.edit') }}" method="post" enctype="multipart/form-data" onSubmit="return submitEditTemplate(event)">
  @csrf
  @method('PATCH')
  <div class="modal fade text-left" id="edit_template" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn btn-success btn_edit_template">定型メッセージ編集</button>
          
          <form action="{{route('template.del')}}" method="post" onSubmit="return submitDeleteTemplate(event)">
            @csrf
            <button type="submit" class="btn btn_del">
              {{-- <input type="hidden" name="user_id" value={{$store->user_id}}>
              <input type="hidden" name="store_id" value={{$store->store_id}}>
              <input type="hidden" class="hid_store_name" value={{$store->name}}> --}}
              <i class="fas fa-trash-alt text-muted"></i>
            </button>
          </form>
          
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>
        
        <div class="modal-body">

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
        </div>
      </div>
    </div>
  </div>
</form>

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