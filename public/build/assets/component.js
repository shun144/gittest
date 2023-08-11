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


// // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// // 保存
// // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// $(document).on('click','.btnPostImi', function(e){
//   e.preventDefault();
//   validatePostMsgInput('#post_message', "#form_post_message")
// })


// // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// // 関数
// // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// function validatePostMsgInput(modalId, submitId){
//   const modal = $(modalId)
//   const inputContent = modal.find('textarea.content_form')
//   const contentFeedback = modal.find('.content_feedback')
//   let is_err = false

//   if (!inputContent.val()) {
//     contentFeedback.text('必須項目です');
//     inputContent.addClass("is-invalid");
//     is_err = true
//   }
//   if (inputContent.val().length > 1000)
//   {
//     contentFeedback.text('入力可能文字数は1000文字です');
//     inputContent.addClass("is-invalid");
//     is_err = true
//   }
//   if (is_err) {
//     return;
//   }
//   else {
//     $(submitId).submit();
//   }
// };