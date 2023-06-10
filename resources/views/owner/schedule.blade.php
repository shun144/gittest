@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>配信</h1>
@stop

@section('content')

  <div class="row">
    
    <button class="btn btn-success" data-toggle="modal" data-target="#send_message">
      即時配信
    </button>
  </div>


  <div class="row">
    <div class="col-md-3">        
      <div class="sticky-top mb-3">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">定型メッセージ</h4>
            
            <button class="btn btn-success" data-toggle="modal" data-target="#add_template">
              作成
            </button>
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
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>

  @include('owner.modals.send_message')
  @include('owner.modals.add_template')
  @include('owner.modals.edit_template')

  @include('owner.modals.add_schedule')
  @include('owner.modals.edit_schedule')
@stop


@section('css')
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fullcalendar/main.min.css') }}"> 
  @vite(['resources/sass/owner/component.scss'])
@stop

@section('js')
  <script src="{{ asset('vendor/adminlte/plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/fullcalendar/main.min.js') }}"></script>
  @vite(['resources/js/owner/component.js'])
<script>

  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // 定例メッセージ取得
  let templateMessages = null;
  $.ajax({url: '{{route('template.get')}}', type:'get'})
  .then(
  function (data) {
    templateMessages = data;
  },
  function () {console.error("読み込み失敗");}); 
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_

  jQuery(function($) {

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    //  定例メッセージクリックイベント
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
        if (data.images.length)
        { 
          has_image = "1"
          let file_list = []
          let img_id_list = []
          $.each(data.images, function(index,img) {
            const imgElem = document.createElement('img')
            imgElem.src = '{{url(config('app.access_storage.image'))}}/' + img.save_name
            preview.appendChild(imgElem);
            file_list.push(img.org_name)
          })
          text_form.val(file_list.join(' '))
        }
        modal.find('.has_file').val(has_image)
      })
      modal.modal('toggle');
    })
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_





    // function post_send() {
    //   $.ajax({
    //     url:'{{ route('owner.schedule')}}',
    //     type:'post',
    //     headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
    //     dataType:'text',
    //     async:true,
    //     cache:false,
    //     data: {
    //       id: 1,
    //       name: 'annkw'
    //     }
    //   }).done(function(data) {
    //     console.log('成功!!')
    //   }).fail(function(jqXHR, textStatus, errorThrown){
    //     console.log(jqXHR);
    //     console.log(textStatus);
    //     console.log(errorThrown);
    //   })
    // }

    function ini_events(ele) {
      ele.each(function () {
        $(this).draggable({
          zIndex : 1070,
          revert : true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        })
      })
    }
    ini_events($('#external-events div.external-event'))


    let date = new Date()
    let d = date.getDate(), m = date.getMonth(), y = date.getFullYear()

    let Calendar = FullCalendar.Calendar;
    let Draggable = FullCalendar.Draggable;
    let containerEl = document.getElementById('external-events');
    let checkbox = document.getElementById('drop-remove');
    let calendarEl = document.getElementById('calendar');

    // initialize the external events
    // -----------------------------------------------------------------
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

    let calendar = new Calendar(calendarEl, {
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
      eventSources: [{url:'{{route('schedule.get')}}'}],

      // カレンダー日付クリックイベント
      dateClick: (info)=>{
        const start = info.date;
        const stYYYY = start.getFullYear();
        const stMonth = ('00'+(Number(start.getMonth())+1)).slice(-2);
        const stDate = ('00'+start.getDate()).slice(-2);
        const calendarDate = `${stYYYY}-${stMonth}-${stDate}`

        const modal = $('#add_schedule')
        modal.on('show.bs.modal', function(){
          modal.find('.datetime_form').val(calendarDate)
        })
        modal.modal('toggle');
      },

      // 登録スケジュールクリックイベント
      eventClick:(e)=>{
        const modal = $('#edit_schedule')
        const start_str = e.event.extendedProps.plan_at
        const calendarDate = start_str.split(' ')[0]
        const hhmmss = start_str.split(' ')[1]
        const calendarHh = hhmmss.split(':')[0]
        const calendarMm = hhmmss.split(':')[1]

        modal.on('show.bs.modal', function(){
          modal.find('.datetime_form').val(calendarDate)
          modal.find('.hh_form').val(calendarHh)
          modal.find('.mm_form').val(calendarMm)
          modal.find('.msg_id').val(e.event.id)
          modal.find('.title_form').val(e.event.title)
          modal.find('.content_form').val(e.event.extendedProps.content)
          $.each(modal.find('.title_color'), function(index, elem) {
            if (elem.value == e.event.backgroundColor)
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
          const images = e.event.extendedProps.images;
          if (images.length)
          { 
            has_image = "1"
            let file_list = []
            let img_id_list = []
            $.each(images, function(index,img) {
              const imgElem = document.createElement('img')
              imgElem.src = '{{url(config('app.access_storage.image'))}}/' + img.save_name
              preview.appendChild(imgElem);
              file_list.push(img.org_name)
            })
            text_form.val(file_list.join(' '))
          }
          modal.find('.has_file').val(has_image)
        })
        modal.modal('toggle');
      },
      
      eventDrop:(e)=>{
        const modal = $('#edit_schedule')
        const start_str = e.event.extendedProps.plan_at
        const calendarDate = start_str.split(' ')[0]
        const hhmmss = start_str.split(' ')[1]
        const calendarHh = hhmmss.split(':')[0]
        const calendarMm = hhmmss.split(':')[1]

        modal.on('show.bs.modal', function(){
          modal.find('.datetime_form').val(calendarDate)
          modal.find('.hh_form').val(calendarHh)
          modal.find('.mm_form').val(calendarMm)
          modal.find('.msg_id').val(e.event.id)
          modal.find('.title_form').val(e.event.title)
          modal.find('.content_form').val(e.event.extendedProps.content)
          $.each(modal.find('.title_color'), function(index, elem) {
            if (elem.value == e.event.backgroundColor)
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
          const images = e.event.extendedProps.images;
          if (images.length)
          { 
            has_image = "1"
            let file_list = []
            let img_id_list = []
            $.each(images, function(index,img) {
              const imgElem = document.createElement('img')
              imgElem.src = '{{url(config('app.access_storage.image'))}}/' + img.save_name
              preview.appendChild(imgElem);
              file_list.push(img.org_name)
            })
            text_form.val(file_list.join(' '))
          }
          modal.find('.has_file').val(has_image)
        });
        modal.on('hide.bs.modal', function(){
          e.revert();
        });
        modal.modal('toggle');

      },

      // 外部イベントのカレンダードロップイベント
      eventReceive: function(info) {        
        const data = templateMessages.find((v) => v.id == info.draggedEl.getAttribute('data-msgid'));
        const start = info.event.start;
        const stYYYY = start.getFullYear();
        const stMonth = ('00'+(Number(start.getMonth())+1)).slice(-2);
        const stDate = ('00'+start.getDate()).slice(-2);
        const calendarDate = `${stYYYY}-${stMonth}-${stDate}`
        const modal = $('#add_schedule');
        modal.on('show.bs.modal', function(){
          modal.find('.msg_id').val(data.id)
          modal.find('.datetime_form').val(calendarDate)
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
          let img_id_form = modal.find('.img_id');
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
              imgElem.src = '{{url(config('app.access_storage.image'))}}/' + img.save_name
              preview.appendChild(imgElem);
              file_list.push(img.org_name)
              img_id_list.push(img.image_id)
            })
            text_form.val(file_list.join(' '))
            img_id_form.val(img_id_list.join(','))
          }
          modal.find('.has_file').val(has_image)
        });
        modal.on('hide.bs.modal', function(){
          info.event.remove()
        });
        modal.modal('toggle');
      },
    });

    calendar.render();

    /* ADDING EVENTS */
    let currColor = '#3c8dbc'
    // Color chooser button
    $('#color-chooser > li > a').click(function (e) {
      e.preventDefault()
      // Save color
      currColor = $(this).css('color')
      // Add color effect to button
      $('#add-new-event').css({
        'background-color': currColor,
        'border-color'    : currColor
      })
    })
    
    $('#add-new-event').click(function (e) {
      e.preventDefault()
      // Get value and make sure it is not null
      let val = $('#new-event').val()
      if (val.length == 0) {return}

      // Create events
      let event = $('<div />')
      event.css({
        'background-color': currColor,
        'border-color'    : currColor,
        'color'           : '#fff'
      }).addClass('external-event')
      event.text(val)
      $('#external-events').prepend(event)

      // Add draggable funtionality
      ini_events(event)

      // Remove event from text input
      $('#new-event').val('')
    })
  })
</script>
@stop