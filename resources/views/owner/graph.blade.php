@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>グラフ</h1>
@stop

@section('content')

<div class="row mb-3">
  <div class="col-md-3">
    <div class="card">
      <div class="card-header border-0">
        <h3 class="card-title" style="color:#007bff">総友だち数</h3>
      </div>
      <div class="card-body py-1">

        <div class="d-flex align-items-center mb-3 border-bottom">
          <div class="col-9 px-0">今日</div>
          <div class="col-2 pl-0 pr-2 text-right people_num">{{$friend_today}}</div>
          <div class="col-1 px-0"></div>
        </div>
        <!-- /.d-flex -->

        <div class="d-flex align-items-center mb-3 border-bottom">
          <div class="col-9 px-0">前日</div>
          <div class="col-2 pl-0 pr-2 text-right people_num">{{$friend_1_ago}}</div>
          <div class="col-1 px-0 text-right diff_val {{$diff_1_ago > 0 ? 'text-info':'text-danger'}}">{{$diff_1_ago}}</div>
        </div>
        <!-- /.d-flex -->

        <div class="d-flex align-items-center mb-3 border-bottom">
          <div class="col-9 px-0">７日前</div>
          <div class="col-2 pl-0 pr-2 text-right people_num">{{$friend_7_ago}}</div>
          <div class="col-1 px-0 text-right diff_val {{$diff_7_ago > 0 ? 'text-info':'text-danger'}}">{{$diff_7_ago}}</div>
        </div>
        <!-- /.d-flex -->

        <div class="d-flex align-items-center mb-0">
          <div class="col-9 px-0">３０日前</div>
          <div class="col-2 pl-0 pr-2 text-right people_num">{{$friend_30_ago}}</div>
          <div class="col-1 px-0 text-right diff_val {{$diff_30_ago > 0 ? 'text-info':'text-danger'}}">{{$diff_30_ago}}</div>
        </div>
        <!-- /.d-flex -->
      </div>
    </div>
  </div>
  <!-- /.col -->
  <div class="col-md-3"></div>
  <!-- /.col -->

  <div class="col-md-3">
    <div class="card">
      <div class="card-header border-0">
        <h3 class="card-title" style="color:#dc3545">総退会数</h3>
      </div>
      <div class="card-body py-1">

        <div class="d-flex align-items-center mb-3 border-bottom">
          <div class="col-9 px-0">今日</div>
          <div class="col-2 pl-0 pr-2 text-right people_num">{{$cancell_today}}</div>
          <div class="col-1 px-0"></div>
        </div>
        <!-- /.d-flex -->

        <div class="d-flex align-items-center mb-3 border-bottom">
          <div class="col-9 px-0">前日</div>
          <div class="col-2 pl-0 pr-2 text-right people_num">{{$cancell_1_ago}}</div>
          <div class="col-1 px-0 text-right diff_val {{$diff_cancell_1_ago > 0 ? 'text-info':'text-danger'}}">{{$diff_cancell_1_ago}}</div>
        </div>
        <!-- /.d-flex -->

        <div class="d-flex align-items-center mb-3 border-bottom">
          <div class="col-9 px-0">７日前</div>
          <div class="col-2 pl-0 pr-2 text-right people_num">{{$cancell_7_ago}}</div>
          <div class="col-1 px-0 text-right diff_val {{$diff_cancell_7_ago > 0 ? 'text-info':'text-danger'}}">{{$diff_cancell_7_ago}}</div>
        </div>
        <!-- /.d-flex -->

        <div class="d-flex align-items-center mb-0">
          <div class="col-9 px-0">３０日前</div>
          <div class="col-2 pl-0 pr-2 text-right people_num">{{$cancell_30_ago}}</div>
          <div class="col-1 px-0 text-right diff_val {{$diff_cancell_30_ago > 0 ? 'text-info':'text-danger'}}">{{$diff_cancell_30_ago}}</div>
        </div>
        <!-- /.d-flex -->
      </div>
    </div>
  </div>
  <!-- /.col -->
</div>



<div class="row  mb-5">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header border-0 pb-0">
        <h3 class="card-title">友だち追加</h3>
        <div class="card-tools">
          <select class="custom-select select_point" style="width:auto;" onChange="changeAddChartData(this.value)">
            <option value="7">7 Days</option>
            <option value="30">30 Days</option>
          </select>
        </div>
      </div>
      <div class="card-body">
        <canvas class="chart" id="add_chart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->    
  </div>
  <!-- /.col -->

  <div class="col-md-6">
    <div class="card">
      <div class="card-header border-0 pb-0">
        <h3 class="card-title">友だち退会</h3>
        <div class="card-tools">
          <select class="custom-select select_point" style="width:auto;" onChange="changeCancellChartData(this.value)">
            <option value="7">7 Days</option>
            <option value="30">30 Days</option>
          </select>
        </div>
      </div>
      <div class="card-body">
        <canvas class="chart" id="cancell_chart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->    
  </div>
  <!-- /.col -->
</div>



<div class="row">
  <div class="col-md-10">
    <table id="friend_table" class="table table-striped table-bordered" style="table-layout:fixed;">
      <thead>
        <tr>
          <th class="text-center">日付</th>
          <th class="text-center">友だち追加</th>
          <th class="text-center">友だち退会</th>
        </tr>
      </thead>
      <tbody>

        @if(isset($posts[0]))
          @foreach ($posts as $post)
            <tr>
              <td class="omit_text">{{$post->action_date}}</td>
              <td class="omit_text">{{$post->add_cnt}}</td>
              <td class="omit_text">{{$post->cancell_cnt}}</td>
            </tr>
          @endforeach
        @endif


      </tbody>
    </table>
  </div>
</div>


@stop

@section('css')
  <link rel="stylesheet" href="{{ asset('build/assets/graph.css')}}">
@stop


@section('js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
<script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script>

<script>

  // テーブル
  $.extend( $.fn.dataTable.defaults, { language: {url:"https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" } }); 
  $('#friend_table').DataTable({
    paging:true,
    lengthChange:false,
    searching:false,
    ordering:true,
    info:false,
    autoWidth: false,
    responsive:false,
    columnDefs:[
      { targets:0, width:120},
    ],
    "order": [
      [0, 'desc'],
    ],
  });
  // テーブル


  const labelColor = '#9b9b9b'
  const addBaseColor = '#007bff'
  const cancelBaseColor = '#dc3545'

  // 友だち追加グラフ
  let addChartCanvas = $('#add_chart').get(0).getContext('2d')
  let addChartData = {
    labels: @json($friend_graph_label),
    datasets: [
      {
        label: '友だち追加数',
        fill: false,
        borderWidth: 3,                   // 線の太さ
        lineTension: 0,
        spanGaps: true,
        borderColor: addBaseColor,
        pointRadius: 5,                   // ポイントサイズ（通常時）
        pointHoverRadius: 7,              // ポイントサイズ（ホバー時）
        pointColor: addBaseColor,
        pointBackgroundColor: '#ffffff',
        pointHoverBackgroundColor: addBaseColor,
        data: @json($friend_graph_data),
      }
    ]
  }

  let addChartOptions = {
    maintainAspectRatio: false,
    responsive: true,
    legend: {
      display: false
    },

    scales: {
      xAxes: [{
        ticks: {
          fontColor: labelColor
        },
        gridLines: {
          display: false,
          color: labelColor,
          drawBorder: false
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          stepSize: Math.round(Math.max(...@json($friend_graph_data)) / 10),
          fontColor: labelColor
        },
        gridLines: {
          display: true,
          borderDash: [1, 20],
          color: labelColor,
          drawBorder: false
        }
      }]
    }
  }

  addChart = new Chart(addChartCanvas, {
    type: 'line',
    data: addChartData,
    options: addChartOptions
  })





  function changeAddChartData(days){
    let data;
    let labels;
    $.ajax({
      url: '{{route('owner.graph.change.friend')}}',
      method: 'GET',
      async: true,
      data: {
        days: days
      }
    })
    .done(function (data) {
      addChart.data.labels = data.friend_graph_label;
      addChart.data.datasets[0].data = data.friend_graph_data;
      addChart.options.scales.yAxes[0].ticks.stepSize = Math.round(Math.max(...data.friend_graph_data) / 10);
      addChart.update(); 
    })
    .fail(function (data) {
      console.log((data.responseJSON.message))
    });
  }
  // 友だち追加グラフ


  
  // 友だち退会グラフ
  let cancellChartCanvas = $('#cancell_chart').get(0).getContext('2d')
  let cancellChartData = {
    labels: @json($friend_graph_label),
    datasets: [
      {        
        label: '友だち退会数',
        fill: false,
        borderWidth: 3,
        lineTension: 0,
        spanGaps: true,
        borderColor: cancelBaseColor,
        pointRadius: 5,
        pointHoverRadius: 7,
        pointColor: cancelBaseColor,
        pointBackgroundColor: '#ffffff',
        pointHoverBackgroundColor: cancelBaseColor,
        data: @json($friend_graph_data),
      }
    ]
  }
  let cancellChartOptions = {
    maintainAspectRatio: false,
    responsive: true,
    legend: {
      display: false
    },
    scales: {
      xAxes: [{
        ticks: {
          fontColor: labelColor
        },
        gridLines: {
          display: false,
          color: labelColor,
          drawBorder: false
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          stepSize: Math.round(Math.max(...@json($friend_graph_data)) / 10),
          fontColor: labelColor
        },
        gridLines: {
          display: true,
          borderDash: [1, 20],
          color: labelColor,
          drawBorder: false
        }
      }]
    }
  }
  cancellChart = new Chart(cancellChartCanvas, {
    type: 'line',
    data: cancellChartData,
    options: cancellChartOptions
  })

  function changeCancellChartData(days){
    let data;
    let labels;
    $.ajax({
      url: '{{route('owner.graph.change.friend')}}',
      method: 'GET',
      async: true,
      data: {
        days: days
      }
    })
    .done(function (data) {
      cancellChart.data.labels = data.friend_graph_label;
      cancellChart.data.datasets[0].data = data.friend_graph_data;
      cancellChart.options.scales.yAxes[0].ticks.stepSize = Math.round(Math.max(...data.friend_graph_data) / 10);
      cancellChart.update(); 
    })
    .fail(function (data) {
      console.log((data.responseJSON.message))
    });
  }  
  // 友だち退会グラフ
    
    
</script>

@stop