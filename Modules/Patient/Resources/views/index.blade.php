@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

@push('stylesheet')
    <style>
#prescription .container {
  background-color: #f2f2f2 !important;
}

.header p {
  font-size: 14px;
}
.aside {
  width: 400px;
  border-right: 1px solid #ddd;
  min-height: 600px;
  padding-bottom: 20px;
}

.signatureImage {
  display: inline-block;
  width: 100px;
  object-fit: contain;
  margin-bottom: 5px;
}
.signatureBox {
  position: absolute;
  right: 50px;
  bottom: 30px;
}
.footer {
  padding-top: 20px;
  padding-bottom: 20px;
  border-top: 1px solid #ddd;
}

.footer p {
  font-size: 14px;
}
.apiLogo {
  max-width: 40px;
  transform: translateY(-4px);
  margin-left: 5px;
}
.logoText {
  font-size: 14px;
}
.nextinfo {
  margin-top: 150px;
}

.userImg {
  margin-top: 20px;
  width: 200px;
  height: 200px;
  object-fit: cover;
  border-radius: 20px;
  border: 10px solid rgba(122,122,122,.15);
}

.dataItem p{
  font-weight: 400;
  font-size: 15px;
}
.dataItem span{
  font-weight: 600;
  font-size: 15px;
}
.buttons-excel{
    display: none !important;
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
               
                

            </div>
            <!-- /entry header -->

            <!-- Card -->
            <div class="dt-card">
               
              

                <!-- Card Body -->
                <div class="dt-card__body">
                <form action="{{ route('patient.searchid') }}" method="POST">
                <div class="row" >
                    @csrf
                      {{-- <div class="form-group col-md-4">
                                <label for="name">Search Patient</label>
                                <input type="text" class="form-control" name="patient_id" id="patient_id" placeholder="Enter Patient ID">
                            </div> --}}

                            <div class="form-group col-md-4">
                                    <label for="name">Search Patient</label>
                                    @php
                                        $loginPrefix = session('login_prefix');
                                        $prefixValue = $loginPrefix && $loginPrefix !== 'admin' ? $loginPrefix : '';
                                    @endphp
                                    <input type="text" class="form-control" name="patient_id" id="patient_id" placeholder="Enter Patient ID" value="{{ $prefixValue }}">
                            </div>

                            <div class="form-group col-md-8 pt-24">
                                <button type="button" class="btn btn-danger btn-sm float-right" id="btn-reset"
                               data-toggle="tooltip" data-placement="top" data-original-title="Reset Data">
                                   <i class="fas fa-redo-alt"></i>
                                </button>
                               <button type="submit" class="btn btn-primary btn-sm float-right mr-2" id="patient-search"
                               data-toggle="tooltip" data-placement="top" data-original-title="Filter Data">
                                   <i class="fas fa-search"></i>
                                </button>
                      </div>
                   </div>
                </form>

                   

                    
                    
                        <table id="dataTable" class="table table-striped table-bordered table-hover">
                            <thead class="bg-primary">
                            <tr>

                                <th>Sl</th>
                                <th>Registration Id</th>
                                <th>Patient Name</th>
                                <th>Patient Age</th>
                                <th>NID Number</th>
                                <th>Mobile Number</th>
                                <th>Gender</th>
                                <th>BirthDate</th>
                                <th>Marital Status</th>
                                <th>Action</th>
                               

                            </tr>

                            </thead>
                            @if($patients ?? '')
                                <tbody>
                                @php
                                    $currentPage = $patients->currentPage();
                                    $start = ($currentPage - 1) * $patients->perPage() + 1;
                                @endphp
                                @foreach($patients as $result)
                                    <tr>

                                        <td>{{ $start++ }}</td>
                                        <td>{{$result->RegistrationId}}</td>
                                        <td>{{$result->GivenName}} {{$result->FamilyName}}</td>
                                        <td>{{$result->Age}}</td>
                                        <td>{{optional($result)->IdNumber}}</td>
                                        <td>{{optional($result)->CellNumber}}</td>
                                        <td>{{optional($result->Gender)->GenderCode}}</td>
                                        <td>{{optional($result)->BirthDate}}</td>
                                        <td>{{optional($result->MaritalStatus)->MaritalStatusCode}}</td>
                                     <td> 
                                        <?php if(permission('patient-viewid')){?><a  class="viewid_data"  style="cursor: pointer;" data-id="{{$result->PatientId}}"><i class="fas fa-eye text-success"></i></a> <a class="" href="{{route('patient.edit', $result->PatientId)}}"><i class="fas fa-edit text-success"></i></a>
                                    <?php } ?>
                                    </td>
                                       

                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                        </table>
                    <div class="d-flex justify-content-between">
                            <div>
                                @if ($patients->total() > 0)
                                    @php
                                        $start = ($patients->currentPage() - 1) * $patients->perPage() + 1;
                                        $end = $start + $patients->count() - 1;
                                    @endphp
                                    Showing {{ $start }} to {{ $end }} of {{ $patients->total() }} entries
                                @else
                                    No entries found
                                @endif
                            </div>
                            <div>
                                {{ $patients->links('pagination::bootstrap-4') }}
                            </div>
                    </div>

                    

                </div>
                <!-- /card body -->

            </div>
            <!-- /card -->

        </div>
        <!-- /grid item -->

    </div>
    <!-- /grid -->

</div>
@include('patient::viewid-modal')
@endsection

@push('script')
<script src="js/dataTables.buttons.min.js"></script>
 <script src="js/buttons.html5.min.js"></script>
<script>
var table;
$(document).ready(function(){

$('#dataTable').DataTable({
    paging: false,
    dom: 't',
    ordering:false,
    bFilter:false,
    columnDefs: [
        { targets: [5,6,7], visible: false }, // Hide the columns
    ],
  
    buttons: [
        {
            extend: 'excel',
            text: 'Export to Excel',
            filename: 'ALL Patients List',
            exportOptions: {
                columns: [1,2,3,4,5,6,7], // Include specific columns in the export
            },
            title: '',
    customize: function(xlsx) {
            var sheet = xlsx.xl.worksheets['sheet1.xml'];
            var downrows = 5; // Number of rows to add
            var clRow = $('row', sheet);

            // Update Row
            clRow.each(function() {
                var attr = $(this).attr('r');
                var ind = parseInt(attr);
                ind = ind + downrows;
                $(this).attr("r", ind);
            });

            // Update row > c
            $('row c', sheet).each(function() {
                var attr = $(this).attr('r');
                var pre = attr.substring(0, 1);
                var ind = parseInt(attr.substring(1, attr.length));
                ind = ind + downrows;
                $(this).attr("r", pre + ind);
            });
         

            function Addrow(index, data) {
                var msg = '<row r="' + index + '">';
                for (var i = 0; i < data.length; i++) {
                    var key = data[i].k;
                    var value = data[i].v;
                    msg += '<c t="inlineStr" r="' + key + index + '">';
                    msg += '<is>';
                    msg += '<t>' + value + '</t>';
                    msg += '</is>';
                    msg += '</c>';
                }
                msg += '</row>';
                return msg;
            }

            var r1 = Addrow(1, [{
                k: 'A',
                v: 'Total Patients:'
            }, {
                k: 'B',
                v: 580
            }]);

            var r2 = Addrow(2, [{
                k: 'A',
                v: 'Male Patients:'
            }, {
                k: 'B',
                v: 6 // Replace '6' with actual count
            }]);

            var r3 = Addrow(3, [{
                k: 'A',
                v: 'Female Patients:'
            }, {
                k: 'B',
                v: 4 // Replace '4' with actual count
            }]);

            var r4 = Addrow(4, [{
                k: 'A',
                v: 'Today Date:'
            }, {
                k: 'B',
                v: new Date().toLocaleDateString()
            }]);
             var r5 = Addrow(4, [{
                k: 'A',
                v: ''
            }, {
                k: 'B',
                v: ''
            }]);

            sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + sheet.childNodes[0].childNodes[1].innerHTML;
    },

        },
    ],

});


    $('#btn-filter').click(function () {
        table.ajax.reload();
    });

    $('#btn-reset').click(function () {
        
        window.location.href = "{{ route('patient') }}";
    });

    $(document).on('click', '#save-btn', function () {
        let form = document.getElementById('store_or_update_form');
        let formData = new FormData(form);
        let url = "{{route('patient.store.or.update')}}";
        let id = $('#update_id').val();
        let method;
        if (id) {
            method = 'update';
        } else {
            method = 'add';
        }
        store_or_update_data(table, method, url, formData);
    });


    // document.addEventListener('DOMContentLoaded', function() {
    // var resetButton = document.getElementById('search-reset');

    // resetButton.addEventListener('click', function() {
    //     
    // });




    

    $(document).on('click', '.viewid_data', function () {
        let id = $(this).data('id');
        if (id) {
            $.ajax({
                url: "{{route('patient.showid')}}",
                type: "POST",
                data: { id: id,_token: _token},
                success: function (data) {

                    $('#viewid_modal .detailsp').html();
                    $('#viewid_modal .detailsp').html(data);

                    $('#viewid_modal').modal({
                        keyboard: false,
                        backdrop: 'static',
                    });
                    $('#viewid_modal .modal-title').html(
                        '<i class="fas fa-eye"></i> <span>Patient Details</span>');
                },
                error: function (xhr, ajaxOption, thrownError) {
                    console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                }
            });
        }
    });

});
</script>
@endpush