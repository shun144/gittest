const URL_ROOT = $(location).attr('origin');
const URL_STORAGE = URL_ROOT + '/storage/owner/image/greeting';
const URL_DASHBOARD = URL_ROOT + '/dashboard';
const URL_MEDSSAGE_POST = URL_DASHBOARD + '/greeting-link-edit';

function greetSave(e){
  e.preventDefault();
  const msg = 'あいさつメッセージを更新してよろしいですか?'
  if(!window.confirm(msg))
  {
    return false;
  }
  const csrf_token = document.getElementById('greetSaveCsrfToken').value;
  let $form = $('#form_greet_save');
  let fd = new FormData($form.get(0));

  const inputContent = $form.find('textarea.content_form')
  const contentFeedback = $form.find('.content_feedback')


  if (!inputContent.val()) {
    contentFeedback.text('必須項目です');
    inputContent.addClass("is-invalid");
    return false
  }

  if (inputContent.val().length > 1000)
  {
    contentFeedback.text('入力可能文字数は1000文字です');
    inputContent.addClass("is-invalid");
    return false
  }
  $.ajax({
    headers: {'X-CSRF-TOKEN': csrf_token},
    url: URL_MEDSSAGE_POST,
    method: 'POST',
    contentType: false,
    processData: false,
    data: fd
  }).done(function(res){
    toastr.info('あいさつメッセージを更新しました');
  }).fail(function(res){
    toastr.error('あいさつメッセージ更新に失敗しました');
    console.log(res);
  })
};

$(document).on('change','.image_form', function(e){
  preview_image(e)
});


function preview_image(event)
{
  let fileList = []
  let filesNameList = []
  let target = event.target
  let parent = $(target).closest('.input-group');
  let preview = parent.siblings('.image_preview')
  let text_form = parent.children('input.filename_view')
  let input_file =  parent.find('.image_form')
  let has_file = parent.find('.has_file')

  // プレビュー初期化
  preview.empty();

  if (target.files.length == 1) {
    const file = target.files[0];
    if (['image/jpeg','image/png'].indexOf(file.type) !== -1) {
      let reader = new FileReader();
      reader.onload = (function (elem) { preview.append('<img src="' + elem.target.result + '">');});
      reader.readAsDataURL(file);
      text_form.val(file.name);
      has_file.val('1');
    } 
    else {
      has_file.val('0');
      text_form.val('');
      input_file.val('');
    }
  }
  else {
    has_file.val('0');
    text_form.val('');
    input_file.val('');
    return false;
  }
}


$(document).on('click','.btn_del_file', function(e){
  let target = e.target
  let parent = $(target).closest('.input-group');
  let preview = parent.siblings('.image_preview')
  let text_form = parent.children('input.filename_view')
  let has_file = $(target).next();
  has_file.val('0')
  let input_file =  parent.find('.image_form')
  input_file.val(null);
  preview.empty();
  text_form.val(null);
});