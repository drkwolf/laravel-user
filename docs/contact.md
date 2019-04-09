# Contacts

manage multiple contacts as json field

## TODO

- add validation to the configuration by user's role
- handle credential field phone/email
- update api
 
## Setup

1. add contacts field to your table

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {
    public function up() {
        Schema::create('users', function(Blueprint $table) {
            $table->json('contacts')->nullable();
        });
    }
}
```

2. add HasContacts to your model
 
```php
use drkwolf\Larauser\Traits\HasContacts;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use HasContacts;
}
```

## Configuration
1. publish the configration
```bash
php artisan vendor:publish --provider="drkwolf\Larauser\LarauserServiceProvider" --tag="config"
```

2. set you default default contact and default attributes 
 
```php
# @config/larauser.php
return [
    'contact' => [
        /** default's user contact's name */
        'default' => '_default_',
        // default contact fillable attribute for eloquent model
        // contact is alias default_contact
        'fillable' => ['contact'],
        /** default contact's attributes */
        'attributes' => [
            'address' => [
                'address' => null,
                'postcode' => null,
                'state' => null,
                'city' => null,
                'country' => null,
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
    ],
];

```

## Usage

```
$user = new User();
$user->contact = [
    'address' => [
        'address'        => $faker->streetAddress,
        'postcode'       => $faker->postcode,
        'city'           => $faker->city,
        'state'          => $faker->cantonShort,
        'country'        => $faker->country,
    ],
    'phone'    => [
        'prefix' => $phone_prefix,
        'suffix' => $phone_suffix,
    ],
    'email'     =>  $faker->unique()->safeEmail
];
$user->save();
```
## Default contact
