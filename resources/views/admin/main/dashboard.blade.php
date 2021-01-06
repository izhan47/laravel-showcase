@extends('admin.layouts.admin')

@push('styles')
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/chart-js/Chart.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
@endpush

@push('meta-tags')
    <title>{{ config('wagenabled.app_name') }} | Dashboard</title>
@endpush

@section('content')
   	<section class="wag-admin-plan-main-cover-section">
        <div class="row wag-member-block-main">
            <div class="col-sm-6 col-md-4">
                <div class="wag-member-details-block-main">
                    <h2>{{ number_format($users_count) }}</h2>
                    <p>Users</p>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="wag-member-details-block-main">
                    <h2>{{ number_format($pet_pros_count) }}</h2>
                    <p>Pet Pros</p>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="wag-member-details-block-main">
                    <h2>{{ number_format($pet_pro_deals_count) }}</h2>
                    <p>Deals <small>(Pet Pros)</small></p>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="wag-member-details-block-main">
                    <h2>{{ number_format($pet_pro_deal_claimed_count) }}</h2>
                    <p>Deals Claimed <small>(Pet Pros)</small></p>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="wag-member-details-block-main">
                    <h2>{{ number_format($watch_and_learn_deals_count) }}</h2>
                    <p>Deals <small>(Product Reviews)</small></p>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="wag-member-details-block-main">
                    <h2>{{ number_format($watch_and_learn_deal_claimed_count) }}</h2>
                    <p>Deals Claimed <small>(Product Reviews)</small></p>
                </div>
            </div>
        </div>

        <div class="wag-page-main-header-bar">
            <div class="wag-title-bar-main">
                <h1 class="wag-admin-page-title-main">New Users</h1>
            </div>

            <div class="wag-title-and-nemu-block-main">
                <input type="text" value="{{ $monthStart }} - {{ $monthEnd }}" name="datetimes" id="graph_duration" />
            </div>
        </div>

        <div class="wag-graph-main">
            <div id="graph_duration_user_graph">
                <div class="ibox-content">
                    <iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                    <canvas id="lineChartMonthly" height="206" width="443" style="display: block; width: 443px; height: 206px;"></canvas>
                </div>
            </div>
        </div>


    </section>
@endsection

@push("scripts")
	<script src="{{ asset('plugins/chart-js/Chart.min.js') }}"></script>
  <script src="{{ asset('plugins/daterangepicker/daterangepicker.min.js') }}"></script>       
	<script type="text/javascript">

	    $(document).ready(function() {
            $('#graph_duration').daterangepicker();

	    	var ctx = document.getElementById("lineChartMonthly").getContext("2d");
	    	var graph_key, graph_value;
	    	var myChart;

	    	$.ajax({
	    	    url:  "{{ url('admin/get-users-graph-data') }}",
	    	    type: "get",
	    	    data: {
                    monthStart: "{{ $monthStart }}",
                    monthEnd: "{{ $monthEnd }}"
                },
	    	    success: function(data){
    	      		graph_key = data.data["graph_key"];
    	      		graph_value = data.data["graph_value"];

      				var lineData = {
      				    labels: graph_key,
      				    datasets: [
      				        {
      				            label: 'New Users',
      				            backgroundColor: 'rgba(97,97,255,1)',
      				            borderColor: 'rgba(97,97,255,1)',
      				            pointBackgroundColor: "rgba(97,97,255,)",
      				            pointBorderColor: "#fff",
      				            data: graph_value,
      				        }
      				    ]
      				};

      				var Options = {
      		    		responsive: true,
  		       			maintainAspectRatio:false,
  		       		    scales: {
                            xAxes: [{
                                gridLines: {
                                    display:false
                                }
                            }],
  		       		        yAxes: [{
  		       		            ticks: {
  		       		                beginAtZero: true,
  		       		                callback: function(value) {if (value % 1   === 0) {return value;}}
  		       		            },
  		       		        }]
  		       		    }
      				};
      				myChart = new Chart(ctx, { type: 'bar' , data: lineData, options:Options});
	    	    }
	    	});

            $('#graph_duration').daterangepicker({
                maxDate: "{{ $monthEnd }}"
             }, function(start, end, label) {
               getGraphData(start.format('MM-DD-YYYY'), end.format('MM-DD-YYYY'))
             });

		    function getGraphData(monthStart, monthEnd) {
		    	$.ajax({
		    	    url:  "{{ url('admin/get-users-graph-data') }}",
		    	    type: "get",
		    	    data: {
                        monthStart: monthStart,
                        monthEnd: monthEnd
                    },
		    	    success: function(data){
	    	      		myChart.data.labels = data.data["graph_key"];
	                	myChart.data.datasets[0].data = data.data["graph_value"];
	                	myChart.update();
		    	    }
		    	});
		    }
		});


	</script>

@endpush
