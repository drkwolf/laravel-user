<?php

return [
    'model' => [
        'avatar_collection' => 'avatars',
        // disk public <=> app/public/defaults
        'avatar_default' => 'defaults/avatar-102.png'
    ],
    'contact' => [
        /** default contact name */
        'default' => '_default_',
        // possible contact, contacts
        'fillable' => ['contact'], 
        /** default contact's attributes */
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
        'validation' => [

        ]
    ],
    'options' => [
        'attributes' => [
            // 'field1' => [],
            // 'field2' => [],
        ],
        'filters' => [
            // 'customer' => ['field1']
        ],
        'validation' => [

        ]
    ]
];