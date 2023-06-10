<form action="{{ route('owner.send') }}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="modal fade text-left" id="send_message" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="submit" class="btn btn-primary">即時配信</button>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="row mb-5">
            @include('owner.components.message_content')
          </div>

          <div class="row mb-2">
            @component('owner.components.message_image')
              @slot('id')send_input_file @endslot
            @endcomponent
          </div>

        </div>
      </div>
    </div>
  </div>
</form>
