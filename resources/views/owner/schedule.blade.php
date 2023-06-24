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
  let tmp_schedule_date = $('#tmp_schedule_date')
  let modal_add_schedule = $('#add_schedule');
  let modal_edit_schedule = $('#edit_schedule')


  // /_/_/_/_/_/_/_/_/_/_/_
  // 編集モーダル閉じる前
  // /_/_/_/_/_/_/_/_/_/_/_
  modal_edit_schedule.on('hide.bs.modal', function(){
    // 編集されずにモーダルが閉じた場合、元の位置に戻す
    // dropイベントのrevertは、モーダルを閉じる際に実行できないため
    // 以下のようにイベント単位で実行している。
    if (tmp_schedule_date.data('isChange').toLowerCase() != 'true')
    {
      const msg_id = modal_edit_schedule.find('.msg_id').val()
      let event = calendar.getEventById(msg_id)
      event.setStart(tmp_schedule_date.data('oldStart'))
      event.setEnd(tmp_schedule_date.data('oldEnd'))
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
      .fail(function () {
        toastr.error('スケジュール追加に失敗しました。');
      });
    }
    modal_add_schedule.find('input[name=date]').val('');
    modal_add_schedule.find('select[name=hh]').val('00');
    modal_add_schedule.find('select[name=mm]').val('00');
    modal_add_schedule.find('input[name=title]').val('');
    modal_add_schedule.find('input[name=message_id]').val('');
    modal_add_schedule.find('textarea[name=content]').val('');
    modal_add_schedule.find('input[name=has_file]').val("0");
    modal_add_schedule.find('input.filename_view').val('');
    modal_add_schedule.find('p.image_preview').empty();
    modal_add_schedule.find('input[name=image_id]').val('');
    modal_add_schedule.find('.title_color')[0].checked = true
    modal_add_schedule.find('.title_color')[0].dispatchEvent(new Event('change'));
    modal_add_schedule.modal('hide');
    return false; 
  };


  // /_/_/_/_/_/_/_/_/_/_/_
  // スケジュール編集
  // /_/_/_/_/_/_/_/_/_/_/_
  function submitEditSchedule(e){
    const msg = 'スケジュールを更新してよろしいですか?'
    if(window.confirm(msg)){
      tmp_schedule_date.data('isChange','true')
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
      .fail(function () {
        toastr.error('スケジュール更新に失敗しました。');
      });
    }
    // $('#edit_schedule').modal('hide');
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
        let event = calendar.getEventById(message_id)
        event.remove();
        toastr.success('スケジュールを削除しました。');
      })
      .fail(function () {
        toastr.error('スケジュール削除に失敗しました。');
      });
    }
    $('#edit_schedule').modal('hide');
    return false;
  };  

  @if (session('edit_template_complate_flushMsg'))
  $(function () {toastr.success('{{ session('edit_template_complate_flushMsg') }}');});
  @endif


  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // 定型メッセージ取得
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
    const data = templateMessages.find((v) => v.id == $(this).data('msgid'));
    const modal = $('#edit_template')
    modal.on('show.bs.modal', function(){
      modal.find('.msg_id').val(data.id)
      modal.find('.title_form').val(data.title)
      modal.find('.content_form').val(data.content)
      $.each(modal.find('.title_color'), function(index, elem) {
        if (elem.value == data.title_color)
        { 
          elem.checked = true
          elem.dispatchEvent(new Event('change'));
        }
      })
      let preview = modal.find('.image_preview').get(0);
      let text_form = modal.find('.filename_view');
      let has_image = "0"
      preview.innerHTML = '';
      text_form.val(null);

      const img = data.images[0]
      if (typeof img !== "undefined")
      { 
        has_image = "1"
        const imgElem = document.createElement('img')
        imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
        preview.appendChild(imgElem);
        text_form.val(img.org_name)
      }
      modal.find('.has_file').val(has_image)
    })
    modal.modal('toggle');
  })

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
    // editable:true,
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
      const start = info.date;
      const stYYYY = start.getFullYear();
      const stMonth = ('00'+(Number(start.getMonth())+1)).slice(-2);
      const stDate = ('00'+start.getDate()).slice(-2);
      const calendarDate = `${stYYYY}-${stMonth}-${stDate}`

      modal_add_schedule.on('show.bs.modal', function(){
        modal_add_schedule.find('.datetime_form').val(calendarDate)
        modal_add_schedule.find('input[name=title]').val('');
        modal_add_schedule.find('input[name=message_id]').val('');
        modal_add_schedule.find('textarea[name=content]').val('');
        modal_add_schedule.find('input[name=has_file]').val("0");
        modal_add_schedule.find('input.filename_view').val('');
        modal_add_schedule.find('p.image_preview').empty();
        modal_add_schedule.find('input[name=image_id]').val('');
        modal_add_schedule.find('.title_color')[0].checked = true
        modal_add_schedule.find('.title_color')[0].dispatchEvent(new Event('change'));
      })
      modal_add_schedule.modal('show');

    },

    // 登録スケジュールクリックイベント
    eventClick:(e)=>{
      
      const start_str = e.event.extendedProps.plan_at
      const calendarDate = start_str.split(' ')[0]
      const hhmmss = start_str.split(' ')[1]
      const calendarHh = hhmmss.split(':')[0]
      const calendarMm = hhmmss.split(':')[1]

      modal_edit_schedule.on('show.bs.modal', function(){
        tmp_schedule_date.data('isChange','false')
        tmp_schedule_date.data('oldStart',e.event.start)
        tmp_schedule_date.data('oldEnd',e.event.end)
  
        modal_edit_schedule.find('.datetime_form').val(calendarDate)
        modal_edit_schedule.find('.hh_form').val(calendarHh)
        modal_edit_schedule.find('.mm_form').val(calendarMm)
        modal_edit_schedule.find('.msg_id').val(e.event.id)
        modal_edit_schedule.find('.title_form').val(e.event.title)
        modal_edit_schedule.find('.content_form').val(e.event.extendedProps.content)
        $.each(modal_edit_schedule.find('.title_color'), function(index, elem) {
          if (elem.value == e.event.backgroundColor)
          { 
            elem.checked = true
            elem.dispatchEvent(new Event('change'));
          }
        })
        let preview = modal_edit_schedule.find('.image_preview').get(0);
        let text_form = modal_edit_schedule.find('.filename_view');
        let has_image = "0"
        preview.innerHTML = '';
        text_form.val(null);

        const img = e.event.extendedProps.images[0];
        if (typeof img !== "undefined")
        { 
          has_image = "1"
          const imgElem = document.createElement('img')
          imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
          preview.appendChild(imgElem);
          text_form.val(img.org_name)
        }
        modal_edit_schedule.find('.has_file').val(has_image)
      })
      modal_edit_schedule.modal('show');
    },

    // カレンダー内ドロップイベント
    eventDrop:(e)=>{
      const start_str = e.oldEvent.extendedProps.plan_at
      const hhmmss = start_str.split(' ')[1]
      const calendarHh = hhmmss.split(':')[0]
      const calendarMm = hhmmss.split(':')[1]

      const start = e.event.start;
      const stYYYY = start.getFullYear();
      const stMonth = ('00'+(Number(start.getMonth())+1)).slice(-2);
      const stDate = ('00'+start.getDate()).slice(-2);
      const calendarDate = `${stYYYY}-${stMonth}-${stDate}`

      modal_edit_schedule.on('show.bs.modal', function(){

        tmp_schedule_date.data('isChange','false')
        tmp_schedule_date.data('oldStart',e.oldEvent.start)
        tmp_schedule_date.data('oldEnd',e.oldEvent.end)

        modal_edit_schedule.find('.datetime_form').val(calendarDate)
        modal_edit_schedule.find('.hh_form').val(calendarHh)
        modal_edit_schedule.find('.mm_form').val(calendarMm)
        modal_edit_schedule.find('.msg_id').val(e.event.id)
        modal_edit_schedule.find('.title_form').val(e.event.title)
        modal_edit_schedule.find('.content_form').val(e.event.extendedProps.content)
        $.each(modal_edit_schedule.find('.title_color'), function(index, elem) {
          if (elem.value == e.event.backgroundColor)
          { 
            elem.checked = true
            elem.dispatchEvent(new Event('change'));
          }
        })
        let preview = modal_edit_schedule.find('.image_preview').get(0);
        let text_form = modal_edit_schedule.find('.filename_view');
        let has_image = "0"
        preview.innerHTML = '';
        text_form.val(null);
        const images = e.event.extendedProps.images;
        if (images.length)
        { 
          has_image = "1"
          let file_list = []
          let img_id_list = []
          $.each(images, function(index,img) {
            const imgElem = document.createElement('img')
            imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
            preview.appendChild(imgElem);
            file_list.push(img.org_name)
          })
          text_form.val(file_list.join(' '))
        }
        modal_edit_schedule.find('.has_file').val(has_image)
      });
      modal_edit_schedule.modal('show');
    },

    // 定型メッセージのカレンダードロップイベント
    eventReceive: function(info) {     
      const data = templateMessages.find((v) => v.id == info.draggedEl.getAttribute('data-msgid'));
      const start = info.event.start;
      const stYYYY = start.getFullYear();
      const stMonth = ('00'+(Number(start.getMonth())+1)).slice(-2);
      const stDate = ('00'+start.getDate()).slice(-2);
      const calendarDate = `${stYYYY}-${stMonth}-${stDate}`

      // const modal = $('#add_schedule');

      modal_add_schedule.on('show.bs.modal', function(){
        modal_add_schedule.find('.msg_id').val(data.id)
        modal_add_schedule.find('.datetime_form').val(calendarDate)
        modal_add_schedule.find('.title_form').val(data.title)
        modal_add_schedule.find('.content_form').val(data.content)
        $.each(modal_add_schedule.find('.title_color'), function(index, elem) {
          if (elem.value == data.title_color)
          { 
            elem.checked = true
            elem.dispatchEvent(new Event('change'));
          }
        })
        let preview = modal_add_schedule.find('.image_preview').get(0);
        let text_form = modal_add_schedule.find('.filename_view');
        let img_id_form = modal_add_schedule.find('.img_id');
        let has_image = "0"
        preview.innerHTML = '';
        text_form.val(null);
        if (data.images.length)
        { 
          has_image = "1"
          let file_list = []
          let img_id_list = []
          $.each(data.images, function(index,img) {
            const imgElem = document.createElement('img')
            imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
            preview.appendChild(imgElem);
            file_list.push(img.org_name)
            img_id_list.push(img.image_id)
          })
          text_form.val(file_list.join(' '))
          img_id_form.val(img_id_list.join(','))
        }
        modal_add_schedule.find('.has_file').val(has_image)
      });
      modal_add_schedule.on('hide.bs.modal', function(){
        info.event.remove()
      });
      modal_add_schedule.modal('show');
    },
  });

    //   eventReceive: function(info) {     
  //     const data = templateMessages.find((v) => v.id == info.draggedEl.getAttribute('data-msgid'));
  //     const start = info.event.start;
  //     const stYYYY = start.getFullYear();
  //     const stMonth = ('00'+(Number(start.getMonth())+1)).slice(-2);
  //     const stDate = ('00'+start.getDate()).slice(-2);
  //     const calendarDate = `${stYYYY}-${stMonth}-${stDate}`

  //     const modal = $('#add_schedule');

  //     modal.on('show.bs.modal', function(){
  //       modal.find('.msg_id').val(data.id)
  //       modal.find('.datetime_form').val(calendarDate)
  //       modal.find('.title_form').val(data.title)
  //       modal.find('.content_form').val(data.content)
  //       $.each(modal.find('.title_color'), function(index, elem) {
  //         if (elem.value == data.title_color)
  //         { 
  //           elem.checked = true
  //           elem.dispatchEvent(new Event('change'));
  //         }
  //       })
  //       let preview = modal.find('.image_preview').get(0);
  //       let text_form = modal.find('.filename_view');
  //       let img_id_form = modal.find('.img_id');
  //       let has_image = "0"
  //       preview.innerHTML = '';
  //       text_form.val(null);
  //       if (data.images.length)
  //       { 
  //         has_image = "1"
  //         let file_list = []
  //         let img_id_list = []
  //         $.each(data.images, function(index,img) {
  //           const imgElem = document.createElement('img')
  //           imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
  //           preview.appendChild(imgElem);
  //           file_list.push(img.org_name)
  //           img_id_list.push(img.image_id)
  //         })
  //         text_form.val(file_list.join(' '))
  //         img_id_form.val(img_id_list.join(','))
  //       }
  //       modal.find('.has_file').val(has_image)
  //     });
  //     modal.on('hide.bs.modal', function(){
  //       info.event.remove()
  //     });
  //     modal.modal('show');
  //   },
  // });

  calendar.render();


</script>
@stop