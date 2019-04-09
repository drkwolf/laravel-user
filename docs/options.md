# Options
option define the json attribute for a model
filter attributes

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

2. add HasOptions to your model
 
```php
use drkwolf\Larauser\Traits\HasOptions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use HasOptions;
}
```

## Configuration

1. publish the configration

```bash
php artisan vendor:publish --provider="drkwolf\Larauser\LarauserServiceProvider" --tag="config"
```
2. edit options attributes

```php
    'options' => [
        'attributes' => [
            // 'field1' => [],
            // 'field2' => [],
        ],
        'filters' => [
            // 'customer' => ['field1']
        ]
    ]
```