let Calendar = FullCalendar.Calendar;
let Draggable = FullCalendar.Draggable;
let containerEl = document.getElementById('external-events');
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
const URL_SCHEDULE_GET = URL_ROOT + '/dashboard/schedule-get';
const URL_SCHEDULE_ADD = URL_ROOT + '/dashboard/schedule-add';
const URL_SCHEDULE_EDIT = URL_ROOT + '/dashboard/schedule-edit';
const URL_SCHEDULE_DEL = URL_ROOT + '/dashboard/schedule-del';
const URL_TEMPLATE_GET = URL_ROOT + '/dashboard/template-get';

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 定型メッセージ更新モーダル開く前イベント
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
modal_edit_template.on('show.bs.modal', function(){
  const temp_id = edit_temp.data('tempId')
  let temp = templateMessages.find((v) => v.id==temp_id);
  modal_edit_template.find('.msg_id').val(temp.id)
  modal_edit_template.find('.title_form').val(temp.title)
  modal_edit_template.find('.content_form').val(temp.content)

  let select_color = modal_edit_template.find("[data-color='" + temp.title_color + "']").get(0)
  select_color.checked = true
  select_color.dispatchEvent(new Event('change'));

  let preview = modal_edit_template.find('.image_preview').get(0);
  let file_name_view = modal_edit_template.find('.filename_view');
  let img_id_form = modal_edit_template.find('.img_id');
  let has_image = "0"
  preview.innerHTML = '';
  file_name_view.val('');
  img_id_form.val('');

  modal_edit_template.find('input[type=file]').val('');

  const img = temp.images[0];
  if (typeof img !== "undefined")
  { 
    has_image = "1"     
    const imgElem = document.createElement('img')
    imgElem.src = URL_STORAGE + '/' + img.save_name
    // imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
    preview.appendChild(imgElem);
    file_name_view.val(img.org_name)
    img_id_form.val(img.image_id)
  }
  modal_edit_template.find('.has_file').val(has_image)
})

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// スケジュール追加モーダル開く前イベント
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
modal_add_schedule.on('show.bs.modal', function(){

  const view_dt = new Date(add_data.data('viewDate'));
  const view_year = view_dt.getFullYear()
  const view_month = ( '00' + Number(view_dt.getMonth()+1)).slice(-2)
  const view_date = ( '00' + Number(view_dt.getDate())).slice(-2)

  modal_add_schedule.find('.datetime_form').val(view_year + '-' + view_month + '-' + view_date)
  modal_add_schedule.find('.hh_form').val('00')
  modal_add_schedule.find('.mm_form').val('00')

  let preview = modal_add_schedule.find('.image_preview').get(0);
  let file_name_view = modal_add_schedule.find('.filename_view');
  let img_id_form = modal_add_schedule.find('.img_id');
  let has_image = "0"
  preview.innerHTML = '';
  file_name_view.val('');
  img_id_form.val('');

  modal_add_schedule.find('input[type=file]').val('');

  const temp_id = add_data.data('tempId')
  if (temp_id != '')
  {
    let temp = templateMessages.find((v) => v.id==temp_id);

    modal_add_schedule.find('.msg_id').val(temp.id)
    modal_add_schedule.find('.title_form').val(temp.title)
    modal_add_schedule.find('.content_form').val(temp.content)

    let select_color = modal_add_schedule.find("[data-color='" + temp.title_color + "']").get(0)
    select_color.checked = true
    select_color.dispatchEvent(new Event('change'));


    const img = temp.images[0];
    if (typeof img !== "undefined")
    { 
      has_image = "1"     
      const imgElem = document.createElement('img')
      imgElem.src = URL_STORAGE + '/' + img.save_name;
      // imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
      preview.appendChild(imgElem);
      file_name_view.val(img.org_name)
      img_id_form.val(img.image_id)
    }
  } else {
    modal_add_schedule.find('.msg_id').val('')
    modal_add_schedule.find('.title_form').val('')
    modal_add_schedule.find('.content_form').val('')

    let select_color = modal_add_schedule.find("[data-color='#E60012']").get(0)
    select_color.checked = true
    select_color.dispatchEvent(new Event('change'));
    
  }
  modal_add_schedule.find('.has_file').val(has_image)
});




// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// スケジュール更新モーダル開く前イベント
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
modal_edit_schedule.on('show.bs.modal', function(){

  const msg_id = edit_data.data('msgId')
  let event = calendar.getEventById(msg_id)
  
  const view_dt = new Date(edit_data.data('viewDate'));
  const view_year = view_dt.getFullYear()
  const view_month = ( '00' + Number(view_dt.getMonth()+1)).slice(-2)
  const view_date = view_dt.getDate()

  const plan_dt = new Date(event.extendedProps.plan_at);
  const plan_hour = plan_dt.getHours().toString().padStart(2, '0');
  const plan_min = plan_dt.getMinutes().toString().padStart(2, '0');

  modal_edit_schedule.find('.datetime_form').val(view_year + '-' + view_month + '-' + view_date)
  modal_edit_schedule.find('.hh_form').val(plan_hour)
  modal_edit_schedule.find('.mm_form').val(plan_min)
  modal_edit_schedule.find('.msg_id').val(event.id)

  let select_color = modal_edit_schedule.find("[data-color='"+ event.backgroundColor + "']").get(0)
  select_color.checked = true
  select_color.dispatchEvent(new Event('change'));
  modal_edit_schedule.find('.title_form').val(event.title)
  modal_edit_schedule.find('.content_form').val(event.extendedProps.content)

  let preview = modal_edit_schedule.find('.image_preview').get(0);
  let file_name_view = modal_edit_schedule.find('.filename_view');
  let has_image = "0"
  preview.innerHTML = '';
  file_name_view.val('');

  const img = event.extendedProps.images[0];
  if (typeof img !== "undefined")
  { 
    has_image = "1"
    const imgElem = document.createElement('img')
    imgElem.src = URL_STORAGE + '/' + img.save_name;
    // imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
    preview.appendChild(imgElem);
    file_name_view.val(img.org_name)
  }
  modal_edit_schedule.find('.has_file').val(has_image)
})


// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 編集モーダル閉じる前イベント
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
modal_edit_schedule.on('hide.bs.modal', function(){
  if (edit_data.data('isChange').toLowerCase() != 'true')
  {
    const msg_id = modal_edit_schedule.find('.msg_id').val()
    let event = calendar.getEventById(msg_id)
    event.setStart(edit_data.data('oldStart'))
    event.setEnd(edit_data.data('oldEnd'))
  }
});


// /_/_/_/_/_/_/_/_/_/_/_
// スケジュール追加
// /_/_/_/_/_/_/_/_/_/_/_
function submitAddSchedule(e){

  e.preventDefault();
  const msg = 'スケジュールを作成してよろしいですか?'
  if(window.confirm(msg)){
    const csrf = $('#addTemplateCsrfToken').val();
    let $form = $('#form_add_schedule');
    let fd = new FormData($form.get(0));
    modal_add_schedule.modal('hide');
    $.ajax({
      headers: {'X-CSRF-TOKEN': csrf},
      // url: '{{route('schedule.add')}}',
      url: URL_SCHEDULE_ADD,
      method: 'POST',
      contentType: false,
      processData: false,
      data: fd
    })
    .done(function (data) {
      calendar.addEvent({
        id: data.id,
        title: data.title,
        start: data.start,
        backgroundColor: data.backgroundColor,
        borderColor: data.borderColor,
        allDay: data.allDay,
        content: data.content,
        plan_at: data.plan_at,
        images:data.images
      });
      toastr.success('スケジュールを追加しました。');
    })
    .fail(function (data) {
      // toastr.error(data.responseJSON.message)
      toastr.error('スケジュール追加に失敗しました。');
    });
  }
  return false; 
};


// /_/_/_/_/_/_/_/_/_/_/_
// スケジュール更新
// /_/_/_/_/_/_/_/_/_/_/_
function submitEditSchedule(e){
  const msg = 'スケジュールを更新してよろしいですか?'
  if(window.confirm(msg)){
    edit_data.data('isChange','true')
    const csrf = $('#editScheduleCsrfToken').val();
    let $form = $('#form_edit_schedule');
    let fd = new FormData($form.get(0));
    let event = calendar.getEventById(fd.get('message_id'))
  
    modal_edit_schedule.modal('hide');
    $.ajax({
      headers: {'X-CSRF-TOKEN': csrf},
      url: URL_SCHEDULE_EDIT,
      // url: '{{route('schedule.edit')}}',
      method: 'POST',
      contentType: false,
      processData: false,
      data: fd
    })
    .done(function (data) {
      const st_date = new Date(data.start.split(' ')[0]  + ' 00:00:00') 
      const end_date = new Date(data.end)
      event.setStart(st_date);
      // setEndでendを指定しないと想定しないendが自動設定される
      event.setEnd(end_date); 

      event.setProp('title', data.title);        
      event.setProp('backgroundColor', data.backgroundColor);
      event.setProp('borderColor', data.borderColor);
      event.setAllDay(data.allDay);
      event.setExtendedProp ('content', data.content);
      event.setExtendedProp('plan_at', data.plan_at);
      event.setExtendedProp('images', data.images);
      toastr.success('スケジュールを更新しました。');
    })
    .fail(function (data) {
      // toastr.error(data.responseJSON.message)
      toastr.error('スケジュール更新に失敗しました。');
    });
  }
  return false;
};

// /_/_/_/_/_/_/_/_/_/_/_
// スケジュール削除
// /_/_/_/_/_/_/_/_/_/_/_
function submitDeleteSchedule(e){
  e.preventDefault();
  const msg = 'スケジュールを削除してよろしいですか?'
  if(window.confirm(msg)){
    const csrf = $('#delScheduleCsrfToken').val();
    let $form = $('#form_del_schedule');
    let fd = new FormData($form.get(0));
    $('#edit_schedule').modal('hide');
    $.ajax({
      headers: {'X-CSRF-TOKEN': csrf},
      // url: '{{route('schedule.del')}}',
      url: URL_SCHEDULE_DEL,
      method: 'POST',
      contentType: false,
      processData: false,
      data: fd
    })
    .done(function (message_id) {
      calendar.getEventById(message_id).remove();
      toastr.success('スケジュールを削除しました。');
    })
    .fail(function(data){
      toastr.error('スケジュール削除に失敗しました。');
    });
  }
  
  return false;
};  




// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 定型メッセージ取得
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
let templateMessages = null;
$.ajax(
  {
    url: URL_TEMPLATE_GET,
    // url: '{{route('template.get')}}', 
    type:'get'})
.then(
function (data) {
  templateMessages = data;
},
function () {
  console.error("読み込み失敗");
}); 

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
//  定型メッセージクリックイベント
$('.external-event').click(function() {
  edit_temp.data('tempId', $(this).data('msgid'));
  modal_edit_template.modal('show');
})

// function test(){
//   console.log('テスト実行');
//   new Draggable(containerEl, {
//     itemSelector: '.external-event',
//     eventData: function(eventEl) {
//       return {
//         id:eventEl.dataset.msgid,
//         title: eventEl.innerText,
//         backgroundColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
//         borderColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
//         textColor: window.getComputedStyle( eventEl ,null).getPropertyValue('color'),
//       };
//     }
//   });
// }

// test()

new Draggable(containerEl, {
  itemSelector: '.external-event',
  eventData: function(eventEl) {
    return {
      id:eventEl.dataset.msgid,
      title: eventEl.innerText,
      backgroundColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
      borderColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
      textColor: window.getComputedStyle( eventEl ,null).getPropertyValue('color'),
    };
  }
});

function ini_events(ele) {
  ele.each(function () {
    $(this).draggable({
      scroll: true,
      helper: 'clone',
      zIndex: 999,
      revert: true,
      revertDuration: 0
    })
  })
}

ini_events($('#external-events div.external-event'));

let calendar = new Calendar(calendarEl, {
  timeZone: 'local',
  headerToolbar: {
    left  : 'prev,next today',
    center: 'title',
    right : 'dayGridMonth'
  },
  themeSystem: 'bootstrap',
  locale: 'ja',
  businessHours:true,
  editable:true,
  droppable: true,
  eventOrderStrict: true,
  eventOrder: "plan_at",
  events:[],
  datesSet:(info) => {
    if (viewMonth != info.view.title){
      viewMonth = info.view.title;
      $.ajax({
        headers: {'X-CSRF-TOKEN': $('#calendarToken').text()},
        url: URL_SCHEDULE_GET,
        // url: '{{route('schedule.get')}}',
        method: 'POST',
        data:{
          start_date: info.start.valueOf(),
          end_date: info.end.valueOf(),
        }
      })
      .done(function (data) {
        calendar.removeAllEvents();
        calendar.setOption('events', data);
      });
    }
  },

  // イベントのリサイズを不可にする
  eventResize: (info) => {
    info.event.setEnd(info.oldEvent.end);
  },

  // カレンダー日付クリックイベント
  dateClick: (info)=>{
    add_data.data('tempId','')
    add_data.data('viewDate', info.date)
    modal_add_schedule.modal('show');
  },

  // 登録スケジュールクリックイベント
  eventClick:(info)=>{
    edit_data.data('msgId',info.event.id)
    edit_data.data('isChange','false')
    edit_data.data('oldStart',info.event.start)
    edit_data.data('oldEnd',info.event.end)
    edit_data.data('viewDate', info.event.start)
    modal_edit_schedule.modal('show');
  },

  // 登録スケジュールドラッグ&ドロップイベント
  eventDrop:(info)=>{
    edit_data.data('msgId',info.event.id)
    edit_data.data('isChange','false')
    edit_data.data('oldStart',info.oldEvent.start)
    edit_data.data('oldEnd',info.oldEvent.end)
    edit_data.data('viewDate',info.event.start)
    modal_edit_schedule.modal('show');
  },

  // 定型メッセージドラッグ&ドロップイベント
  eventReceive: function(info) {      
    add_data.data('tempId', info.draggedEl.getAttribute('data-msgid'))
    add_data.data('viewDate', info.event.start)
   
    modal_add_schedule.modal('show');
    info.event.remove()
  },
});

calendar.render();




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

  $.each(target.files, function (idx, file) {
    if (['image/jpeg','image/png'].indexOf(file.type) !== -1) {
      fileList.push(file)
      filesNameList.push(file.name)
    }
  });

  // プレビュー初期化
  preview.empty();

  if (target.files.length == 1) {
    has_file.val('1');
    $.each(fileList, function (idx, file) {
      let reader = new FileReader();
      reader.onload = (function (elem) { preview.append('<img src="' + elem.target.result + '">');});
      reader.readAsDataURL(file);
    });
    text_form.val(filesNameList.join(' '));
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
  validatePostMsgInput('#post_message', "#form_post_message")
})

$('#post_message').on('hidden.bs.modal', function(e) {
  const modal = $(e.target)
  modal.find('textarea.content_form').removeClass("is-invalid");
})

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 定型メッセージ追加
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
$(document).on('click','.btn_add_template', function(e){
  e.preventDefault();
  validateTemplateInput('#add_template', "#form_add_template")
})

$('#add_template').on('hidden.bs.modal', function(e) {
  const modal = $(e.target)
  modal.find('input.title_form').removeClass("is-invalid");
  modal.find('textarea.content_form').removeClass("is-invalid");
})


// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 定型メッセージ更新
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
$(document).on('click','.btn_edit_template', function(e){
  e.preventDefault();
  validateTemplateInput('#edit_template', "#form_edit_template")
})

$('#edit_template').on('hidden.bs.modal', function(e) {
  const modal = $(e.target)
  modal.find('input.title_form').removeClass("is-invalid");
  modal.find('textarea.content_form').removeClass("is-invalid");
})

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// スケジュール追加
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
$(document).on('click','.btn_add_schedule', function(e){
  e.preventDefault();
  validateScheduleInput('#add_schedule', "#form_add_schedule")
})

$('#add_schedule').on('hidden.bs.modal', function(e) {
  const modal = $(e.target)
  modal.find('input.title_form').removeClass("is-invalid");
  modal.find('textarea.content_form').removeClass("is-invalid");
  modal.find('input.datetime_form').removeClass("is-invalid");
})


// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// スケジュール更新
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
$(document).on('click','.btn_edit_schedule', function(e){
  e.preventDefault();
  validateScheduleInput('#edit_schedule', "#form_edit_schedule")
})

$('#edit_schedule').on('hidden.bs.modal', function(e) {
  const modal = $(e.target)
  modal.find('input.title_form').removeClass("is-invalid");
  modal.find('textarea.content_form').removeClass("is-invalid");
  modal.find('input.datetime_form').removeClass("is-invalid");
})

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 関数
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
function validatePostMsgInput(modalId, submitId){
  const modal = $(modalId)
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



function validateTemplateInput(modalId, submitId){
  const modal = $(modalId)
  const inputTitle = modal.find('input.title_form')
  const titleFeedback = modal.find('.title_feedback')
  const inputContent = modal.find('textarea.content_form')
  const contentFeedback = modal.find('.content_feedback')
  let is_err = false

  if (!inputTitle.val()) {
    titleFeedback.text('必須項目です');
    inputTitle.addClass("is-invalid");
    is_err = true
  }
  if (inputTitle.val().length > 50) {
    titleFeedback.text('入力可能文字数は50文字です');
    inputTitle.addClass("is-invalid");
    is_err = true
  }

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

function validateScheduleInput(modalId, submitId){
  const modal = $(modalId)
  const inputTitle = modal.find('input.title_form')
  const titleFeedback = modal.find('.title_feedback')
  const inputContent = modal.find('textarea.content_form')
  const contentFeedback = modal.find('.content_feedback')

  const inputDatetime = modal.find('input.datetime_form')
  const datetimeFeedback = modal.find('.datetime_feedback')
  let is_err = false

  if (!inputDatetime.val()) {
    datetimeFeedback.text('必須項目です');
    inputDatetime.addClass("is-invalid");
    is_err = true
  }

  if (!inputTitle.val()) {
    titleFeedback.text('必須項目です');
    inputTitle.addClass("is-invalid");
    is_err = true
  }
  if (inputTitle.val().length > 50) {
    titleFeedback.text('入力可能文字数は50文字です');
    inputTitle.addClass("is-invalid");
    is_err = true
  }

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