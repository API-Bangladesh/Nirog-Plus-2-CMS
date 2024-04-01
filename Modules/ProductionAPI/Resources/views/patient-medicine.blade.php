@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

@push('stylesheet')
<link rel="stylesheet" type="text/css"href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

@endpush


@section('content')
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <!--begin::Notice-->
        <div class="card card-custom gutter-b">
            <div class="card-header flex-wrap py-5">
                <div class="card-title">
                    <h3 class="card-label"><i class="{{ $page_icon }} text-primary"></i> {{ $sub_title }}</h3>
                </div>
                <div class="card-toolbar">
                    <!--begin::Button-->

                    <!--end::Button-->
                </div>
            </div>
        </div>
        <!--end::Notice-->
        <!--begin::Card-->
        <div class="card card-custom" style="padding-bottom: 100px !important;">
            <div class="card-body">
                <form id="store_or_update_form" method="post" enctype="multipart/form-data">
                @csrf
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <h3>Patient Medicine Payload</h3><br>

                        <div class="col-md-12">
                            <div class="row">
                                <x-form.selectbox labelName="Select Facility" name="identifier" id="identifier" required="required" col="col-md-6" class="form-group selectpicker">
                                    @if (!$facilities->isEmpty())
                                        @foreach ($facilities as $facility)
                                        <option value="{{ $facility->identifier }}">{{ $facility->facility_name }} ({{ $facility->identifier }})</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>
                            <div class="col-md-2 warning-searching invisible" id="warning-searching">
                                <span class="text-danger" id="warning-message">Searching...Please Wait</span>
                                <span class="spinner-border text-danger"></span>
                            </div>

                                <div class="form-group col-4">

                                </div>


                                <x-form.textbox type="number" labelName="Total Unsent Patients" readonly  name="total_unsent" id="total_unsent" col="col-md-2" value="" />
                                 <x-form.textbox type="number" labelName="Sending Patients" name="sending_patient" id="sending_patient" col="col-md-2" value="" />
                                <div class="form-group col-md-2 ml-3 pt-5 mt-1">
                                    <button type="button" class="btn btn-primary btn-sm" id="get_count">Get Count</button>
                                </div>
                                <x-form.textbox type="number" labelName="Medicine Data(Max 99) " id="sending_now" name="sending_now" col="col-md-2" value="" />
                                <div class="col-md-2 warning-exceed invisible" id="warning-searching">
                                    <span class="text-danger" id="warning-message">Max limit Exceeded,Please Insert Fewer Patient</span>
                                   
                                </div>


                            </div>
                        </div>




                    </div>
                </div>
                <!-- /modal body -->

                <!-- Modal Footer -->
            <div class="row">
                <div class="form-group col-md-2 ml-3 ">
                    <button type="button" class="btn btn-primary btn-sm" id="send">Send</button>
                </div>
                 <div class="col-md-8 warning-sending invisible" id="warning-sending">
                                <span class="text-danger" id="sending-message">Submitting...Please Do Not Close The Tab</span>
                                <span class="spinner-border text-danger"></span>
                </div>
            </div>

                <!-- /modal footer -->
                </form>
            </div>
        </div>
        <!--end::Card-->
    </div>
</div>
@endsection

@push('script')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>


<script>
$(document).ready(function () {

toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "showMethod": "slideDown",
        "hideMethod": "fadeOut",
        "timeOut": 5000 // 5 seconds
    };




    $('.remove-files').on('click', function(){
        $(this).parents(".col-md-12").remove();
    });


    $('#send').click(function () {

        var identifier = $('#identifier').val();
        const send_patient = $('#sending_patient').val();
        $.ajax({
            type: "GET",
            url: "{{ url('send-patient-medicine') }}",
            data: { identifier: identifier, send_patient: send_patient},
            beforeSend: function () {
                 $('#warning-sending').removeClass('invisible');
            },
            complete: function () {
                $('#warning-sending').addClass('invisible');
            },
            success: function (response) {
   
                 if (response.error.length > 0) {
                    // Display Toastr alert for each error
                    response.error.forEach(error => {
                        toastr.error('Patient Medication Send Failed', 'Error');
                    });
                } else {
                       const successCount = response.success.length;
                    // Display a success Toastr alert with the count
                    toastr.success(`${successCount} Medication Sent successfully`, 'Success');
                    // Display a success Toastr alert

                }
                setTimeout(function() {
                    window.location.reload();
                }, 4000);



            },
        });
    });
    $('#sending_now').prop('disabled', true);
    $('#send').prop('disabled', true);

       $('#get_count').click(function () {

        var identifier = $('#identifier').val();
        const sending_patient = $('#sending_patient').val();
        $.ajax({
            type: "GET",
            url: "{{ url('count-patient-medicine') }}",
            data: { identifier: identifier, sending_patient: sending_patient},
            beforeSend: function () {
                 $('#warning-sending').removeClass('invisible');
            },
            complete: function () {
                $('#warning-sending').addClass('invisible');
            },
            success: function (response) {
            if (response.totalCount > 99) {
                // If total count exceeds 99, disable the send button
                $('#send').prop('disabled', true);
                $('#warning-exceed').removeClass('invisible');
            } else {
                // If total count is 99 or less, enable the send button
                $('#send').prop('disabled', false);
                $('#warning-exceed').addClass('invisible');
            }
            // Set the total count in the sending_now field
            $('#sending_now').val(response.totalCount);
        },
        });
    });

    $('#identifier').change(function () {

        var identifier = $('#identifier').val();
        console.log(identifier);
         $.ajax({
            type: "GET",
            url: "{{ url('get-patient-medicine') }}",
            data: { identifier: identifier},
            beforeSend: function () {
                $('#warning-searching').removeClass('invisible');
            },
            complete: function () {
                 $('#warning-searching').addClass('invisible');
            },
            success: function (response) {
                $('#sending_now').val('');
                var unsents = response.unsent ?? '0';
                $('#total_unsent').val(unsents);

                // maxSendingNow = Math.min(unsents, 99);
                //  $('#sending_now').prop('disabled', false).attr('max', maxSendingNow);
                //  // Enable and set max attribute of sending_now input

                //   if (unsents === 0) {
                //      maxSendingNow = 0;
                //  }
                // // Handle input event on sending_now input
                // $('#sending_now').on('input', function () {
                //     var sendingNowValue = parseInt($(this).val());
                //     if (sendingNowValue > maxSendingNow) {
                //         $(this).val(maxSendingNow); // Set input value to maxSendingNow if it exceeds the maximum
                //     }
                //     $('#send').prop('disabled', $(this).val() === "");
                // });
            }

        });

    });
});


</script>
@endpush
