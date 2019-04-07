# Introduction 
Manage User model that has multiple roles, complex data that change with the role or the context, 

- handle optional data as json field
- handle Json contacts
- add tutor
- laratrust extension traits
  - User
  - Team
  - Group

# Dependencies 

- drkwolf/laravel-handler
- laratrust
- laravel passport
- MediaLibrary for pictures

## default Handlers

- userCredentialHandler
 
| action        | fields                              | event                  |
| ------------- | ----------------------------------- | ---------------------- |
| update        | password, email*, username*, phone* | CredentialUpdatedEvent |
| resetPassword | password                            | CredentialUpdatedEvent |

*: optional fields

- UserHandler: Jons fields should be handled by you
  - options : UserOptions/HasOption
  - contacts: see HasContact

| action       | fields       | event            |
| ------------ | ------------ | ---------------- |
| attachAvatar | attachAvatar |                  |
| create       | *            | UserCreatedEvent |
| update       | *            | UserUpdatedEvent |

# Database

```php
Schema::create('users', function(Blueprint $table) {
    $table->increments('id');

    $table->string('username')->unique()->nullable();
    $table->string('phone')->unique()->nullable();
    $table->string('email')->unique()->nullable();
    $table->string('password')->nullable();

    $table->tinyInteger('active')->default(0);

    $table->string('first_name')->nullable();
    $table->string('last_name')->nullable();
    $table->enum('sex', ['M', 'F'])->nullable();

    $table->json('options')->nullable();
    $table->json('contacts')->nullable();

    $table->timestamps();
    $table->softDeletes();
});

Schema::create('tutor_user', function(Blueprint $table) {
    $table->integer('user_id');
    $table->integer('tutor_id');
    $table->json('options')->nullable();

    $table->timestamps();
});
```

# Managing Users

 - To create a user:
    to create a user you have to 
        - create the useroptions to intiate the UserHandler, UserOptions define the 
       schema of the options as validation $Rules, it can pass validation rules to The UserHandler and 
       validate the option, option can filter by section (array element)
        - define the user handler : 
            - filter data, 
            - validate it  
            - execute Actions, 
```php

use drkwolf\Larauser\UserHandler;
use App\Packages\Users\RolesTypes;
use Illuminate\Validation\Rule;
use App\Packages\Users\Entities\User;

use drkwolf\Larauser\UserOptions;
class DefaultUserOptions extends UserOptions {
    protected $schemas = [
        'fiedl1' => [
            'iban' => 'string',
        ],
        'field2' => [
        'insurance' => [
            'name'            => 'string',
            ]
        ],
        'therapist' => [
        ]
    ];
}

class CustomerHandler extends DefaultUserOptions {

    public function __construct($presenter, $data, $user = null) {
        $userOptions = new DefaultUserOptions(['field', 'field2']);
       parent::__construct($presenter, $data, $userOptions, RolesTypes::CUSTOMER, $user);
    }

    // override the default rules
    public function rules($action = null, $params = []) {
        $rules = [ ];
        return array_merge(
            $rules,
            $this->UserOptions->rules($action, $params);
        )
    }
}
```

## User Options

## HasOptions Trait
HasiOption is an Eloquent trait that defined and init optional fields
```php
use drkwolf\Larauser\Traits\HasOptions;

class User extends Model {
    use HasOptions;

    protected $optionsAttributes = [
        'customer' => [
            'inssurance' => [ ]
        ],
        'provider' => [ ]
    ];
}
```

## UserOptions class
User Option handle the insertion and the validation to a subset schema, it's helpfull if you need have more control on
the data inserted/update in the json field
Feature:
    - filter option 

first define options schema that contains all possible option

```php
use drkwolf\Larauser\UserOptions;

class DefaultUserOptions extends UserOptions {

    protected $schemas = [
        'bank' => [
            'iban' => 'string',
        ],
        'provider' => [
            'insurance' => [ 'name' => 'string' ]
        ],
        'sport' => [ ]
    ];

    public function __construct($data, $section = []) {
        parent::__construct($data, $section);
    }
}
```
for user with role provider options

```php
use App\Packages\Users\DefaultUserOptions;

class ProviderOptions extends DefaultUserOptions {
    const $defaultSections = ['provider', 'bank'];
}
```

updating a section :
```php
# schema should have the same structure as the deta
# we want to update only sports.player.football section
$userOptions = new PlayerOptions(['bank', 'sport']);
// OR
$userOptions = new PlayerOptions();
$user->fillWithOptions($data, $userOptions);
```

TODO
    - validate by using Laravel's rules Object

#Tutor

// TODO

#Contact

handling contact: address, emails, phones

TODO:
    - user hasOptions Trait 
    - 
