@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

@push('stylesheet')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

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
                    <a href="{{ route('patient') }}" class="btn btn-warning btn-sm font-weight-bolder">
                        <i class="fas fa-arrow-left"></i> Back</a>
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
                        <h3>Registration</h3><br>
                        <input type="hidden" name="address_update_id" id="address_update_id" value="{{ $address->AddressId ?? ''}}"/>
                        <input type="hidden" name="update_id" id="update_id" value="{{ $patient->PatientId }}"/>
                        <input type="hidden" name="WorkPlaceId" id="WorkPlaceId" value="{{ $patient->WorkPlaceId }}"/>
                        <input type="hidden" name="WorkPlaceBranchId" id="WorkPlaceBranchId" value="{{ $patient->WorkPlaceBranchId }}"/>
                        <input type="hidden" name="PatientCode" id="PatientCode" value="{{ $patient->PatientCode }}"/>
                        <input type="hidden" name="RegistrationId" id="RegistrationId" value="{{ $patient->RegistrationId }}"/>
                        <div class="col-md-12">
                            <div class="row">
                                <x-form.textbox type="text" labelName="First Name" name="GivenName" col="col-md-6" value="{{ $patient->GivenName??'' }}" placeholder="Enter First Name"/>
                                <x-form.textbox type="text" labelName="Last Name" name="FamilyName" col="col-md-6" value="{{ $patient->FamilyName ??'' }}" placeholder="Enter Last Name" />
                                <x-form.textbox type="date" labelName="Date Of Birth" name="BirthDate" required="required" col="col-md-6" value="{{ $patient->BirthDate ??'' }}" placeholder="Enter Date Of Birth" />
                                <x-form.textbox type="number" labelName="Patient Age" name="Age" col="col-md-6" value="{{ $patient->Age ??'' }}" placeholder="Enter name" />
                                <x-form.textbox type="text" labelName="Contact Number" name="CellNumber" col="col-md-6" value="{{ $patient->CellNumber ??'' }}" placeholder="Enter name" />
                                <x-form.selectbox labelName="Gender" name="GenderId" required="required" col="col-md-6" class="selectpicker">
                                    @if (!$genders->isEmpty())
                                        @foreach ($genders as $gender)
                                        <option value="{{ $gender->GenderId }}"  {{ $patient->GenderId == $gender->GenderId ? 'selected' : '' }}>{{ $gender->GenderCode }}</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>
                                <div class="form-group col-md-8">
                                    <label for="">ID Type</label><br>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="NID" name="IdType" value="NID" class="custom-control-input"
                                                {{ $patient->IdType == 'NID' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="NID">NID</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="BIRTH" name="IdType" value="BIRTH" class="custom-control-input"
                                            {{ $patient->IdType == 'Birth' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="BIRTH">BIRTH</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="ID" name="IdType" value="ID" class="custom-control-input"
                                            {{ $patient->IdType == 'IDNO' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="ID">ID</label>
                                        </div>
                                </div>
                                <x-form.textbox type="text" labelName="Contact Number" name="IdNumber" col="col-md-6" value="{{ $patient->IdNumber ??'' }}" placeholder="Id Number" />
                                <x-form.selectbox labelName="ID Owner" name="IdOwner" required="required" col="col-md-6" class="selectpicker">
                                    @if (!$selfTypes->isEmpty())
                                    @if($patient->IdOwner != '')

                                        @foreach ($selfTypes as $selfType)
                                        <option value="{{ $selfType->HeadOfFamilyId }}"  {{ $patient->IdOwner == $selfType->HeadOfFamilyId ? 'selected' : '' }}>{{ $selfType->HeadOfFamilyCode ??'' }}</option>
                                        @endforeach
                                    @endif
                                    @else
                                         @foreach ($selfTypes as $selfType)
                                        <option value="{{ $selfType->HeadOfFamilyId }}">{{ $selfType->HeadOfFamilyCode ??'' }}</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>
                                <x-form.selectbox labelName="Marital Status" name="MaritalStatusId" required="required" col="col-md-6" class="selectpicker">
                                    @if (!$maritals->isEmpty())
                                        @foreach ($maritals as $marital)
                                        <option value="{{ $marital->MaritalStatusId }}"  {{ $patient->MaritalStatusId == $marital->MaritalStatusId ? 'selected' : '' }}>{{ $marital->MaritalStatusCode ??'' }}</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>
                                <div class="form-group col-md-2 pt-5">
                                    <button type="button" class="btn btn-primary btn-sm" id="camera-btn" >Take Photo</button>
                                    <button type="button" class="btn btn-primary btn-sm d-none" id="capture-btn" >Capture Photo</button>
                                    <input type="hidden" id="profile_photo" name="profile_photo" class="image-tag">
                                </div>
                                <div class="col-md-2">
                                    <div id="my_camera"></div>
                                </div>
                                <div class="col-md-2 mt-4 pt-1">
                                    <div id="captured-image"></div>
                                </div>

                                <x-form.textbox type="text" labelName="Spouse Name" name="SpouseName" col="col-md-6" value="{{ $patient->SpouseName ??'' }}" placeholder="Enter Spouse Name"/>

                                <x-form.selectbox labelName="Religion" name="ReligionId" required="required" col="col-md-6" class="selectpicker">
                                    @if (!$religions->isEmpty())
                                        @foreach ($religions as $religion)
                                            <option value="{{ $religion->ReligionId }}"  {{ $patient->ReligionId == $religion->ReligionId ? 'selected' : '' }}>{{ $religion->ReligionCode ??'' }}</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>

                                <x-form.textbox type="number" labelName="Family Members" name="FamilyMembers" col="col-md-6" value="{{ $patient->FamilyMembers ??'' }}" placeholder="Enter Family Member"/>
                                <x-form.textbox type="text" labelName="Father Name" name="FatherName" col="col-md-6" value="{{ $patient->FatherName ??'' }}" placeholder="Enter Father Name"/>
                                <x-form.textbox type="text" labelName="Mother Name" name="MotherName" col="col-md-6" value="{{ $patient->MotherName ??'' }}" placeholder="Enter Mother Name"/>

                                <x-form.selectbox labelName="Education" name="EducationId" required="required" col="col-md-6" class="selectpicker">
                                    @if (!$educations->isEmpty())
                                        @foreach ($educations as $education)
                                            <option value="{{ $education->EducationId }}"  {{ $education->EducationId == $patient->EducationId ? 'selected' : '' }}>{{ $education->EducationCode ??'' }}</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>

                                <x-form.selectbox labelName="Head of Family" name="HeadOfFamilyId" required="required" col="col-md-6" class="selectpicker">
                                       @if($patient->HeadOfFamilyId != '')
                                        @foreach ($RefHeadOfFamilies as $RefHeadOfFamily)
                                            <option value="{{ $RefHeadOfFamily->HeadOfFamilyId }}"  {{ $RefHeadOfFamily->HeadOfFamilyId == $patient->HeadOfFamilyId ? 'selected' : '' }}>{{ $RefHeadOfFamily->HeadOfFamilyCode ??'' }}</option>
                                        @endforeach
                                        @else
                                         @foreach ($RefHeadOfFamilies as $RefHeadOfFamily)
                                            <option value="{{ $RefHeadOfFamily->HeadOfFamilyId }}">{{ $RefHeadOfFamily->HeadOfFamilyCode ??'' }}</option>
                                         @endforeach

                                       @endif

                                </x-form.selectbox>

                                <div class="form-group col-md-6">
                                    <label for="ChildAge0To1">Child Age 0To1</label>
                                    <input type="number" name="ChildAge0To1" id="ChildAge0To1" class="form-control " value="{{ $patient->ChildAge0To1 ??'' }}" placeholder="Enter Child Age 0To1">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="ChildAge1To5">Child Age 1To5</label>
                                    <input type="number" name="ChildAge1To5" id="ChildAge1To5" class="form-control " value="{{ $patient->ChildAge1To5 ??'' }}" placeholder="Enter Child Age 1To5">
                                </div>

                                <div class="form-group col-md-6" >
                                    <label for="ChildAgeOver5">Child Age Over5</label>
                                    <input type="number" name="ChildAgeOver5" id="ChildAgeOver5" class="form-control " value="{{ $patient->ChildAgeOver5 ??'' }}" placeholder="Enter Child Age Over5">
                                </div>

{{--                                <div class="form-group col-md-6">--}}
{{--                                    <label for="">Children</label><br>--}}
{{--                                    <div class="custom-control custom-control-inline">--}}
{{--                                        <label class="custom-control-label" for="ChildAge0To1">ChildAge0To1</label>--}}
{{--                                        <input type="number" id="ChildAge0To1" name="ChildAge0To1" value="{{ $patient->ChildAge0To1?? '' }}" class="custom-control-input">--}}

{{--                                    </div>--}}
{{--                                    <div class="custom-control custom-control-inline">--}}
{{--                                        <label class="custom-control-label" for="ChildAge1To5">ChildAge1To5</label>--}}
{{--                                        <input type="number" id="ChildAge1To5" name="ChildAge1To5" value=" {{ $patient->ChildAge1To5 ?? '' }}" class="custom-control-input">--}}
{{--                                    </div>--}}
{{--                                    <div class="custom-control custom-control-inline">--}}
{{--                                        <label class="custom-control-label" for="ChildAgeOver5">ChildAgeOver5</label>--}}
{{--                                        <input type="number" id="ChildAgeOver5" name="ChildAgeOver5" value="{{ $patient->ChildAgeOver5 ?? '' }}" class="custom-control-input">--}}
{{--                                    </div>--}}
{{--                                </div>--}}

                            </div>
                        </div>
                        <h3>Present Address</h3><br>
                        <div class="col-md-12">
                            <div class="row">
                                <x-form.textbox type="text" labelName="Address" name="AddressLine1" col="col-md-6" value="{{ $address->AddressLine1 ??'' }}" placeholder="Enter Address" readonly/>
                                <x-form.textbox type="text" labelName="Village" name="Village" col="col-md-6" value="{{ $address->Village ??'' }}" placeholder="Enter Village" />
                             
                                
                                 <x-form.selectbox labelName="Union" name="Thana" required="required" col="col-md-6" class="selectpicker">
                              
                                    @if( isset($address) && $address->UnionId != '')
                                        @foreach ($upazilas as $upazila)
                                        <option value="{{ $upazila->id }}"  {{ $address->UnionId == $upazila->id ? 'selected' : '' }}>{{ $upazila->name ??'' }}</option>
                                        @endforeach
                             
                                    @else
                                         @foreach ($upazilas as $upazila)
                                        <option value="{{ $upazila->id }}">{{ $upazila->name ??'' }}</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>

                                <x-form.textbox type="text" labelName="Post Code" name="PostCode" col="col-md-6" value="{{ $address->PostCode ??'' }}" placeholder="Enter Post Code" />

                                <x-form.selectbox labelName="District" name="District" required="required" col="col-md-6" class="selectpicker">
                                  
                                    @if( isset($address) && $address->District != '')
                                        @foreach ($districts as $district)
                                        <option value="{{ $district->id }}"  {{ $address->District == $district->id ? 'selected' : '' }}>{{ $district->name ??'' }}</option>
                                        @endforeach
                           
                                    @else
                                         @foreach ($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name ??'' }}</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>
                                <x-form.textbox type="text" labelName="Country" name="Country" col="col-md-6" value="{{ $address->Country ??'' }}" placeholder="Enter Country" />


                            </div>
                        </div>
                        <h3>Permanent Address</h3><br>
                        <div class="col-md-12">
                            <div class="row">
                                <x-form.textbox type="text" labelName="Address" name="AddressLine1Parmanent" col="col-md-6" value="{{ $address->AddressLine1Parmanent ??'' }}" placeholder="Enter Address" readonly/>
                                <x-form.textbox type="text" labelName="Village" name="VillageParmanent" col="col-md-6" value="{{ $address->VillageParmanent ??'' }}" placeholder="Enter Village" />
                             

                                <x-form.selectbox labelName="Union" name="ThanaParmanent" required="required" col="col-md-6" class="selectpicker">
                          
                                    @if(isset($address) && $address->UnionIdParmanent != '')
                                        @foreach ($upazilas as $upazila)
                                        <option value="{{ $upazila->id }}"  {{ $address->UnionIdParmanent == $upazila->id ? 'selected' : '' }}>{{ $upazila->name ??'' }}</option>
                                        @endforeach
                              
                                    @else
                                         @foreach ($upazilas as $upazila)
                                        <option value="{{ $upazila->id }}">{{ $upazila->name ??'' }}</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>

                                <x-form.textbox type="text" labelName="Post Code" name="PostCodeParmanent" col="col-md-6" value="{{ $address->PostCodeParmanent ??'' }}" placeholder="Enter Post Code" />

                                <x-form.selectbox labelName="District" name="DistrictParmanent" required="required" col="col-md-6" class="selectpicker">
                                
                                    @if(isset($address) &&  $address->DistrictParmanent != '')
                                        @foreach ($districts as $district)
                                        <option value="{{ $district->id }}"  {{ $address->DistrictParmanent == $district->id ? 'selected' : '' }}>{{ $district->name ??'' }}</option>
                                        @endforeach
                           
                                    @else
                                         @foreach ($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name ??'' }}</option>
                                        @endforeach
                                    @endif
                                </x-form.selectbox>
                                <x-form.textbox type="text" labelName="Country" name="CountryParmanent" col="col-md-6" value="{{ $address->CountryParmanent ??'' }}" placeholder="Enter Country" />


                            </div>

                        </div>
                        <h3>FDMN Camp</h3><br>
                        <div class="col-md-12">
                            <div class="row">
                                <x-form.textbox type="text" labelName="Camp" name="Camp" col="col-md-6" value="{{ $address->Camp ??'' }}" placeholder="Enter Address" readonly/>
                                <x-form.textbox type="text" labelName="Block Number" name="BlockNumber" col="col-md-6" value="{{ $address->BlockNumber ??'' }}" placeholder="Enter Village" />
                                <x-form.textbox type="text" labelName="Majhi" name="Majhi" col="col-md-6" value="{{ $address->Majhi ??'' }}" placeholder="Enter Union" />
                                <x-form.textbox type="text" labelName="Tent Number" name="TentNumber" col="col-md-6" value="{{ $address->TentNumber ??'' }}" placeholder="Enter Post Code" />
                                <x-form.textbox type="text" labelName="FCN Number" name="FCN" col="col-md-6" value="{{ $address->FCN ??'' }}" placeholder="Enter Country" />


                            </div>

                        </div>


                    </div>
                </div>
                <!-- /modal body -->

                <!-- Modal Footer -->
                <div class="form-group col-md-12 pt-5">
                    <button type="button" class="btn btn-primary btn-sm" id="update-btn">Update</button>
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
<script src="js/spartan-multi-image-picker-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script>
$(document).ready(function () {
    $(document).on('click','#camera-btn',function(){
        Webcam.attach( '#my_camera' );
        $('#capture-btn').removeClass('d-none');
        $('#camera-btn').addClass('d-none');
    })
    $(document).on('click','#capture-btn',function(){
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('captured-image').innerHTML = '<img style="height: 112px; width: 150px" src="'+data_uri+'"/>';
        } );
    })


    $('.summernote').summernote({
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]

      });
    /** Start :: patient Image **/
    $('#image').spartanMultiImagePicker({
        fieldName: 'image',
        maxCount: 1,
        rowHeight: '200px',
        groupClassName: 'col-md-12 com-sm-12 com-xs-12',
        maxFileSize: '',
        dropFileLabel: 'Drop Here',
        allowExt: 'png|jpg|jpeg',
        onExtensionErr: function(index, file){
            Swal.fire({icon:'error',title:'Oops...',text: 'Only png,jpg,jpeg file format allowed!'});
        }
    });

    $('input[name="image"]').prop('required',true);

    $('.remove-files').on('click', function(){
        $(this).parents(".col-md-12").remove();
    });



    @if(!empty($patient->image))
    $('#image img').css('display','none');
    $('#image .spartan_remove_row').css('display','block');
    $('#image .img_').css('display','block');
    $('#image .img_').attr('src',"{{ asset('storage/'.patient_IMAGE_PATH.$patient->image)}}");
    @else
    $('#image img').css('display','block');
    $('#image .spartan_remove_row').css('display','none');
    $('#image .img_').css('display','none');
    $('#image .img_').attr('src','');
    @endif
    /** End :: patient Image **/


    $('#lifestyle_image').spartanMultiImagePicker({
        fieldName: 'lifestyle_image',
        maxCount: 1,
        rowHeight: '200px',
        groupClassName: 'col-md-12 com-sm-12 com-xs-12',
        maxFileSize: '',
        dropFileLabel: 'Drop Here',
        allowExt: 'png|jpg|jpeg',
        onExtensionErr: function(index, file){
            Swal.fire({icon:'error',title:'Oops...',text: 'Only png,jpg,jpeg file format allowed!'});
        }
    });

    $('input[name="lifestyle_image"]').prop('required',true);

    $('.remove-files').on('click', function(){
        $(this).parents(".col-md-12").remove();
    });

    @if(!empty($patient->lifestyle_image))
    $('#lifestyle_image img').css('display','none');
    $('#lifestyle_image .spartan_remove_row').css('display','block');
    $('#lifestyle_image .img_').css('display','block');
    $('#lifestyle_image .img_').attr('src',"{{ asset('storage/'.patient_IMAGE_PATH.$patient->lifestyle_image)}}");
    @else
    $('#lifestyle_image img').css('display','block');
    $('#lifestyle_image .spartan_remove_row').css('display','none');
    $('#lifestyle_image .img_').css('display','none');
    $('#lifestyle_image .img_').attr('src','');
    @endif
$('input[name="patient_video_path"]').prop(true);
$('input[name="patient_brochure"]').prop(true);

    $('.remove-files').on('click', function(){
        $(this).parents(".col-md-12").remove();
    });




    /****************************/
    $(document).on('click','#update-btn',function(){

        let form = document.getElementById('store_or_update_form');
        let formData = new FormData(form);

        $.ajax({
            url: "{{route('patient.store.or.update1')}}",
            type: "POST",
            data: formData,
            dataType: "JSON",
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function(){
                $('#update-btn').addClass('spinner spinner-white spinner-right');
            },
            complete: function(){
                $('#update-btn').removeClass('spinner spinner-white spinner-right');
            },
            success: function (data) {
                $('#store_or_update_form').find('.is-invalid').removeClass('is-invalid');
                $('#store_or_update_form').find('.error').remove();
                if (data.status == false) {
                    $.each(data.errors, function (key, value){
                        var key = key.split('.').join('_');
                        $('#store_or_update_form input#' + key).addClass('is-invalid');
                        $('#store_or_update_form textarea#' + key).addClass('is-invalid');
                        $('#store_or_update_form select#' + key).parent().addClass('is-invalid');
                        if(key == 'code'){
                            $('#store_or_update_form #' + key).parents('.form-group').append(
                            '<small class="error text-danger">' + value + '</small>');
                        }else{
                            $('#store_or_update_form #' + key).parent().append(
                            '<small class="error text-danger">' + value + '</small>');
                        }
                    });
                } else {
                    notification(data.status, data.message);
                    if (data.status == 'success') {
                            window.location.replace("{{ route('patient') }}");
                    }
                }
            },
            error: function (xhr, ajaxOption, thrownError) {
                console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
            }
        });
    });




});
Webcam.set({
    width: 150,
    height: 150,
    image_format: 'jpeg',
    jpeg_quality: 90
});

</script>
@endpush
