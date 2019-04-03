# Introduction 
- handle optional data as json field
- handle Json contacts
- add tutor
- laratrust extension traits
  - User
  - Team
  - Group

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
# Options

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
if you have different user's roles, with each one has his optional data

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
