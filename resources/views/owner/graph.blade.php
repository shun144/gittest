@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>グラフ</h1>
@stop

@section('content')

<div class="row">
  <div class="col-md-5">
    <div>友だち追加数</div>
    <table id="add_friend_table" class="table table-striped table-bordered" style="table-layout:fixed;">
      <thead>
        <tr>
          @foreach (array("今日","前日","7日前","30日前") as $col)
          <th class="text-center">{{$col}}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="omit_text">10</td>
          <td class="omit_text">20</td>
          <td class="omit_text">13</td>
          <td class="omit_text">5</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>


<div class="row">
  <div class="col-md-5">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">友だち追加</h3>

        {{-- <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div> --}}

      </div>
      <div class="card-body">
        <div class="chart">
          <canvas id="areaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">友だち追加</h3>

        {{-- <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div> --}}

      </div>
      <div class="card-body">
        <div class="chart">
          <canvas id="areaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
        </div>
      </div>
    </div>
  </div>


</div>

@stop

@section('css')
    {{-- ページごとCSSの指定
    <link rel="stylesheet" href="/css/xxx.css">
    --}}
@stop


@section('js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>

<script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script>

<script>
  $(function () {
    $.extend( $.fn.dataTable.defaults, { 
      language: {url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" } 
    }); 
    $('#add_friend_table').DataTable({
      paging:false,
      lengthChange:false,
      searching:false,
      ordering:true,
      info:false,
      autoWidth: false,
      responsive:false,
      columnDefs:[
        // { targets:0, width:55},
      ],
    });

    var data = @json($data);
    console.log(data);

    // var d = new Date()
    // max = 7;
    // for (var i = 0; i <= max; i++) {
    //   console.log(d.getMonth() + 1 + '/' + d.getDate()); 
    //   d.setDate(d.getDate() - 1);
    // }





    var areaChartCanvas = $('#areaChart').get(0).getContext('2d')
    var areaChartData = {
      // labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      labels  : @json($data),
      datasets: [
        {
          label               : 'Digital Goods',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [28, 48, 40, 19, 86, 27, 90]
        },
        // {
        //   label               : 'Electronics',
        //   backgroundColor     : 'rgba(210, 214, 222, 1)',
        //   borderColor         : 'rgba(210, 214, 222, 1)',
        //   pointRadius         : false,
        //   pointColor          : 'rgba(210, 214, 222, 1)',
        //   pointStrokeColor    : '#c1c7d1',
        //   pointHighlightFill  : '#fff',
        //   pointHighlightStroke: 'rgba(220,220,220,1)',
        //   data                : [65, 59, 80, 81, 56, 55, 40]
        // },
      ]
    }

    var areaChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines : {
            display : false,
          }
        }],
        yAxes: [{
          gridLines : {
            display : false,
          }
        }]
      }
    }

    // This will get the first returned node in the jQuery collection.
    new Chart(areaChartCanvas, {
      type: 'line',
      data: areaChartData,
      options: areaChartOptions
    })
})
</script>
@stop