<?php use Faker\Generator as Faker;


 // generated at the same time on a di/fifferent server won't have a collision.

function get_fake_image($faker) {
    $storage_pic_path = 'users/pictures';
    $storage_faker_imgs_path = 'app/faker/pics';


    $dist_dir = storage_path('app/public/'.$storage_pic_path);
    $src_dir = storage_path($storage_faker_imgs_path);

    if(!File::exists($dist_dir)){
        File::makeDirectory($dist_dir, 0777, $recusive=true);
    }

    $src_path =  $src_dir .'/'. $faker->numberBetween(1,10) . '.jpg';
    $name = md5(uniqid(empty($_SERVER['SERVER_ADDR']) ? '' : $_SERVER['SERVER_ADDR'], true));
    $name = $name . '.jpg';
    $new_path = $dist_dir .'/'. $name;

    File::copy($src_path, $new_path);

    return $storage_pic_path . '/' . $name;
}

$factory->define(App\Packages\User\Entities\User::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\fr_CH\Address($faker));
    $faker->addProvider(new \Faker\Provider\fr_CH\PhoneNumber($faker));
    $faker->addProvider(new \Faker\Provider\fr_CH\Person($faker));
    $faker->addProvider(new \Faker\Provider\DateTime($faker));
    $faker->addProvider(new \Faker\Provider\fr_CH\Payment($faker));

    $phone_format ='0#########';

//    $pic = get_fake_image($faker);
    $phone_suffix = $faker->unique()->numerify($phone_format);
    return [
        'first_name'     => $faker->firstName,
        'last_name'      => $faker->lastName,
        'birthdate'      => $faker->dateTimeThisCentury()->format('Y-m-d'),
//        'picture'        => $pic,
        'sex'            => $faker->randomElement(['M', 'F']),
        'address'        => $faker->streetAddress,
        'postcode'       => $faker->postcode,
        'city'           => $faker->city,
        'state'          => $faker->cantonShort,
        'country'          => $faker->country,
        'contacts'      => [
            'phones'    => [
                [
                    'prefix' => '0041',
                    'suffix' => $phone_suffix,
                    'number' => '0041' . substr($phone_suffix, 1) ,
                    'name' => 'main'
                ]
            ],
            'emails'     => [
                [ 'title' => 'main', 'email' => $faker->unique()->safeEmail ]
            ]
        ]
    ];
});


$factory->state(App\Packages\User\Entities\User::class, 'login', function(Faker $faker) {
    $phone_format ='0041#########';
    return [
        'username'      => $faker->unique()->userName,
        'phone'     => $faker->unique()->numerify($phone_format),
        'email'          => $faker->unique()->safeEmail,
        'active'        => true, // 0: has no login, 1 active, 2 blocked
    ];
});

$factory->state(App\Packages\User\Entities\User::class, 'football', function(Faker $faker) {
    $passport_nbr   = '#######';
    $player_nbr     = '######';
    return [
    ];
});

$factory->state(App\Packages\User\Entities\User::class, 'player', function(Faker $faker) {
    return [
        'options' => [
            'sports' => [
                'player' => [
                    'football' => [
                        'passport' => [
                            'id' => $faker->randomNumber(),
                            'type' => 'infinit', // annual for basket
                            'expires' => now()->addYear(1)->toDateString(), // annual for basket
                        ]
                    ],
                    'basketball' => [
                        'passport' => [
                            'id' => $faker->randomNumber(),
                            'type' => 'yearly', // annual for basket
                            'expires' => now()->addYear(1)->toDateString(), // annual for basket
                        ]
                    ]
                ]
            ]
        ]
    ];
});

$factory->state(App\Packages\User\Entities\User::class, 'coach', function(Faker $faker) {
    return [
        'options' => [
            'sports' => [
                'coach' => [
                    'football' => [
                        'licence' => [
                            'id' => $faker->randomNumber(),
                            'type' => 'infinit', // annual for basket
                        ],
                        'qualifications' => [
                            'name1' => [
                                'value' => 'title',
                                'label' => 'title',
                                'name' => 'title',
                                'level' => 'A',
                                'date'  => $faker->dateTimeThisCentury(),
                                'has'   => true,
                                'details'   => true
                            ]
                        ],
                        'trainings' => [
                            'name1' => [
                                'value' => 'train1',
                                'label' => 'title',
                                'level' => 'train1',
                                'date'  => $faker->dateTimeThisCentury(),
                                'has'   => true,
                                'details'   => true
                            ]
                        ]
                    ],

                    'basketball' => [
                        'licence' => [
                            'id' => $faker->randomNumber(),
                            'type' => 'yearly', // annual for basket
                            'start' => now()->toDateString(),
                            'end'   => now()->addYear(1)->ToDateString()
                        ],
                        'qualifications' => [
                            'name1' => [
                                'value' => 'train1',
                                'label' => 'train1',
                                'level' => 'train1',
                                'date'  => $faker->dateTimeThisCentury(),
                                'has'   => true,
                                'details'   => true,
                            ]
                        ],
                        'trainings' => [
                            'name1' => [
                                'value' => 'train1',
                                'label' => 'train1',
                                'level' => 'train1',
                                'date'  => $faker->dateTimeThisCentury(),
                                'has'   => true,
                                'details'   => true
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
});

$factory->state(App\Packages\User\Entities\User::class, 'tutor', function(Faker $faker) {

    $phone_format ='0041#########';
    return [
        'phone'     => $faker->unique()->numerify($phone_format),
        'email'     => $faker->unique()->safeEmail,
    ];
});

