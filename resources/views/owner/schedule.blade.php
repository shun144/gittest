@extends('adminlte::page')

@section('title', 'Dashboard')

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

$(function(){

  let Calendar = FullCalendar.Calendar;
  let Draggable = FullCalendar.Draggable;
  let containerEl = document.getElementById('external-events');
  let checkbox = document.getElementById('drop-remove');
  let calendarEl = document.getElementById('calendar');

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

    // $('.external-event').click(function() {
    //   const data = templateMessages.find((v) => v.id == $(this).data('msgid'));
    //   const modal = $('#edit_template')
    //   modal.on('show.bs.modal', function(){
    //     modal.find('.msg_id').val(data.id)
    //     modal.find('.title_form').val(data.title)
    //     modal.find('.content_form').val(data.content)
    //     $.each(modal.find('.title_color'), function(index, elem) {
    //       if (elem.value == data.title_color)
    //       { 
    //         elem.checked = true
    //         elem.dispatchEvent(new Event('change'));
    //       }
    //     })
    //     let preview = modal.find('.image_preview').get(0);
    //     let text_form = modal.find('.filename_view');
    //     let has_image = "0"
    //     preview.innerHTML = '';
    //     text_form.val(null);
    //     if (data.images.length)
    //     { 
    //       has_image = "1"
    //       let file_list = []
    //       let img_id_list = []
    //       $.each(data.images, function(index,img) {
    //         const imgElem = document.createElement('img')
    //         imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
    //         preview.appendChild(imgElem);
    //         file_list.push(img.org_name)
    //       })
    //       text_form.val(file_list.join(' '))
    //     }
    //     modal.find('.has_file').val(has_image)
    //   })
    //   modal.modal('toggle');
    // })
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_


    // let date = new Date()
    // let d = date.getDate(), m = date.getMonth(), y = date.getFullYear()


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

      eventOrderStrict: true,
      eventOrder: "plan_at",

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

          const img = e.event.extendedProps.images[0];
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
      },
      
      // カレンダー内ドロップイベント
      eventDrop:(e)=>{

        const modal = $('#edit_schedule')
        const start_str = e.oldEvent.extendedProps.plan_at
        const hhmmss = start_str.split(' ')[1]
        const calendarHh = hhmmss.split(':')[0]
        const calendarMm = hhmmss.split(':')[1]

        const start = e.event.start;
        const stYYYY = start.getFullYear();
        const stMonth = ('00'+(Number(start.getMonth())+1)).slice(-2);
        const stDate = ('00'+start.getDate()).slice(-2);
        const calendarDate = `${stYYYY}-${stMonth}-${stDate}`


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
              imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
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
        console.log(info.draggedEl);

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
              imgElem.src = '{{url(config('storage.owner.image.template'))}}/' + img.save_name
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
  });

</script>
@stop