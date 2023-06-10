<form id="form_edit_template" action="{{ route('template.edit') }}" method="post" enctype="multipart/form-data">
  @csrf
  @method('PATCH')
  <div class="modal fade text-left" id="edit_template" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn btn-primary btn_edit_template">定型メッセージ編集</button>
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

</script>