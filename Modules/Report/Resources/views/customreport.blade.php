@extends('layouts.app')

@section('title')
{{ $page_title }}
@endsection

@push('stylesheet')


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
                                <label for="name">Branches <span style="color: red">*</span></label>

                            <select class="selectpicker required" required="required" data-live-search="true" name="hc_id" id="hc_id">
                                <option value="">Select Branch </option> <!-- Empty option added -->
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
                      <table id="dataTable" class="table table-striped table-bordered table-hover d-none">
                            <thead class="bg-primary">
                            <tr>
                              
                                <th>Headers</th>
                                <th>data</th>
                            </tr>

                            </thead>
                            <tbody>
                                <tr>
                                  
                                    <td>Camp Name/CC Name</td>
                                    <td id="ccname"></td>
                                </tr>
                                 <tr>
                                  
                                    <td>Union</td>
                                    <td id="union"></td>
                                </tr>
                                <tr>
                                    
                                    <td>Upazilla</td>
                                    <td id="upazilla"></td>
                                </tr>
                                <tr>
                               
                                    <td>Contact Name</td>
                                    <td id="contactname"></td>
                                </tr>

                                 <tr>
                             
                                    <td>Contact Email</td>
                                    <td id="email"></td>
                                </tr>
                                 <tr>
                                   
                                    <td>Project Donor</td>
                                    <td id="donor"></td>
                                </tr>
                                <tr>
                                   
                                    <td>Implementing Partners</td>
                                    <td id="implementingpartners"></td>
                                </tr>
                                <tr>
                                   
                                    <td>Programming Partners</td>
                                    <td id="programmingpartners"></td>
                                </tr>
                            </tbody>
                    
                         
                    </table>

                        <table id="dataTable2" class="table table-striped table-bordered table-hover d-none">
                            <thead class="bg-primary">
                            <tr>
                              
                                <th>Diseases</th>
                                <th>total</th>
                            </tr>

                            </thead>
                            <tbody>
                                <tr>
                                  
                                    <td>Diabetes</td>
                                    <td id="diabetes"></td>
                                </tr>
                                 <tr>
                                  
                                    <td>Hypertension</td>
                                    <td id="hypertension"></td>
                                </tr>
                                <tr>
                                    
                                    <td>Pregnant</td>
                                    <td id="pregnant"></td>
                                </tr>
                               
                            </tbody>
                    
                         
                    </table>

                     <table id="dataTable3" class="table table-striped table-bordered table-hover d-none">
                            <thead class="bg-primary">
                            <tr>
                              
                                <th>Rohingya NCD Screening</th>
                                <th>Rohingya</th>
                            </tr>

                            </thead>
                            <tbody>
                                <tr>
                                  
                                    <td>Number of beneficiaries screened for hypertension (mention the number of persons whose blood pressure were measured) -  Male</td>
                                    <td id="bpmalerohingya"></td>
                                </tr>
                                 <tr>
                                  
                                    <td>Number of beneficiaries screened for hypertension (mention the number of persons whose blood pressure were measured) -  Female</td>
                                    <td id="bpfemalerohingya"></td>
                                </tr>
                                <tr>
                                    
                                    <td>Number of beneficiaries detected with high blood pressure (mention the number of persons whose blood pressure was found systolic blood pressure ≥140 mmHg and/or diastolic blood pressure on both days is ≥90 mmHg) -  Male</td>
                                    <td id="htnmalerohingya"></td>
                                </tr>
                                <tr>
                               
                                    <td>Number of beneficiaries detected with high blood pressure (mention the number of persons whose blood pressure was found systolic blood pressure ≥140 mmHg and/or diastolic blood pressure on both days is ≥90 mmHg) -  Female</td>
                                    <td id="htnfemalerohingya"></td>
                                </tr>

                                 <tr>
                             
                                    <td>Number of beneficiaries screened for diabetes mellitus (mention the number of persons whose blood glucose were measured) -  Male</td>
                                    <td id="glucosemalerohingya"></td>
                                </tr>
                                 <tr>
                                   
                                    <td>Number of beneficiaries screened for diabetes mellitus (mention the number of persons whose blood glucose were measured) - Rohingya Female</td>
                                    <td id="glucosefemalerohingya"></td>
                                </tr>
                                <tr>
                                   
                                    <td>Number of beneficiaries detected with high blood sugar (mention the number of persons whose Fasting Plasma Glucose was found ≥7 mmol/L or Random Plasma Glucose was found ≥11.1 mmol/L) -  Male</td>
                                    <td id="dmmalerohingya"></td>
                                </tr>
                                <tr>
                                   
                                    <td>Number of beneficiaries detected with high blood sugar (mention the number of persons whose Fasting Plasma Glucose was found ≥7 mmol/L or Random Plasma Glucose was found ≥11.1 mmol/L) -  Female</td>
                                    <td id="dmfemalerohingya"></td>
                                </tr>
                            </tbody>
                    
                         
                    </table>


                 <table id="dataTable4" class="table table-striped table-bordered table-hover d-none">
                            <thead class="bg-primary">
                            <tr>
                               
                                <th>Host NCD Screening</th>
                                <th>Host</th>
                            </tr>

                            </thead>
                            <tbody>
                                <tr>
                                  
                                    <td>Number of beneficiaries screened for hypertension (mention the number of persons whose blood pressure were measured) - Host Male</td>
                                    <td id="bpmalehost"></td>

                                </tr>
                                 <tr>
                                  
                                    <td>Number of beneficiaries screened for hypertension (mention the number of persons whose blood pressure were measured) - Host Female</td>
                                    <td id="bpfemalehost"></td>

                                </tr>
                                <tr>
                               
                                    <td>Number of beneficiaries detected with high blood pressure (mention the number of persons whose blood pressure was found systolic blood pressure ≥140 mmHg and/or diastolic blood pressure on both days is ≥90 mmHg) - Host Male</td>
                                    <td id="htnmalehost"></td>

                                </tr>
                                <tr>
                              
                                    <td>Number of beneficiaries detected with high blood pressure (mention the number of persons whose blood pressure was found systolic blood pressure ≥140 mmHg and/or diastolic blood pressure on both days is ≥90 mmHg) - Host Female</td>
                                    <td id="htnfemalehost"></td>

                                </tr>

                                 <tr>
                          
                                    <td>Number of beneficiaries screened for diabetes mellitus (mention the number of persons whose blood glucose were measured) -Host Male</td>
                                    <td id="glucosemalehost"></td>

                                </tr>
                                 <tr>
                           
                                    <td>Number of beneficiaries screened for diabetes mellitus (mention the number of persons whose blood glucose were measured) - Host Female</td>
                                    <td id="glucosefemalehost"></td>

                                </tr>
                                <tr>
                           
                                    <td>Number of beneficiaries detected with high blood sugar (mention the number of persons whose Fasting Plasma Glucose was found ≥7 mmol/L or Random Plasma Glucose was found ≥11.1 mmol/L) - Host Male</td>
                                    <td id="dmmalehost"></td>

                                </tr>
                                <tr>
            
                                    <td>Number of beneficiaries detected with high blood sugar (mention the number of persons whose Fasting Plasma Glucose was found ≥7 mmol/L or Random Plasma Glucose was found ≥11.1 mmol/L) - Host Female</td>
                                    <td id="dmfemalehost"></td>

                                </tr>
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
    var healthcenter='';
    var collectionDate='';
    var patients;
    var now = new Date();
    var formattedDate = now.getDate().toString().padStart(2, '0') + '_' + (now.getMonth() + 1).toString().padStart(2, '0') + '_' + now.getFullYear();
    var filename = 'Custom Report_' + formattedDate;

    document.getElementById('search').disabled = true; // Disable the button initially

    document.getElementById('hc_id').addEventListener('change', function () {
        // Enable the button when the select field is filled
        document.getElementById('search').disabled = !this.value;
    });

    document.getElementById('btn-reset').addEventListener('click', function () {
        // Reset the select field and disable the button
        document.getElementById('hc_id').value = '';
        document.getElementById('search').disabled = true;
    });

    
    $(document).ready(function () {
   
  document.getElementById('export-button').addEventListener('click', function () {
    // Get data from all four tables
    var table1Data = [];
    var table2Data = [];
    var table3Data = [];
    var table4Data = [];

    var table1Rows = document.querySelectorAll('#dataTable tbody tr');
    var table2Rows = document.querySelectorAll('#dataTable2 tbody tr');
    var table3Rows = document.querySelectorAll('#dataTable3 tbody tr');
    var table4Rows = document.querySelectorAll('#dataTable4 tbody tr');

    // Process and collect data for the first table
    table1Data.push(['Nirog Plus', '']);
    table1Data.push(['Report']);
    table1Data.push(['Date:'+ formattedDate]);
     table1Data.push(['']);
    table1Rows.forEach(function (row) {
        var rowData = [];
        row.querySelectorAll('td').forEach(function (cell) {
            rowData.push(cell.innerText);
        });
        table1Data.push(rowData);
    });

    // Process and collect data for the second table
    table2Data.push(['']);
    table2Data.push(['']);
    table2Data.push(['', '']);
    table2Rows.forEach(function (row) {
        var rowData = [];
        row.querySelectorAll('td').forEach(function (cell) {
            rowData.push(cell.innerText);
        });
        table2Data.push(rowData);
    });

    // Process and collect data for the third table
    table3Data.push(['']);
    table3Data.push(['']);
    table3Data.push(['Rohingya NCD Screening', '']);
    table3Rows.forEach(function (row) {
        var rowData = [];
        row.querySelectorAll('td').forEach(function (cell) {
            rowData.push(cell.innerText);
        });
        table3Data.push(rowData);
    });

    // Process and collect data for the fourth table
    table4Data.push(['']);
    table4Data.push(['']);
    table4Data.push(['Host NCD Screening', '']);
    table4Rows.forEach(function (row) {
        var rowData = [];
        row.querySelectorAll('td').forEach(function (cell) {
            rowData.push(cell.innerText);
        });
        table4Data.push(rowData);
    });

    // Combine data from all tables
    var combinedData = table1Data.concat(table2Data, table3Data, table4Data);

    // Create a new workbook
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
    XLSX.writeFile(wb, 'Custom_Report_' + formattedDate + '.xlsx');
});


     $('#search').click(function () {
        var daterange = $('#daterange').val();
        var hc_id = $('#hc_id').val();
        const parts = daterange.split(" - ");
        const fdate = parts[0];
        const ldate = parts[1];

        $.ajax({
            type: "GET",
            url: "{{ url('custom-report') }}",
            data: { hc_id: hc_id, fdate: fdate, ldate: ldate },
            beforeSend: function () {
                $('#warning-searching').removeClass('invisible');
            },
            complete: function () {
                $('#warning-searching').addClass('invisible');
                $('#export-button').removeClass('disabled');
            },
            success: function (response) {
               let bpmalehost=response.total_bp_male_host ? response.total_bp_male_host : '0';
               let bpmalerohingya=response.total_bp_male_rohingya ? response.total_bp_male_rohingya : '0';
               let bpfemalehost=response.total_bp_female_host ? response.total_bp_female_host : '0';
               let bpfemalerohingya=response.total_bp_female_rohingya ? response.total_bp_female_rohingya : '0';

               let htnmalehost=response.total_htn_male_host ? response.total_htn_male_host : '0';
               let htnmalerohingya=response.total_htn_male_rohingya ? response.total_htn_male_rohingya : '0';
               let htnfemalehost=response.total_htn_female_host ? response.total_htn_female_host : '0';
               let htnfemalerohingya=response.total_htn_female_rohingya ? response.total_htn_female_rohingya : '0';

               let glucosemalehost=response.total_glucose_male_host ? response.total_glucose_male_host : '0';
               let glucosemalerohingya=response.total_glucose_male_rohingya ? response.total_glucose_male_rohingya : '0';
               let glucosefemalehost=response.total_glucose_female_host ? response.total_glucose_female_host : '0';
               let glucosefemalerohingya=response.total_glucose_female_rohingya ? response.total_glucose_female_rohingya : '0';

               let dmmalehost=response.total_dm_male_host ? response.total_dm_male_host : '0';
               let dmmalerohingya=response.total_dm_male_rohingya ? response.total_dm_male_rohingya : '0';
               let dmfemalehost=response.total_dm_female_host ? response.total_dm_female_host : '0';
               let dmfemalerohingya=response.total_dm_female_rohingya ? response.total_dm_female_rohingya : '0';


               let pregnant=response.pregnant ? response.pregnant : '0';
               let total_htn=response.total_htn ? response.total_htn : '0';
               let total_diabetes=response.total_diabetes ? response.total_diabetes : '0';
               let cc_name=response.cc_name ? response.cc_name : 'N/A';
               let union=response.union ? response.union : 'N/A';
               let upazila=response.upazila ? response.upazila : 'N/A';
               $('#diabetes').text(total_diabetes);
               $('#hypertension').text(total_htn);
               $('#pregnant').text(pregnant);
               $('#ccname').text(cc_name);
               $('#union').text(union);
               $('#upazilla').text(upazila);             
               $('#bpmalehost').text(bpmalehost);
               $('#bpmalerohingya').text(bpmalerohingya);
               $('#bpfemalehost').text(bpfemalehost);
               $('#bpfemalerohingya').text(bpfemalerohingya);

               $('#htnmalehost').text(htnmalehost);
               $('#htnmalerohingya').text(htnmalerohingya);
               $('#htnfemalehost').text(htnfemalehost);
               $('#htnfemalerohingya').text(htnfemalerohingya);

               $('#glucosemalehost').text(glucosemalehost);
               $('#glucosemalerohingya').text(glucosemalerohingya);
               $('#glucosefemalehost').text(glucosefemalehost);
               $('#glucosefemalerohingya').text(glucosefemalerohingya);


               $('#dmmalehost').text(dmmalehost);
               $('#dmmalerohingya').text(dmmalerohingya);
               $('#dmfemalehost').text(dmfemalehost);
               $('#dmfemalerohingya').text(dmfemalerohingya);
              
                var firstDate = new Date(response.first_date);
                var lastDate = new Date(response.last_date);

                    var formatDate = function (date) {
                    var day = date.getDate().toString().padStart(2, '0');
                    var monthNames = [
                        "January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"
                    ];
                    var monthName = monthNames[date.getMonth()];
                    var year = date.getFullYear();
                    return day + "-" + monthName + "-" + year;
                };

                collectionDate = formatDate(firstDate) + "_To_" + formatDate(lastDate);
               
            },
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
