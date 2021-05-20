<?php

use App\Enums\LeadStatus;

return [

    /*
    |--------------------------------------------------------------------------
    | Generated Open API file
    |--------------------------------------------------------------------------
    |
    | Setting for filename
    |
    */
    'open_api'                     => [
        'filename' => 'open-api.json',
    ],

    /*
    |-------------------------------------------
    | Current API Version
    |-------------------------------------------
    | That is the default API version of your API (Last version).
    | The idea is that if there is no version when calling the API, this will be used
    */
    'api_latest'                   => 'v1',

    /*
    |-------------------------------------------
    | Lead status setting
    |-------------------------------------------
    | Lead status are updated from GREEN => YELLOW => RED => EXPIRED
    | When a lead has stayed in a given status for the defined duration below,
    | we will update the lead to the next status.
    */
    'lead_status_duration_seconds' => [
        LeadStatus::GREEN  => 60 * 60 * 24 * 3,
        LeadStatus::YELLOW => 60 * 60 * 24 * 3,
        LeadStatus::RED    => 60 * 60 * 24 * 3,
    ],

    /*
    |-------------------------------------------
    | File import
    |-------------------------------------------
    | file import option setting
    */
    'import'                       => [
        'max_size' => 1024 * 20, //20 MB
    ],
];
