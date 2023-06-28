<form id="form_post_message" action="{{ route('post') }}" method="post" enctype="multipart/form-data" onSubmit="postImmediately(event)">
  <input id="postImiCsrfToken" type="hidden" name="_token" value="{{csrf_token()}}">
  <div class="modal fade text-left" id="post_message" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="submit" class="btn btn-success btnPostImi">即時配信</button>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row mb-5">
            @include('owner.components.message_content')
          </div>
          <div class="row mb-2">
            @include('owner.components.message_image')
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

