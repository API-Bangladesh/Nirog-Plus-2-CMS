<div class="col-md-12">
   <!-- slider start here -->
   <section id="prescription" class="print-section" >
      <div class="container px-4"  id="prescription-print">

        <header class="header">
          <p class="mb-0 pt-2 fs-"><b>Location :</b> {{$location->healthcenter->HealthCenterName}}</p>
          @foreach($prescriptionCreation as $pc)
          <h4 class="mb-0 p-3 text-center">Prescription {{ $pc->PrescriptionId }}</h4>
          @endforeach
        </header>
        <div class="topHeading border border-start-0 border-end-0 py-2">
          @foreach($patientDetails as $patientDetail)
          <span class="me-3"><b>Name :</b> {{ $patientDetail->GivenName }} {{ $patientDetail->FamilyName }}</span>
          <span class="me-3"><b>Reg. No. :</b> {{ $patientDetail->RegistrationId }}</span>
          <span class="me-3"><b>Age :</b> {{ $patientDetail->Age }}</span>
          <span class="me-3"><b>Gender :</b> {{ $patientDetail->Gender->GenderCode }}</span>
          @endforeach
          @foreach($prescriptionCreation as $pc)
          <span class="me-3"><b>Date :</b> {{ date('d-m-Y', strtotime($pc->CreateDate)) }}</span>
          @endforeach
          
        </div>

        <div class="d-flex"style="height:9in; width:100%;">
          <aside class="aside pe-2" style="font-size: 12px !important;">
            <div class="item pt-3">
              <b class="d-block mb-0 py-0 border-bottom">Complaints</b>
              @foreach($Complaints as $Complaint)
              <p class="mb-0 mt-0 pe-1">{{ date('d-m-Y', strtotime($Complaint->CreateDate)) }}: {{ $Complaint->ChiefComplain }}{!! !empty($Complaint->OtherCC) ? '[' . $Complaint->OtherCC . ']' : '' !!} for {{ $Complaint->CCDurationValue }} {{ $Complaint->DurationInEnglish }}</p>
              @endforeach
            </div>
            <div class="item pt-1">
              <b class="d-block mb-0 py-1 border-bottom">O / E</b>
              @foreach($HeightWeight as $hw)
              <p class="mb-0 mt-0 pe-1">Height: {{ $hw->Height }} cm</p>
              <p class="mb-0 mt-0 pe-1">Weight: {{ $hw->Weight }} kg</p>
              <p class="mb-0 mt-0 pe-1">BMI: {{ $hw->BMI }}</p>
              @endforeach
              @foreach($BP as $bps)
              <p class="mb-0 mt-0 pe-1">Pulse: {{$bps->HeartRate}}</p>
              <p class="mb-0 mt-0 pe-1">Blood Pressure: {{$bps->BPSystolic1}}/{{$bps->BPDiastolic1}} mmHg</p>
              @endforeach
              @foreach($GlucoseHb as $GHB)
              <p class="mb-0 mt-0 pe-1">RBG: {{$GHB->RBG}} mMol</p>
              <p class="mb-0 mt-0 pe-1">FBG: {{$GHB->FBG}} mMol</p>
              <p class="mb-0 mt-0 pe-1">Hemoglobin: {{$GHB->Hemoglobin}} g/dL</p>
              @endforeach
            </div>
            <div class="item pt-1">
              <b class="d-block mb-0 py-0 border-bottom">Provisional Dx</b>
              @foreach($ProvisionalDx as $key => $PDX)
              

              <p class="mb-0 mt-0  pe-1">Date:{{ date('d-m-Y', strtotime($PDX->CreateDate)) }}</p>
              <p class="mb-0 mt-0  pe-1">{{ ++$key }}.{{ $PDX->ProvisionalDiagnosis !="" ? $PDX->ProvisionalDiagnosis : $PDX->OtherProvisionalDiagnosis }} {!! !empty($PDX->OtherProvisionalDiagnosis) ? '[' . $PDX->OtherProvisionalDiagnosis . ']' : '' !!} [<?php if($PDX->DiagnosisStatus == "N" || $PDX->DiagnosisStatus == "n"){?>Presumptive<?php }elseif($PDX->DiagnosisStatus == "Y" || $PDX->DiagnosisStatus == "y"){?>Confirmed<?php }else{?>Unspecified<?php } ?>]</p>
              @endforeach
            </div>

             <div class="item pt-1">
              <b class="d-block mb-0 py-0 border-bottom">Patient Summary for TB</b>
              @foreach($TBSymptom as $key => $TBS)
              <p class="mb-0 mt-0  pe-1"> {{ $TBS->TBSymptomCode ? $TBS->TBSymptomCode . ': Positive' : '' }}</p>
          
              @endforeach

             @foreach($TBEFinding as $key => $TBEF)
              <p class="mb-0 mt-0  pe-1"> {{ $TBEF->TBEFindingCode ? $TBEF->TBEFindingCode . ': Positive' : '' }}</p>
          
              @endforeach

             @foreach($TBEPastEvidence as $key => $TBEP)
              <p class="mb-0 mt-0  pe-1"> {{ $TBEP->TBEPastEvidenceCode ? $TBEP->TBEPastEvidenceCode . ': Positive' : '' }}</p>
          
              @endforeach

              @foreach($TBPast as $key => $TBP)
              <p class="mb-0 mt-0  pe-1"> {{ $TBP->TBHistoryIdCode ? $TBP->TBHistoryIdCode . ':'.$TBP->Status : '' }}</p>
          
              @endforeach

            </div>
            
            <div class="item pt-1">
              <b class="d-block mb-0 py-0 border-bottom">Lab Investigations</b>
              @foreach($Investigation as $key => $IGS)
              <p class="mb-0 mt-0 pe-1">{{ ++$key }}. {{ $IGS->Investigation}} {!! !empty($IGS->PositiveNegativeStatus) ? '[' . $IGS->PositiveNegativeStatus . ']' : '' !!} {!! !empty($IGS->Instruction) ? '[' . $IGS->Instruction . ']' : '' !!}</p>
              @endforeach
            </div>
          </aside>
          <div class="rightSide position-relative w-100 py-3 px-4" style="font-size: 12px !important;">
            <h2 class="mb-2">℞</h2>
            <div class="medicine mb-1">
              @foreach($Treatment as $key => $TMS)
              <p class="mb-0"><b>{{ ++$key }} .</b> {{ $TMS->DrugCode }}({{ $TMS->DrugDose }}) </p>
              <i>
              {{ $TMS->Frequency }} -{{ $TMS->InstructionInBangla.','}}<?php $durationValue = $TMS->DrugDurationValue;if (stripos($durationValue, 'Day') !== false || stripos($durationValue, 'Day') !== false) {$durationValue = str_ireplace('Day', ' দিন', $durationValue);echo $durationValue;
                  } elseif (stripos($durationValue, 'Month') !== false || stripos($durationValue, 'Month') !== false) {$durationValue = str_ireplace('Month', ' মাস', $durationValue);echo $durationValue;
                  } elseif (stripos($durationValue, 'Year') !== false || stripos($durationValue, 'Year') !== false) {
                      $durationValue = str_ireplace('Year', ' বছর', $durationValue);
                      echo $durationValue;
                  } elseif (stripos($durationValue, 'Week') !== false || stripos($durationValue, 'Week') !== false) {
                      $durationValue = str_ireplace('Week', ' সপ্তাহ', $durationValue);
                      echo $durationValue;
                  }
              ?>
             </i>
              @endforeach
            </div>

            <div class="nextinfo">
              <div class="medicine mb-1">
                <p class="mb-0"><b>Follow-up / পরবর্তী সাক্ষাৎ</b></p>
                @foreach($FollowUpDate as $key =>$FD)
                @php 
                 $followDate=date('d-m-Y', strtotime($FD->FollowUpDate))
                @endphp
                <p class="mb-0">{{  $followDate == "01-01-1900" ? "" : $followDate.':'.$FD->Comment }}</p>
                @endforeach
              </div>
              <div class="medicine mb-1 mt-2">
                <p class="mb-0"><b>Advice / পরামর্শ</b></p>
                @foreach($Advice as $key =>$AS)
                <p class="mb-0"><b>{{ ++$key }} . </b>{{$AS->AdviceInBangla}}</p>
                @endforeach
               
              </div>
              <div class="medicine mb-1 mt-2">
                <p class="mb-0"><b>Referral / রেফারেল</b></p>
                @foreach($PatientReferral as $key =>$PRF)
                <p class="mb-0"><b>{{ ++$key }} . </b>{{ date('d-m-Y', strtotime($PRF->CreateDate)) }}:{{ $PRF->Description }}, {{ $PRF->HealthCenterName }}</p>
                @endforeach
              </div>
            </div>

          <div class="signatureBox text-center mt-4 my-4">
            @foreach($prescriptionCreation as $pc)
              @if($pc->EmployeeSignature != null)
              <img
                src="{{ $pc->EmployeeSignature }}"
                alt="img"
                class="signatureImage"
              />
              @endif
              <p class="mb-0">{{ $pc->FirstName }} {{ $pc->LastName }}</p>
              <i class="my-0">{{ $pc->Designation }}</i>
          @endforeach
              
            </div>
          </div>
        </div>

        <footer class="footer d-flex justify-content-between" >
          <address class="mb-0">
            <p class="mb-0">Health and Education for All (HAEFA), USA</p>
            <p class="mb-0">311 Bedford St Lexington, MA 02420, USA</p>
            <p class="mb-0">Email: haefa@haefa.org</p>
            <p class="mb-0">Website: www.haefa.org</p>
          </address>
          <address class="mb-0">
            <p class="mb-0">Health and Education for All (HAEFA), Bangladesh</p>
            <p class="mb-0">House: 31, Road: 16, Sector: 13, Uttara, Dhaka</p>
            <p class="mb-0">Email: haefa@haefa.org</p>
            <p class="mb-0">Website: www.haefabd.org</p>
          </address>
        </footer>
        <p class="mb-0 text-center pb-4 logoText">
          Powered By: API Solutions Ltd.
         
        </p>
      </div>
  </section>
    <!-- slider end here -->
</div>
