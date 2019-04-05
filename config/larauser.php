<?php

return [
    'contact' => [
        'default' => '_default_',
        // possible contact, contacts
        'fillable' => ['contact'], 
        'attributes' => [
            'address' => [
                'address' => null,
                'postcode' => null,
                'state' => null,
                'city' => null,
                'country' => null,
                'latitude' => null,
                'longitude' => null,
            ],
            'email' =>  null,
            'phone' => [
                'prefix' => null,
                'suffix' => null,
            ]
        ],
        // 'phone' => [
        //     'default_country' => 'CH',
        // ]
    ],
    'options' => [
        'attributes' => []
    ]

]