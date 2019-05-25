# Introduction

Manage User model that has multiple roles, complex data that change with the role or the context, and multiple contacts

Features:

- handle optional data: filter and validation
- handle contacts

## Dependencies

- drkwolf/laravel-handler : for request handler and data presenter
- spatie/laravel-medialibrary : Optional, form user's avatar
- laravel/passport : Optional, authentication
- santigarcor/laratrust : Optional managing teams and groups

## Database

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

## Configuration

publish the configuration file

```bash
php artisan vendor:publish --provider="drkwolf\Larauser\LarauserServiceProvider" --tag="config"
```

setup user model parameters

```php
'model' => [
    // field name ($request)
    'avatar_field' => 'avatar',
    // filesystem disk where default pic is
    // public disk should have url attribute !
    'avatar_disk' => 'public',
    'avatar_collection' => 'avatars',
    'avatar_default' => 'defaults/avatar-102.png',
    // validation rules
    'rules' => [
        'default' => [
            'first_name'    => 'required|string|max:50'
            // ...
        ]
    ]
]
```

rules can also be a function

```php
'model' => [
    //...
    'rules' => [
        'default' => function ($user_model) {
            return [
                // ...
                'email'         => 'nullable|email|unique:users,email' . ",{$user_model->id}"
            ]
        }
        ],
    ]
]
```

## default Handlers

### UserCredentialHandler

Update credentials password, email, username and phone
 
| action        | fields                              | event                  |
| ------------- | ----------------------------------- | ---------------------- |
| update        | password, email*, username*, phone* | CredentialUpdatedEvent |
| resetPassword | password                            | CredentialUpdatedEvent |

*: optional fields

### UserHandler

handle user main fields, options and contacts

| action       | fields       | event            |
| ------------ | ------------ | ---------------- |
| attachAvatar | attachAvatar |                  |
| create       | *            | UserCreatedEvent |
| update       | *            | UserUpdatedEvent |

### UserInfoHandler

same as UserHandler but doesn't handle options field

## Managing Users

```php
use drkwolf\Package\Presenter\DefaultPresenter as Presenter;
// creating
$presenter = new Presenter();
$action = 'create'
$response = UserHandler::resolve(
    $action, $params = [],
    $presenter, $request->data, 'admin');

// updating
$presenter = new UserPresenter();
$action = 'update'
$response = UserHandler::resolve(
    $action, $params = [],
    $presenter, $request->data, 'admin');
```

## HasOptions Trait

see docs/options

## HasContacts Trait

see docs/contacts