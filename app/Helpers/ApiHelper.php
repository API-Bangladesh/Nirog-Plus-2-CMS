<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Modules\ProductionAPI\Entities\ApiPatientTrigView;
use Modules\ProductionAPI\Entities\ApiPatientList;
use Modules\ProductionAPI\Entities\ApiBpTrigView;
use Modules\ProductionAPI\Entities\ApiBsTrigView;
use Modules\ProductionAPI\Entities\ApiConditionTrigView;
use Modules\ProductionAPI\Entities\ApiMedicineTrigView;
use Modules\ProductionAPI\Entities\ApiPatientVisitedTrigView;

class ApiHelper
{
    public static function authenticate()
    {
        $client = new Client();

        

        try {
            $response = $client->post('https://api.bd.simple.org/oauth/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'haefa',
                    'client_secret' => '9a1a793ee32849de',
                    'username' => 'emailtorubel@gmail.com',
                    'password' => 'APISolutions@2024',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Handle the response data as needed
            return $data;
        } catch (\Exception $e) {
            // Handle any exceptions
            return ['error' => $e->getMessage()];
        }
    }

 public static function registerPatient($accessToken, $patientData)
    {
        $client = new Client();




        try {
            $response = $client->put('https://api.bd.simple.org/api/v4/import', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*',
                    'X-Organization-ID' => 'e998b782-a38d-42e2-ba74-aeb895d6d0e9',
                    'Client-Secret' => '9a1a793ee32849de',
                ],
                'json' => $patientData,
            ]);

            // dd($response);



        $statusCode = $response->getStatusCode();

        // Decode the response body
        $responseData = json_decode($response->getBody()->getContents(), true);

        // If the status code is 202, return success along with the response data
        if ($statusCode == 202) {
            return ['status' => $statusCode, 'data' => $responseData];
        } else {
            // Handle other status codes if needed
            return ['status' => $statusCode, 'data' => $responseData];
        }



            // Handle the response data as needed

        } catch (\Exception $e) {
            // Handle any exceptions
               return ['status' => 500, 'error' => $e->getMessage()];
        }
    }
     public static function SendRegistrationPayload($accessToken, $identifier, $sending_patient)
    {
        // Retrieve patients based on the provided criteria
         $patients = ApiPatientTrigView::where('Facility_identifier', '=', $identifier)
                                  ->take($sending_patient)
                                  ->get();

    // Prepare arrays to store success and error responses
    $successResponses = [];
    $errorResponses = [];

    // Loop through the patients and format the data
    foreach ($patients as $patient) {
        $patientData = [
            "resourceType" => "Patient",
            "meta" => [
                "lastUpdated" => $patient->UpdateDate ? \Carbon\Carbon::parse($patient->UpdateDate)->toIso8601String() : null,
                "createdAt" => $patient->CreateDate ? \Carbon\Carbon::parse($patient->CreateDate)->toIso8601String() : null
            ],
            "identifier" => [
                [
                    "value" => $patient->PatientId // Assuming you have a unique_id field in your Patient model
                ]
            ],
            "gender" => strtolower($patient->GenderCode),
            "birthDate" => $patient->BirthDate ? \Carbon\Carbon::parse($patient->BirthDate)->toIso8601String() : null,
            "managingOrganization" => [
                [
                    "value" => $patient->Facility_identifier // Assuming you have an organization_name field in your Patient model
                ]
            ],
            "registrationOrganization" => [
                [
                    "value" => $patient->Facility_identifier
                ]
            ],
            "deceasedBoolean" => false,
            "telecom" => [],
            "address" => [
                [
                    "line" => null,
                    "district" => $patient->Address,
                    "city" => $patient->Address,
                    "postalCode" => $patient->PostCode ?? null
                ]
            ],
            "name" => [
                "text" => $patient->Name
            ],
            "active" => true
        ];

        // Register the patient
        $registrationResponse = ApiHelper::registerPatient($accessToken, ['resources' => [$patientData]]);

        if (isset($registrationResponse['error'])) {
            // Handle registration error
            ApiPatientList::where('PatientId', $patient->PatientId)->update(['RegistrationStatus' => 'error']);
            $errorResponses[] = ['error' => $registrationResponse['error']];
        } elseif ($registrationResponse['status'] == 202) {
            // Update registration status for successful registration
            ApiPatientList::where('PatientId', $patient->PatientId)->update(['RegistrationStatus' => 'sent']);
            $successResponses[] = ['message' => 'Patient registered successfully'];
        }
    }
     return [
            'successResponses' => $successResponses,
            'errorResponses' => $errorResponses
        ];
 }


  public static function SendBPPayload($accessToken, $identifier, $sending_patient)
    {
       
        // Retrieve 100 patients from the database
          $patients = ApiBpTrigView::where('identifier', '=', $identifier)
                                  ->take($sending_patient)
                                        ->get();

            $successResponses = [];
            $errorResponses = [];

                // Loop through the patients and format the data
            foreach ($patients as $patientbp) {
                $patientData = [
                "resourceType" => "Observation",
                "meta" => [
                        "lastUpdated" => $patientbp->UpdateDate ? \Carbon\Carbon::parse($patientbp->UpdateDate)->toIso8601String() : null,
                        "createdAt" => $patientbp->CreateDate ? \Carbon\Carbon::parse($patientbp->CreateDate)->toIso8601String() : null
                    ],
                "identifier" => [
                    [
                        "value" =>$patientbp->MdataBPId 
                    ]
                ],
                "subject" => [
                    "identifier" => $patientbp->PatientId 
                ],
                "performer" => [
                    [
                        "identifier" =>  $patientbp->identifier
                    ]
                ],
                "effectiveDateTime" => $patientbp->CreateDate ? \Carbon\Carbon::parse($patientbp->CreateDate)->toIso8601String() : null,
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://loinc.org",
                            "code" => "85354-9"
                        ]
                    ]
                ],
                "component" => [
                    [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8480-6"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => intval($patientbp->BPSystolic1),
                            "unit" => "mmHg",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mm[Hg]"
                        ]
                    ],
                    [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "8462-4"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" =>intval($patientbp->BPDiastolic1),
                            "unit" => "mmHg",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mm[Hg]"
                        ]
                    ]
                ]
            ];
                $bpResponse = ApiHelper::registerPatient($accessToken, ['resources' => [$patientData]]);

                if (isset($bpResponse['error'])) {
                    // Handle registration error
                    ApiPatientList::where('PatientId', $patientbp->PatientId)->where('DiseaseName','Hypertension')->update(['BPStatus' => 'error']);
                    $errorResponses[] = ['error' => $bpResponse['error']];
                } elseif ($bpResponse['status'] == 202) {
                    // Update registration status for successful registration
                    ApiPatientList::where('PatientId', $patientbp->PatientId)->where('DiseaseName','Hypertension')->update(['BPStatus' => 'sent']);
                    $successResponses[] = ['message' => 'Patient BP sent successfully'];
                }
                
            }
                return [
                    'successResponses' => $successResponses,
                    'errorResponses' => $errorResponses
                ];
    }
      
 public static function SendBSPayload($accessToken, $identifier, $sending_patient)
    {
       
        // Retrieve 100 patients from the database
               // Retrieve 100 patients from the database
          $patients = ApiBsTrigView::where('identifier', '=', $identifier)
                                  ->take($sending_patient)
                                  ->get();



        $successResponses = [];
        $errorResponses = [];


        // Prepare patient data array

        // Loop through the patients and format the data
        foreach ($patients as $patientBS) {
            if($patientBS->RBG != '' && $patientBS->FBG != '') {
                    $patientData = [
                "resourceType" => "Observation",
                "meta" => [
                    "lastUpdated" => $patientBS->UpdateDate ? \Carbon\Carbon::parse($patientBS->UpdateDate)->toIso8601String() : null,
                    "createdAt" => $patientBS->CreateDate ? \Carbon\Carbon::parse($patientBS->CreateDate)->toIso8601String() : null
                ],
                "identifier" => [
                    [
                        "value" => $patientBS->MdataGlucoseId
                    ]
                ],
                "subject" => [
                    "identifier" => $patientBS->PatientId
                ],
                "performer" => [
                    [
                        "identifier" => $patientBS->identifier
                    ]
                ],
                "effectiveDateTime" => $patientBS->CreateDate ? \Carbon\Carbon::parse($patientBS->CreateDate)->toIso8601String() : null,
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://loinc.org",
                            "code" => "2339-0"
                        ]
                    ]
                ],
                "component" => [
                    [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "2339-0"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => intval($patientBS->RBG ),
                            "unit" => "mg/dL",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mg/dL"
                        ]
                    ]
                ]
            ];
            $registrationResponse = ApiHelper::registerPatient($accessToken, ['resources' => [$patientData]]);
             $patientData = [
                "resourceType" => "Observation",
                "meta" => [
                    "lastUpdated" => $patientBS->UpdateDate ? \Carbon\Carbon::parse($patientBS->UpdateDate)->toIso8601String() : null,
                    "createdAt" => $patientBS->CreateDate ? \Carbon\Carbon::parse($patientBS->CreateDate)->toIso8601String() : null
                ],
                "identifier" => [
                    [
                        "value" => $patientBS->MdataGlucoseId
                    ]
                ],
                "subject" => [
                    "identifier" => $patientBS->PatientId
                ],
                "performer" => [
                    [
                        "identifier" => $patientBS->identifier
                    ]
                ],
                "effectiveDateTime" => $patientBS->CreateDate ? \Carbon\Carbon::parse($patientBS->CreateDate)->toIso8601String() : null,
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://loinc.org",
                            "code" => "2339-0"
                        ]
                    ]
                ],
                "component" => [
                    [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => "88365-2"
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => intval($patientBS->FBG ),
                            "unit" => "mg/dL",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mg/dL"
                        ]
                    ]
                ]
            ];
             $registrationResponse = ApiHelper::registerPatient($accessToken, ['resources' => [$patientData]]);
            }else{
                   $patientData = [
                "resourceType" => "Observation",
                "meta" => [
                    "lastUpdated" => $patientBS->UpdateDate ? \Carbon\Carbon::parse($patientBS->UpdateDate)->toIso8601String() : null,
                    "createdAt" => $patientBS->CreateDate ? \Carbon\Carbon::parse($patientBS->CreateDate)->toIso8601String() : null
                ],
                "identifier" => [
                    [
                        "value" => $patientBS->MdataGlucoseId
                    ]
                ],
                "subject" => [
                    "identifier" => $patientBS->PatientId
                ],
                "performer" => [
                    [
                        "identifier" => $patientBS->identifier
                    ]
                ],
                "effectiveDateTime" => $patientBS->CreateDate ? \Carbon\Carbon::parse($patientBS->CreateDate)->toIso8601String() : null,
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://loinc.org",
                            "code" => "2339-0"
                        ]
                    ]
                ],
                "component" => [
                    [
                        "code" => [
                            "coding" => [
                                [
                                    "system" => "http://loinc.org",
                                    "code" => $patientBS->FBG != '' ? "88365-2" : "2339-0",	
                                ]
                            ]
                        ],
                        "valueQuantity" => [
                            "value" => intval($patientBS->FBG != '' ? $patientBS->FBG : $patientBS->RBG ),
                            "unit" => "mg/dL",
                            "system" => "http://unitsofmeasure.org",
                            "code" => "mg/dL"
                        ]
                    ]
                ]
            ];
             $registrationResponse = ApiHelper::registerPatient($accessToken, ['resources' => [$patientData]]);

            }
         
        // Register the patient

       

        if (isset($registrationResponse['error'])) {
            // Handle registration error
            ApiPatientList::where('PatientId', $patientBS->PatientId)->where('DiseaseName','=','Diabetes Mellitus')->update(['GlucoseStatus' => 'error']);
            $errorResponses[] = ['error' => $registrationResponse['error']];
        } elseif ($registrationResponse['status'] == 202) {
            // Update registration status for successful registration
            ApiPatientList::where('PatientId', $patientBS->PatientId)->where('DiseaseName','=','Diabetes Mellitus')->update(['GlucoseStatus' => 'sent']);
            $successResponses[] = ['message' => 'Patient Blood Sugar successfully'];
        }

        }
                return [
                    'successResponses' => $successResponses,
                    'errorResponses' => $errorResponses
                ];
    }

public static function SendConditionPayload($accessToken, $identifier, $sending_patient)
{
       
             $patients = ApiConditionTrigView::where('identifier', '=', $identifier)
                                  ->take($sending_patient)
                                  ->get();
            $successResponses = [];
            $errorResponses = [];

        // Loop through the patients and format the data
            foreach ($patients as $patientcondition) {
            $patientData = [
            "resourceType" => "Condition",
            "meta" => [
                "lastUpdated" => $patientcondition->UpdateDate ? \Carbon\Carbon::parse($patientcondition->UpdateDate)->toIso8601String() : null,
                "createdAt" => $patientcondition->CreateDate ? \Carbon\Carbon::parse($patientcondition->CreateDate)->toIso8601String() : null
            ],
            "identifier" => [
                [
                    "value" => $patientcondition->MDataPatientId // Assuming you have a unique identifier for the condition
                ]
            ],
            "subject" => [
                "identifier" => $patientcondition->PatientId // Assuming you have a PatientId field in your Patient model
            ],
            "code" => [
                "coding" => []
            ]
        ];

        $conditionToCode = [
            "Diabetes Mellitus" => "73211009",
            "Hypertension" => "38341003"
        ];
        // Assuming you have an array of condition codes
        $processedConditions = [];

        // Explode the string of conditions into an array
        $conditionsArray = explode(", ", $patientcondition->Condition);

        // Loop through the patient's conditions and add them to the payload
        foreach ($conditionsArray as $condition) {
            // Check if the condition exists in the mapping and has not been processed already
            if (isset($conditionToCode[$condition]) && !in_array($condition, $processedConditions)) {
                // Add the condition to the payload
                $patientData["code"]["coding"][] = [
                    "system" => "http://snomed.info/sct",
                    "code" => $conditionToCode[$condition]
                ];
                // Add the condition to the processed conditions array
                $processedConditions[] = $condition;
            }
        }
                $conditionResponse = ApiHelper::registerPatient($accessToken, ['resources' => [$patientData]]);

                if (isset($bpResponse['error'])) {
                    // Handle registration error
                    ApiPatientList::where('PatientId', $patientcondition->PatientId)->update(['ConditionStatus' => 'error']);
                    $errorResponses[] = ['error' => $conditionResponse['error']];
                } elseif ($conditionResponse['status'] == 202) {
                    // Update registration status for successful registration
                    ApiPatientList::where('PatientId', $patientcondition->PatientId)->update(['ConditionStatus' => 'sent']);
                    $successResponses[] = ['message' => 'Patient Condition successfully'];
                }
                    
        }
                return [
                    'successResponses' => $successResponses,
                    'errorResponses' => $errorResponses
                ];
    }


public static function SendMedicinePayload($accessToken, $identifier, $sending_patient)
{
       
        // $patients = ApiMedicineTrigView::where('identifier', '=', $identifier)
        //                           ->take($sending_patient)
        //                           ->get();
         $distinctPatients  = ApiMedicineTrigView::where('identifier', '=', $identifier)
                                        ->distinct('PatientId')
                                        ->take($sending_patient)
                                        ->pluck('PatientId');

        $successResponses = [];
        $errorResponses = [];
      

        foreach ($distinctPatients as $patientId) {
        // Get medicines for the current patient
        $patientMedicines = ApiMedicineTrigView::where('identifier', '=', $identifier)
            ->where('PatientId', $patientId)
            ->get();

        // Prepare medication data for the current patient
        $patientData = [];
        foreach ($patientMedicines  as $patientMed) {
       $timeUnit = $patientMed->DrugDurationValue;

     

// Define a mapping of time units to their equivalent days
        $timeUnitToDays = [
            'day' => 1,
            'days' => 1,
            'week' => 7,
            'weeks' => 7,
            'month' => 30,
            'months' => 30,
            'year' => 365,
            'years' => 365,
            'continue' => 90,
        ];

        // Extract the numeric value and units from the time unit string
        preg_match('/(\d+)?\s*(\w+)/', $timeUnit, $matches);


        

        // Numeric value
        $numericValue = $matches[1] !== "" ? intval($matches[1]) : 1;


        // Units
        $units = strtolower($matches[2]);

        
        //  dd($numericValue,$units);

        // Calculate the equivalent days
        $days = $numericValue * $timeUnitToDays[$units];

        

       


        preg_match('/(\d+(?:\.\d+)?)\s*(\w+)/', $patientMed->DrugDose, $dosematches);

        // Numeric value
        $numericValueDose = ctype_digit($dosematches[1]) ? (int) $dosematches[1] : (float) $dosematches[1];

        // Units
        $unitsDose = strtolower($dosematches[2]);
        $patientData = [
            "contained" => [
                [
                    "resourceType" => "Medication",
                    "id" => $patientMed->DrugTableUUID,
                    "status" => "active",
                    "code" => [
                        "coding" => [
                            [
                                "system" => "http://www.nlm.nih.gov/research/umls/rxnorm",
                                "code" => $patientMed->DrugId,
                                "display" => $patientMed->DrugName.' '.$patientMed->DrugDose
                            ]
                        ]
                    ]
                ]
            ],
            "resourceType" => "MedicationRequest",
            "meta" => [
                "lastUpdated" => $patientMed->UpdateDate ? \Carbon\Carbon::parse($patientMed->UpdateDate)->toIso8601String() : null,
                "createdAt" => $patientMed->CreateDate ? \Carbon\Carbon::parse($patientMed->CreateDate)->toIso8601String() : null
            ],
            "identifier" => [
                [
                    "value" => $patientMed->MDTreatmentSuggestionId // Assuming you have a unique_id field in your Patient model
                ]
            ],
            "subject" => [
                "identifier" => $patientMed->PatientId // Assuming you have a PatientId field in your Patient model
            ],
            "performer" => [
                    "identifier" => $patientMed->identifier // Assuming you have a Facility_identifier field in your Patient model
    
            ],
            "medicationReference" => [
                "reference" => '#'.$patientMed->DrugTableUUID
            ],
            "dispenseRequest" => [
                "expectedSupplyDuration" => [
                    "value" => $days,
                    "unit" => 'days',
                    "system" => "http://unitsofmeasure.org",
                    "code" => 'd'
                ]
            ],
            "dosageInstruction" => [
                [
                    "timing" => [
                        "code" => "BID"
                    ],
                    "doseAndRate" => [
                        [
                            "doseQuantity" => [
                                "value" => $numericValueDose,
                                "unit" => $unitsDose,
                                "system" => "http://unitsofmeasure.org",
                                "code" => $unitsDose
                            ]
                        ]
                    ],
                    "text" => $patientMed->DrugDose.' '."BID"
                ]
            ]
];

        // Register the patient

        $registrationResponse = ApiHelper::registerPatient($accessToken, ['resources' => [$patientData]]);

        if (isset($registrationResponse['error'])) {
            // Handle registration error
            ApiPatientList::where('PatientId', $patientId)->update(['MedicineStatus' => 'error']);
            $errorResponses[] = ['error' => $registrationResponse['error']];
        } elseif ($registrationResponse['status'] == 202) {
            // Update registration status for successful registration
            ApiPatientList::where('PatientId', $patientId)->update(['MedicineStatus' => 'sent']);
            $successResponses[] = ['message' => 'Patient Medication sent successfully'];
        }

        }

    }


        // Prepare patient data array

        // Loop through the patients and format the data
  
                return [
                    'successResponses' => $successResponses,
                    'errorResponses' => $errorResponses
                ];
    }

public static function SendVisitedPayload($accessToken, $identifier, $sending_patient)
{
       
      // Retrieve 100 patients from the database
          $patients = ApiPatientVisitedTrigView::where('identifier', '=', $identifier)
                                  ->take($sending_patient)
                                  ->get();

    $successResponses = [];
    $errorResponses = [];

        // Loop through the patients and format the data
foreach ($patients as $patientvisited) {
    $patientData = [
        "resourceType" => "Appointment",
        "meta" => [
            "lastUpdated" => $patientvisited->CreateDate ? \Carbon\Carbon::parse($patientvisited->CreateDate)->toIso8601String() : null,
            "createdAt" => $patientvisited->CreateDate ? \Carbon\Carbon::parse($patientvisited->CreateDate)->toIso8601String() : null
        ], 
        "identifier" => [
            [
                "value" => $patientvisited->PrescriptionCreationId
            ]
        ],
        "status" => 'fulfilled',
        "start" => $patientvisited->StartDate ? \Carbon\Carbon::parse($patientvisited->StartDate)->toIso8601String() : null,
        "appointmentOrganization" => [
            "identifier" => $patientvisited->identifier
        ],
        "appointmentCreationOrganization" => null,
        "participant" => [
            [
                "actor" => [
                    "identifier" => $patientvisited->PatientId_Apl
                ]
            ]
        ]
    ];

    $appointmentResponse = ApiHelper::registerPatient($accessToken, ['resources' => [$patientData]]);

    if (isset($appointmentResponse['error'])) {
        // Handle registration error
        ApiPatientList::where('PatientId', $patientvisited->PatientId_Apl)->update(['VisitedStatus' => 'error']);
        $errorResponses[] = ['error' => $appointmentResponse['error']];
    } elseif ($appointmentResponse['status'] == 202) {
        // Update registration status for successful registration
        ApiPatientList::where('PatientId', $patientvisited->PatientId_Apl)->update(['VisitedStatus' => 'sent']);
        $successResponses[] = ['message' => 'Patient Appointment visited successfully'];
    }
}
                return [
                    'successResponses' => $successResponses,
                    'errorResponses' => $errorResponses
                ];
    }
             


}
