<div class="col-md-12">
   <!-- slider start here -->
   <section id="prescription">
      <div class="container px-4">
        <header class="header">
          <h4 class="mb-0 px-3 py-4 text-center border-bottom">Patient Details</h4>
        </header>

        <div class="d-flex">
          <aside class="aside">
            <div class="d-flex align-items-center flex-column">
              <img src="{{ $patientDetails->PatientImage }}" alt="img" class="userImg">
              <div class="text-left">
                <h5 class="mt-3 text-primary text-left">Name : {{ $patientDetails->GivenName }} {{ $patientDetails->FamilyName }}</h5>
                <p class="mb-0 text-left">Reg. No: <span>{{ $patientDetails->RegistrationId }}</span></p>
                <p class="mb-0 text-left">Reg. Date: <span>{{ date('d-m-Y', strtotime($patientDetails->CreateDate)) }}</span></p>
                <p class="mb-0 text-left">Last Update: <span>{{ date('d-m-Y', strtotime($patientDetails->UpdateDate)) }}</span></p>
              </div>
            </div>
          </aside>

          <div class="rightSide w-100 py-3 px-5">
            <div class="dataItem mt-3">
              <h5 class="mb-3 py-1 ps-2 border-start border-success text-success border-4 d-inline-block">Patient Info</h5>
                <p><span>Registration Number :</span> {{ $patientDetails->RegistrationId }}</p>
                <p><span>Date Of Birth :</span> {{ date('d-m-Y', strtotime($patientDetails->BirthDate)) }}</p>
                <p><span>Patient Age :</span> {{ $patientDetails->Age }}</p>
                <p><span>Contact Number :</span> {{ $patientDetails->CellNumber }}</p>
                <p><span>Gender :</span> {{ $patientDetails->Gender->GenderCode }}</p>
                <p><span>NID :</span> {{ $patientDetails->IdNumber }} (<span>@if (!(empty($patientDetails->self_type->HeadOfFamilyCode))){{$patientDetails->self_type->HeadOfFamilyCode}} @endif </span>)</p>
                <p><span>Marital Status :</span> {{ $patientDetails->MaritalStatus->MaritalStatusCode }}</p>
            </div>
            <div class="dataItem mt-5">
              <h5 class="mb-3 py-1 ps-2 border-start border-success text-success border-4 d-inline-block">Present Address</h5>
                <p><span>Address :</span> @if (!(empty($patientDetails->address))){{$patientDetails->address->AddressLine1}} @endif</p>
                <p><span>Village :</span> @if (!(empty($patientDetails->address))){{$patientDetails->address->Village}} @endif</p>
                <p><span>Union :</span> @if (!(empty($patientDetails->address))){{$patientDetails->address->unionAddress->name}} @endif</p>
                <p><span>Post Code :</span> @if (!(empty($patientDetails->address))){{$patientDetails->address->PostCode}} @endif</p>
                <p><span>District :</span> @if (!(empty($patientDetails->address))){{$patientDetails->address->districtAddress->name}} @endif</p>
                <p><span>Country :</span> @if (!(empty($patientDetails->address))){{$patientDetails->address->Country}} @endif</p>
            </div>
            <div class="dataItem mt-5">
              <h5 class="mb-3 py-1 ps-2 border-start border-success text-success border-4 d-inline-block">Permanent Address</h5>
                <p><span>Address :</span>  @if (!(empty($patientDetails->address))){{$patientDetails->address->AddressLine1Parmanent}} @endif</p>
                <p><span>Village :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->VillageParmanent}} @endif</p>
                <p><span>Union :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->unionAddressPermanent->name}} @endif </p>
                <p><span>Post Code :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->PostCodeParmanent}} @endif </p>
                <p><span>District :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->districtAddressPermanent->name}}  @endif </p>
                <p><span>Country :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->CountryParmanent}} @endif </p>
            </div>
            <div class="dataItem mt-5">
              <h5 class="mb-3 py-1 ps-2 border-start border-success text-success border-4 d-inline-block">FDMN Camp</h5>
                <p><span>Camp :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->Camp}} @endif </p>
                <p><span>Block Number :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->BlockNumber}} @endif</p>
                <p><span>Majhi / Captain :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->Majhi}} @endif </p>
                <p><span>Tent Number :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->TentNumber}} @endif </p>
                <p><span>FCN Number :</span>@if (!(empty($patientDetails->address))){{$patientDetails->address->FCN}} @endif </p>
            </div>
        
          </div>
        </div>

        
    </section>
    <!-- slider end here -->
</div>