{{-- <form id="form_add_template" action="{{ route('template.add') }}" method="post" enctype="multipart/form-data" onSubmit="submitAddTemplate(event)"> --}}
  {{-- @csrf --}}
<form id="form_add_template" action="{{ route('template.add') }}" method="post" enctype="multipart/form-data"> 
  {{-- <input id="addTemplateCsrfToken" type="hidden" name="_token" value="{{csrf_token()}}"> --}}

  @csrf
  <div class="modal fade text-left" id="add_template" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="submit" class="btn btn-success btn_add_template">定型メッセージ作成</button>
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

<script>

  // function submitAddTemplate(e){
  //   e.preventDefault();    
  //   const csrf_token = document.getElementById('addTemplateCsrfToken').value;
  //   let $form = $('#form_add_template');
  //   let fd = new FormData($form.get(0));

  //   $.ajax({
  //     headers: {'X-CSRF-TOKEN': csrf_token},
  //     url: '{{route('template.add')}}',
  //     method: 'POST',
  //     contentType: false,
  //     processData: false,
  //     data: fd
  //   })
  //   .done(function (data) {
  //     console.log(data);
  //     toastr.success('追加しました。');
  //   })
  //   //通信失敗した時の処理
  //   .fail(function (data) {
  //     toastr.error('失敗しました。');
  //   });

  //   $('#add_template').modal('hide');

  //   // const msg = '定型メッセージを作成してよろしいですか?'
  //   // if(!window.confirm(msg)){return false;}
  // }

</script>



{{-- <form id="form_add_template" action="{{ route('template.add') }}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="modal fade text-left" id="add_template" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="submit" class="btn btn-success btn_add_template">定型メッセージ作成</button>
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

<script>

</script> --}}