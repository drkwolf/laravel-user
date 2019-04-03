# Database
```php
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');

            $table->string('username')->unique()->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('active')->default(0);

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('sex', ['M', 'F'])->nullable();

            $table->longText('options')->nullable();
            $table->longText('contacts')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tutor_user', function(Blueprint $table) {
            $table->integer('user_id');
            $table->integer('tutor_id');
            $table->longText('options')->nullable();

            $table->timestamps();
        });
```
# User Options

populate user.options json fied

```php
use App\Packages\User\UserOptions;
use Illuminate\Support\Arr;

class CoachOptions extends UserOptions {
    protected $schemas = [
        'sports' => [
            'player' => [
                    'football' => [
                        'passport.id'     => 'number',
                        'passport.expire' => 'date',
                        'post'            => 'string',
                        'number'          => 'number',
                    ],
            ]
        ]
    ];

    public function getSchema($section) {
        if (Arr::has($this->schemas, $sport)) {
            return [$this->prefix => $this->schemas[$sport]];
        } else {
            throw new \InvalidArgumentException('invalid coach sport :'.$sport);
        }
    }
}

updating a section :
```php
# data an schema should have the same structure
# we want to update only sports.player.football section
$userOptions = new PlayerOptions($request->options, 'sports.player.football')'
$userOptions->getAttributes($user->data)
```
```

# Creating User
