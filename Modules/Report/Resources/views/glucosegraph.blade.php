@extends('layouts.app')

@section('title')
{{ $page_title }}
@endsection

@push('stylesheet')

<style>
    .bootstrap-select{
        width: 100% !important;
    }
    .dropdown-menu.inner.show{
    min-width: 10px !important;
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
    color: #040505;
    text-align: left;
    caption-side: top;
    font-weight: bold
}
</style>


@endpush


    <script src="js/highcharts.js"></script>
    <script src="js/series-label.js"></script>
    <script src="js/exporting.js"></script>
    <script src="js/export-data.js"></script>
    <script src="js/accessibility.js"></script>

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

                    <form id="form-filter" method="GET">

                           <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                <label for="name" class="d-block">Date Range</label>
                                 <input type="text" class="form-control  w-100" value="" name="daterange" id="daterange"
                                    placeholder="Select Date" required>
                            </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                <label for="name" class="d-block">Branch</label>
                                <select class="selectpicker w-100" data-live-search="true" name="hc_id" id="hc_id">
                                    <option value="">Select Branch</option> <!-- Empty option added -->
                                    @foreach($branches as $branch)
                                    <option value="{{$branch->barcode_prefix}}">{{$branch->healthCenter->HealthCenterName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                <label for="name" class="d-block">Patient</label>
                                <select class="selectpicker w-100 disable" data-live-search="true" name="registration_id" id="registration_id" required disabled>
                                    <option value="">Select Registration ID</option> <!-- Empty option added -->
                                </select>
                            </div>
                            </div>
                            
                            <div class="col-lg-1">
                                <div class="warning-searching invisible" id="warning-searching">
                                <span class="text-danger" id="warning-message"></span>
                                <span class="spinner-border text-danger"></span>
                            </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="form-group pt-24">
                               

                                <button type="button" id="refresh" class="btn btn-danger btn-sm float-right mr-2 refresh">
                                <i class="fas fa-sync-alt"></i></button>
                                 <button type="button" id="search" class="btn btn-primary btn-sm float-right mr-2">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            </div>
                        </div>

                        <div class="row d-none" id="highcharts">
                            <div class="col-md-12">
                                <figure class="highcharts-figure">
                                    <div id="container_glucose"></div>
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



<script>

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

      $('#hc_id').change(function () {
        
        var hcId = $(this).val();
      

      if (hcId) {
         
            $.ajax({
                type: 'GET',
                url: '{{ route("get-patients", "hcId") }}'.replace('hcId', hcId),
                beforeSend: function(){
                     $('#registration_id').prop('disabled', true);
                     $('#warning-searching').removeClass('invisible');
                },
                success: function (data) {
                     $('#registration_id').prop('disabled', false);
                     $('#warning-searching').addClass('invisible');
                     
            // Add the default empty option
            $('#registration_id').html('<option value="">Select Patient</option>');
                        $.each(data, function (key, value) {
                    
                            
                             $("#registration_id").append('<option value="'+ value.RegistrationId+'">' + value.RegistrationId +'</option>');
                        });
                        $("#registration_id").addClass("selectpicker");
                        $("#registration_id").selectpicker('refresh');
                }
            });
        } else {
            $('#registration_id').empty();
            $('#registration_id').append($('<option>', {
                value: '',
                text: 'Select Patient'
            }));
        }

    });



 });
$('#refresh').click(function(){
    $('#daterange').val('');
  

    $('.selectpicker').selectpicker('val', '');
    $('#container_glucose').html('');
});

$('#search').click(function() {
    var daterange = $('#daterange').val();
   
   
    const parts = daterange.split(" - ");

// Get the first date.
    const fdate = parts[0];

// Get the second date.
    const ldate = parts[1];
   

    var registration_id = $('#registration_id').val();

    $.ajax({
        url: "{{ url('glucose-graph') }}",
        type: "get",
        data: { starting_date: fdate, ending_date: ldate, registration_id: registration_id },
        dataType: "html",
        beforeSend: function(){
            $('#warning-searching').removeClass('invisible');
        },
        complete: function(){
            $('#warning-searching').addClass('invisible');
        },
        success: function(data) {
    
            $('#container_glucose').html(data);
        },
        error: function(xhr, ajaxOption, thrownError) {
            console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
        }
    });
});


</script>
@endpush
