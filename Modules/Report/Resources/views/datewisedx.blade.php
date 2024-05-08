@extends('layouts.app')

@section('title')
{{ $page_title }}
@endsection

@push('stylesheet')
<style>
.table th {
    color: rgb(10, 0, 0)202 !important; 
}
</style>
@endpush

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

                    <form id="form-filter" method="POST" action="" >
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="daterange">Date Range</label>
                                <input type="text" class="form-control daterangepicker-start" id="daterange" name="daterange" >
                            </div>

                            <div class="form-group col-md-3">
                                <label for="name">Branches</label>

                        
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
                                    <button type="button" class="btn btn-danger btn-sm float-right" id="btn-reset"
                                    data-toggle="tooltip" data-placement="top" data-original-title="Reset Data">
                                    <i class="fas fa-redo-alt"></i>
                                    </button>

                                <button type="button"  class="btn btn-primary btn-sm float-right mr-2" id="search"
                                        data-toggle="tooltip" data-placement="top" data-original-title="Filter Data">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

            <table id="normal-table" class="table table-striped table-bordered table-hover d-none">
                <thead class="bg-primary">
                   
                </thead>
                <tbody>
                    <!-- Data will be dynamically added here -->
                </tbody>
            </table>
             <button id="export-button" class="btn btn-primary disabled">Export to Excel</button>

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





<script src="js/dataTables.buttons.min.js"></script>
<script src="js/buttons.html5.min.js"></script>
<script lang="javascript" src="js/xlsx.full.min.js"></script>
<script>

// disease rate by date range start




// disease rate by date range end

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
            // 'This Quarter': [moment().startOf('quarter'), moment().endOf('quarter')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            // Add more custom ranges here...
        }
    });


    $('.daterangepicker').mouseleave(function() {
        $(this).hide();
    });
    $('.daterangepicker-start').click(function() {
        $('.daterangepicker').show();
    });


    var table;
    var healthcenter=[];
    var collectionDate='';
    var patients;
    var now = new Date();
    var formattedDate = now.getDate().toString().padStart(2, '0') + '_' +
    (now.getMonth() + 1).toString().padStart(2, '0') + '_' +
    now.getFullYear() + '_' +
    now.getHours().toString().padStart(2, '0') + '_' +
    now.getMinutes().toString().padStart(2, '0') + '_' +
    now.getSeconds().toString().padStart(2, '0');
    var filename = 'ProvisionalDiagnosis_Datewise_' + formattedDate;

  

    
    $(document).ready(function () {
        
  

     $('#search').click(function () {
        var daterange = $('#daterange').val();
        var hc_id = $('#hc_id').val();
        const parts = daterange.split(" - ");
        const fdate = parts[0];
        const ldate = parts[1];
        var fdateFormatted = moment(fdate, 'MM/DD/YYYY').format('DD-MMM-YYYY');
        var ldateFormatted = moment(ldate, 'MM/DD/YYYY').format('DD-MMM-YYYY');

    

        $.ajax({
            type: "GET",
            url: "{{ url('date-wise-dx') }}",
            data: { hc_ids: hc_id, fdate: fdate, ldate: ldate },
            beforeSend: function () {
                $('#warning-searching').removeClass('invisible');
            },
            complete: function () {
                $('#warning-searching').addClass('invisible');
                $('#export-button').removeClass('disabled');
            },
           success: function (response) {
            var results = response.results;
            healthcenter = response.healthcenter;
            patients = response.resultCount;
            var table = $('#normal-table tbody');

            // Clear the existing table rows
            table.empty();

            // Create header row
            var headerRow = $('<tr></tr>');
            headerRow.append('<th style="color:black; font-weight:bold;">Provisional DX</th>');

            // Extract unique dates from all diagnoses and sort them
            var uniqueDates = [...new Set(Object.values(results).flatMap(diagnosis => Object.keys(diagnosis)))];

            // Sort the uniqueDates based on both year and month
            uniqueDates.sort(function (a, b) {
                var dateA = moment(a, 'DD-MM-YYYY');
                var dateB = moment(b, 'DD-MM-YYYY');
                return dateA.isBefore(dateB) ? -1 : dateA.isAfter(dateB) ? 1 : 0;
            });

            uniqueDates.forEach(function (date) {
                var formattedDate = moment(date, 'DD-MM-YYYY').format('DD-MMM-YYYY');
                // Append the formatted date to the headerRow
                headerRow.append('<th style="color:black; width:20px;">' + formattedDate + '</th>');
            });

            headerRow.append('<th style="color:black;">Total</th>'); // Add Total column header
            table.append(headerRow);

            // Create data rows
            Object.keys(results).forEach(function (diagnosis) {
                var dataRow = $('<tr></tr>');
                dataRow.append('<td>' + diagnosis + '</td>');

                var total = 0;

                uniqueDates.forEach(function (date) {
                    var count = parseInt(results[diagnosis][date]) || 0;
                    total += count;
                    dataRow.append('<td>' + count + '</td>');
                });

                dataRow.append('<td>' + total + '</td>'); // Add Total column
                table.append(dataRow);
            });
},


        });

        document.getElementById('export-button').addEventListener('click', function () {
        // Get data from all four tables
            var table1Data = [];

    // Add the header row as the first row
            table1Data.push(['App Name: Nirog Plus', '']);
            table1Data.push(['Branch:'+ healthcenter, '']);
            table1Data.push(['Collection Date:'+ fdateFormatted +'_To_'+ ldateFormatted, '']);
            table1Data.push(['Report Type: ProvisionalDiagnosis_DateWise', '']);
            table1Data.push(['']);

            var table1headers = document.querySelectorAll('#normal-table th');
            var headerData = [];
            table1headers.forEach(function (cell) {
                headerData.push(cell.innerText);

            });
            
            table1Data.push(headerData);
            // Process and collect data for the data rows
            var table1Rows = document.querySelectorAll('#normal-table tbody tr');
            table1Rows.forEach(function (row) {
                var rowData = [];
                row.querySelectorAll('td').forEach(function (cell) {
                    rowData.push(cell.innerText);
                });

                // Check if rowData is not empty before pushing
                if (rowData.length > 0) {
                    table1Data.push(rowData);
                }
            });

                var combinedData = table1Data;

                var wb = XLSX.utils.book_new();

                // Add data to a worksheet
                var ws = XLSX.utils.aoa_to_sheet(combinedData);
                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                var wscols = [
                { wch: 60 }, // Set the width of the first column to 20
                ];

            // Update the worksheet with column widths
                ws['!cols'] = wscols;

                // Export the workbook to Excel
                XLSX.writeFile(wb, 'ProvisionalDiagnosis_Datewise_' + formattedDate + '.xlsx');
        });
    });

    });
      $('#btn-reset').click(function () {
       
        table.clear().draw();
        $('#hc_id').val('').selectpicker('refresh');
        
    });

    $('#btn-filter').on('click', function (event) {
        $('#warning-searching').removeClass('invisible');
    });

    

</script>
@endpush
