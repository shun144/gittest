<form id="form_add_template" action="{{ route('template.add') }}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="modal fade text-left" id="add_template" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="submit" class="btn btn-primary btn_add_template">定型メッセージ作成</button>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">

          <div class="row mb-3">
            @include('owner.components.message_title')
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

