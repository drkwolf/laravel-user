<?php

return [
    'model' => [
        'avatar_collection' => 'avatars',
        // disk public <=> app/public/defaults
        'avatar_default' => 'defaults/avatar-102.png'
    ],
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
        'attributes' => [
            // 'field1' => [],
            // 'field2' => [],
        ],
        'filters' => [
            // 'customer' => ['field1']
        ]
    ]

]