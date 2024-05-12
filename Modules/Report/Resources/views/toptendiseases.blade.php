@extends('layouts.app')

@section('title')
{{ $page_title }}
@endsection

@push('stylesheet')
 {{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> --}}
<style>
.colorBox{
    left: 0px;
}
    .highcharts-data-table table {
    border: 1px solid #ccc;
}



.highcharts-data-table td,
.highcharts-data-table th {
    border: 1px solid #ccc;
    padding: 5px;
    font-family: Arial, Monospace;
    font-size: 15px;
}

caption {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
    color: #090e0e;
    text-align: left;
    caption-side: top;
    font-weight: bold
}

</style>
@endpush

{{-- <script src="js/dataTables.buttons.min.js"></script>
<script src="js/buttons.html5.min.js"></script> --}}


@section('content')
<div class="dt-content">

    <!-- Grid -->
    <div class="row">
        <div class="col-xl-12 pb-3">
            <ol class="breadcrumb bg-white">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                <li class="active breadcrumb-item">{{ $sub_title }}</li>
            </ol>
        </div>
        <!-- Grid Item -->
        <div class="col-xl-12">

            <!-- Entry Header -->
            <div class="dt-entry__header">

                <!-- Entry Heading -->
                <div class="dt-entry__heading">
                    <h2 class="dt-page__title mb-0 text-primary"><i class="{{ $page_icon }}"></i> {{ $sub_title }}</h2>
                </div>
                <!-- /entry heading -->
                @if (permission('patientage-add'))
                <button class="btn btn-primary btn-sm" onclick="showFormModal('Add New patientage','Save')">
                    <i class="fas fa-plus-square"></i> Add New
                </button>
                @endif


            </div>
            <!-- /entry header -->

            <!-- Card -->
            <div class="dt-card">

                <!-- Card Body -->
                <div class="dt-card__body">

                    <form id="form-filter" method="GET" action="{{url('ajax-top-ten-diseases')}}">

                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="name">Date Range</label>
                                <input type="text" class="form-control" value="" name="daterange" id="daterange"
                                    placeholder="Select Date">
                            </div>


                            <div class="form-group col-md-3">
                                <label for="name">Select Branch</label>

                            <select class="selectpicker" data-actions-box="true" data-live-search="true" name="hc_id[]" id="hc_id" multiple>
                                    <option value="">Select Branch</option> <!-- Empty option added -->
                                    @foreach($branches as $branch)
                                    <option value="{{$branch->barcode_prefix}}">{{$branch->healthCenter->HealthCenterName}}</option>
                                    @endforeach
                            </select>
                            </div>
                            <div class="col-md-2 warning-searching invisible" id="warning-searching">
                                <span class="text-danger" id="warning-message">Searching...Please Wait</span>
                                <span class="spinner-border text-danger"></span>
                            </div>

                            <div class="form-group col-md-4 pt-24">
                                 <button type="button" id="refresh" class="btn btn-danger btn-sm float-right mr-2 refresh">
                                <i class="fas fa-sync-alt"></i></button>

                                <button type="button" id="search" class="btn btn-primary btn-sm float-right mr-2">
                                    <i class="fas fa-search"></i>
                                </button>

                               
                            </div>
                        </div>

                    <div class="row">
                        <div class="col-md-12">
                            <figure class="highcharts-figure position-relative">
                            <div class="colorBox d-flex">
                                    <p class="mb-0 d-flex align-items-center"><span></span>0 - 100</p>
                                    <p class="mb-0 d-flex align-items-center"><span></span>101 - 150</p>
                                    <p class="mb-0 d-flex align-items-center"><span></span>>151</p>
                                </div>
                                <div id="container_diseases"></div>
                            </figure>
                        </div>
                    </div>

                    </form>



                </div>
                <!-- /card body -->

            </div>
            <!-- /card -->

        </div>
        <!-- /grid item -->

    </div>
    <!-- /grid -->

</div>
@endsection

@push('script')
 {{-- <script src="js/dataTables.buttons.min.js"></script>
 <script src="js/buttons.html5.min.js"></script> --}}
 {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}
<script src="js/highcharts.js"></script>
<script src="js/series-label.js"></script>
<script src="js/exporting.js"></script>
<script src="js/export-data.js"></script>
<script src="js/accessibility.js"></script>

<script>
 var table;
 $(document).ready(function(){
 var start = moment().subtract(29, 'days');
    var end = moment();

     $('input[name="daterange"]').daterangepicker({
        startDate: start,
        endDate: end,
        showDropdowns: true,
        linkedCalendars: false,
        ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        'This Quarter': [moment().startOf('quarter'), moment().endOf('quarter')],
        'This Year': [moment().startOf('year'), moment().endOf('year')]
        }
    });

     $('.daterangepicker').mouseleave(function() {
        $(this).hide();
    });
      $('input[name="daterange"]').click(function() {
        $('.daterangepicker').show();
    });



 });




$('#refresh').click(function(){
    $('#daterange').val('');


    $('.selectpicker').selectpicker('val', '');
    $('#container_diseases').html('');
});

$('#search').click(function() {
    var daterange = $('#daterange').val();
    var hc_id = $('#hc_id').val();
    console.log(daterange);
    const parts = daterange.split(" - ");

// Get the first date.
    const fdate = parts[0];

// Get the second date.
    const ldate = parts[1];
    console.log(fdate,ldate,hc_id)


    $.ajax({
        url: "{{ url('ajax-top-ten-diseases') }}",
        type: "get",
        data: { starting_date: fdate, ending_date: ldate, hc_ids: hc_id },
        dataType: "html",
        beforeSend: function(){
            $('#warning-searching').removeClass('invisible');
        },
        complete: function(){
            $('#warning-searching').addClass('invisible');
        },
        success: function(data) {
            $('#container_diseases').html(data);
        },
        error: function(xhr, ajaxOption, thrownError) {
            console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
        }
    });
});


</script>
@endpush
