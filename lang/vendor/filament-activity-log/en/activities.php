<?php

return [
    'title' => 'Activity history',

    'date_format' => 'j F, Y',
    'time_format' => 'H:i l',

    'filters' => [
        'date' => 'Date',
        'causer' => 'Initiator',
        'subject_type' => 'Subject',
        'subject_id' => 'Subject ID',
        'event' => 'Action',
    ],
    'table' => [
        'field' => 'Field',
        'old' => 'Old',
        'new' => 'New',
        'value' => 'Value',
        'no_records_yet' => 'There are no entries yet',
    ],
    'events' => [
        'created' => [
            'title' => 'Created',
            'description' => 'Entry created',
        ],
        'updated' => [
            'title' => 'Updated',
            'description' => 'Entry updated',
        ],
        'deleted' => [
            'title' => 'Deleted',
            'description' => 'Entry deleted',
        ],
        'restored' => [
            'title' => 'Restored',
            'description' => 'Entry restored',
        ],
        'login' => [
            'title' => 'Login',
            'description' => 'User Logged In',
        ],
        'logout' => [
            'title' => 'Logout',
            'description' => 'User Logged Out',
        ],
        'request-x-reading' => [
            'title' => 'Request X-Reading',
            'description' => 'Requested X-Reading',
        ],
        'request-z-reading' => [
            'title' => 'request Z-Reading',
            'description' => 'Requested Z-Reading',
        ],
    ],
    'boolean' => [
        'true' => 'True',
        'false' => 'False',
    ],
];
