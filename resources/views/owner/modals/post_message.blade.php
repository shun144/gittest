<form id="form_post_message" action="{{ route('post') }}" method="post" enctype="multipart/form-data" onSubmit="postImmediately(event)">
  {{-- <form id="form_post_message" action="{{ route('post') }}" method="post" enctype="multipart/form-data" > --}}
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


<script>
  function postImmediately(e){
    e.preventDefault();
    const msg = '即時配信を開始してよろしいですか?'
    if(!window.confirm(msg))
    {
      return false;
    }


    const csrf_token = document.getElementById('postImiCsrfToken').value;
    let $form = $('#form_post_message');
    let fd = new FormData($form.get(0));


    $.ajax({
      headers: {'X-CSRF-TOKEN': csrf_token},
      url: '{{route('post')}}',
      method: 'POST',
      contentType: false,
      processData: false,
      data: fd
    })
    // .done(function (data) {
    //   toastr.success('即時配信が完了しました。<br/> 実行結果は配信履歴一覧をご確認ください');
    // })
    // //通信失敗した時の処理
    // .fail(function (data) {
    //   toastr.error('即時配信が失敗しました。<br/> 実行結果は配信履歴一覧をご確認ください');
    // });

    toastr.info('即時配信を開始しました。<br/> 状況は配信履歴一覧をご確認ください');
    $('#post_message').modal('hide');
  };
  </script>

{{-- <script>
function postImmediately(e){
  e.preventDefault();
  const msg = '即時配信を開始してよろしいですか?'
  if(!window.confirm(msg))
  {
    return false;
  }
  const form = document.getElementById('form_post_message');
  const csrf_token = document.getElementById('postImiCsrfToken').value;
  const content = form.querySelector("textarea").value;

  $.ajax({
    headers: {'X-CSRF-TOKEN': csrf_token},
    url: '{{route('post')}}',
    method: 'POST',
    data: {
      'content': content
    }})
  .done(function (data) {
    toastr.success('即時配信が完了しました。<br/> 実行結果は配信履歴一覧をご確認ください');
  })
  //通信失敗した時の処理
  .fail(function (data) {
    toastr.error('即時配信が失敗しました。<br/> 実行結果は配信履歴一覧をご確認ください');
  });
  toastr.info('即時配信を開始しました。<br/> 実行結果は配信履歴一覧をご確認ください');
  $('#post_message').modal('hide');
};
</script> --}}