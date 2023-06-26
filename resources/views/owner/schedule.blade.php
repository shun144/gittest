@extends('adminlte::page')

{{-- @section('title', 'Dashboard') --}}

@section('title', '配信')

@section('content_header')
    <h1>配信</h1>
@stop

@section('content')

<div class="card">
  <div class="card-header">
    <button class="btn btn-success" data-toggle="modal" data-target="#post_message">
      即時配信
    </button>
  </div>


  <div class="card-body">
    <div class="row">
      <div class="col-md-3">        
        <div class="sticky-top mb-3">
          <div class="card">

            <div class="card-header">
              <div class="d-flex justify-content-between">
                <h5 class="d-flex align-items-center mb-0">定型メッセージ</h5>
                <button class="btn btn-success" data-toggle="modal" data-target="#add_template">
                  作成
                </button>
              </div>
            </div>

            <div class="card-body">
              
              <div id="external-events">
                @foreach ($templates as $item)
                <div class="external-event" data-msgid="{{ $item->id }}" style="color:white; background-color:{{$item->title_color}}">
                  {{$item->title}}
                </div>
                @endforeach
              </div>
            </div>


          </div>
        </div>
      </div>
  
      <div class="col-md-9">
        <div class="card card-primary">
          <div class="card-body p-0">
            <div id="calendarToken" style="display:none">{{ csrf_token()}}</div>
            <div id="calendar"></div>
          </div>
        </div>
      </div>
    </div>    
  </div>

</div>

  @include('owner.modals.post_message')
  @include('owner.modals.add_template')
  @include('owner.modals.edit_template')

  @include('owner.modals.add_schedule')
  @include('owner.modals.edit_schedule')
@stop


@section('css')
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fullcalendar/main.min.css') }}"> 
  <link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
  <link rel="stylesheet" href="{{ asset('build/assets/component-ccd5ae91.css')}}">
  {{-- @vite(['resources/sass/component.scss']) --}}
@stop

@section('js')
<script src="{{ asset('vendor/adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/fullcalendar/main.min.js') }}"></script>
  {{-- <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script> --}}
  <script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
  <script src="{{ asset('build/assets/component-8509f447.js')}}"></script>
  {{-- @vite(['resources/js/component.js']) --}}


<script>

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
  

  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // 定型メッセージ編集モーダル開く前イベント
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
      imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
      preview.appendChild(imgElem);
      file_name_view.val(img.org_name)
      img_id_form.val(img.image_id)
    }
    modal_edit_template.find('.has_file').val(has_image)
  })

  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // 追加モーダル開く前イベント
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
        imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
        preview.appendChild(imgElem);
        file_name_view.val(img.org_name)
        img_id_form.val(img.image_id)
      }
      // modal_add_schedule.find('.has_file').val(has_image)

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
  // 編集モーダル開く前イベント
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
      imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
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
      $.ajax({
        headers: {'X-CSRF-TOKEN': csrf},
        url: '{{route('schedule.add')}}',
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

    modal_add_schedule.modal('hide');
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

      $.ajax({
        headers: {'X-CSRF-TOKEN': csrf},
        url: '{{route('schedule.edit')}}',
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
    modal_edit_schedule.modal('hide');


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
      $.ajax({
        headers: {'X-CSRF-TOKEN': csrf},
        url: '{{route('schedule.del')}}',
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
    $('#edit_schedule').modal('hide');
    return false;
  };  

  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // 定型メッセージ関連ポップアップ
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  @if (session('add_template_success_flushMsg'))
  $(function () {toastr.success('{{ session('add_template_success_flushMsg') }}');});
  @endif
  @if (session('add_template_error_flushMsg'))
  $(function () {toastr.error('{{ session('add_template_error_flushMsg') }}');});
  @endif

  @if (session('edit_template_success_flushMsg'))
  $(function () {toastr.success('{{ session('edit_template_success_flushMsg') }}');});
  @endif
  @if (session('edit_template_error_flushMsg'))
  $(function () {toastr.error('{{ session('edit_template_error_flushMsg') }}');});
  @endif

  @if (session('del_template_success_flushMsg'))
  $(function () {toastr.success('{{ session('del_template_success_flushMsg') }}');});
  @endif
  @if (session('del_template_error_flushMsg'))
  $(function () {toastr.error('{{ session('del_template_error_flushMsg') }}');});
  @endif



  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // 定型メッセージ取得
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  let templateMessages = null;
  $.ajax(
    {url: '{{route('template.get')}}', type:'get'})
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
        zIndex:1070,
        revert:true,
        revertDuration:0
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
          url: '{{route('schedule.get')}}',
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


</script>
@stop