// // let Calendar = FullCalendar.Calendar;
// let Draggable = FullCalendar.Draggable;
// // let containerEl = document.getElementById('external-events');
let checkbox = document.getElementById('drop-remove');
let calendarEl = document.getElementById('calendar');
let viewMonth = null

let modal_add_schedule = $('#add_schedule');
let modal_edit_schedule = $('#edit_schedule')
let modal_edit_template = $('#edit_template')
let add_data = $('#tmp_add_schedule_data')
let edit_data = $('#tmp_edit_schedule_data')
let edit_temp = $('#tmp_edit_template_data')

const URL_ROOT = $(location).attr('origin');
const URL_STORAGE = URL_ROOT + '/storage/owner/image/template';

const URL_DASHBOARD = URL_ROOT + '/dashboard';
const URL_MEDSSAGE_POST = URL_DASHBOARD + '/post';
const URL_SCHEDULE_GET = URL_DASHBOARD + '/schedule-get';
const URL_SCHEDULE_ADD = URL_DASHBOARD + '/schedule-add';
const URL_SCHEDULE_EDIT = URL_DASHBOARD + '/schedule-edit';
const URL_SCHEDULE_DEL = URL_DASHBOARD + '/schedule-del';
const URL_TEMPLATE_GET = URL_DASHBOARD + '/template-get';


// /_/_/_/_/_/_/_/_/_/_/_
// 即時配信
// /_/_/_/_/_/_/_/_/_/_/_
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
    url: URL_MEDSSAGE_POST,
    method: 'POST',
    contentType: false,
    processData: false,
    data: fd
  }).done((results) => {
    console.log('成功');
  }).fail(() => {
    console.log('失敗');
  });

  // console.log($form.get(0));
  // console.log(fd);

  // for (const [key, value] of fd) {
  //   console.log(`${key}: ${value}\n`);
  // }

  // $.ajax({
  //   headers: {'X-CSRF-TOKEN': csrf_token},
  //   url: URL_MEDSSAGE_POST,
  //   method: 'POST',
  //   contentType: false,
  //   processData: false,
  //   data: fd
  // })

  // const modal_post_message =  $('#post_message');
  const modal_post_message =  $('#form_post_message');
  console.log(modal_post_message);
  modal_post_message.find('.content_form').val('');
  modal_post_message.find('.has_file').val('0');
  modal_post_message.find('input[type=file]').val('');

  modal_post_message.find('.image_preview').get(0).innerHTML = '';
  modal_post_message.find('.filename_view').val('');

  modal_post_message.modal('hide');
  toastr.info('即時配信を開始しました。<br/> 状況は配信履歴一覧をご確認ください');

};


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
      text_form.val('');
      has_file.val('0');
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




// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// メッセージ共有
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// タイトル_エラーフィードバック解除
$(document).on('keydown','.title_form', function(e){
  $(e.target).removeClass("is-invalid");
})

// 内容_エラーフィードバック解除
$(document).on('keydown','.content_form', function(e){
  $(e.target).removeClass("is-invalid");
})

// 日付_エラーフィードバック解除
$(document).on('keydown','.datetime_form', function(e){
  $(e.target).removeClass("is-invalid");
})

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 即時配信
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
$(document).on('click','.btnPostImi', function(e){
  e.preventDefault();
  validatePostMsgInput("#form_post_message");
  // validatePostMsgInput('#post_message', "#form_post_message");
})

// $('#post_message').on('hidden.bs.modal', function(e) {
//   const modal = $(e.target)
//   modal.find('textarea.content_form').removeClass("is-invalid");
// })



// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 関数
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
function validatePostMsgInput(submitId){
  const modal = $(submitId)
  const inputContent = modal.find('textarea.content_form')
  const contentFeedback = modal.find('.content_feedback')
  let is_err = false

  if (!inputContent.val()) {
    contentFeedback.text('必須項目です');
    inputContent.addClass("is-invalid");
    is_err = true
  }
  if (inputContent.val().length > 1000)
  {
    contentFeedback.text('入力可能文字数は1000文字です');
    inputContent.addClass("is-invalid");
    is_err = true
  }
  if (is_err) {
    return;
  }
  else {
    $(submitId).submit();
  }
};


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



// function validateTemplateInput(modalId, submitId){
//   const modal = $(modalId)
//   const inputTitle = modal.find('input.title_form')
//   const titleFeedback = modal.find('.title_feedback')
//   const inputContent = modal.find('textarea.content_form')
//   const contentFeedback = modal.find('.content_feedback')
//   let is_err = false

//   if (!inputTitle.val()) {
//     titleFeedback.text('必須項目です');
//     inputTitle.addClass("is-invalid");
//     is_err = true
//   }
//   if (inputTitle.val().length > 50) {
//     titleFeedback.text('入力可能文字数は50文字です');
//     inputTitle.addClass("is-invalid");
//     is_err = true
//   }

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

// function validateScheduleInput(modalId, submitId){
//   const modal = $(modalId)
//   const inputTitle = modal.find('input.title_form')
//   const titleFeedback = modal.find('.title_feedback')
//   const inputContent = modal.find('textarea.content_form')
//   const contentFeedback = modal.find('.content_feedback')

//   const inputDatetime = modal.find('input.datetime_form')
//   const datetimeFeedback = modal.find('.datetime_feedback')
//   let is_err = false

//   if (!inputDatetime.val()) {
//     datetimeFeedback.text('必須項目です');
//     inputDatetime.addClass("is-invalid");
//     is_err = true
//   }

//   if (!inputTitle.val()) {
//     titleFeedback.text('必須項目です');
//     inputTitle.addClass("is-invalid");
//     is_err = true
//   }
//   if (inputTitle.val().length > 50) {
//     titleFeedback.text('入力可能文字数は50文字です');
//     inputTitle.addClass("is-invalid");
//     is_err = true
//   }

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