<?php namespace drkwolf\Larauser\Entities;

use drkwolf\Larauser\Traits\HasContacts;
use drkwolf\Larauser\Traits\HasOptions;
use drkwolf\Larauser\Traits\HasTutors;

use Carbon\Carbon;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * drkwolf\Larauser\Entities\User
 */
class User extends Authenticatable implements HasMedia {
    use HasApiTokens;
    use Notifiable;
    use HasContacts;
    use HasOptions;
    use HasMediaTrait;
    use HasTutors;

    protected $attributes = [
        'active' => 0, // not active
        'country' => 'CH'
    ];

    protected $casts = [
        'active' => 'integer',
    ];
//
    // not fillable picture, password
    protected $fillable = [
        'username', 'phone', 'email',
        'first_name', 'last_name',  'sex', 'birthdate',
    ];

    protected $guarded = ['active'];
    protected $hidden = ['password'];
    protected $appends  = ['name'];
    protected $dates    = ['birthdate'];

    // region attributes
    /** 
     * Encrypt password before setting it
     */
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getNameAttribute() {
        return $this->last_name . ' ' . $this->first_name;
    }

    public function getAgeAttribute() {
        return $this->birthdate->age;
    }

    public function getAvatarUrlAttribute() {
        $avatarCollection = config('larauser.model.avatar_collection', 'avatars');
        $defaultAvatar = config('larauser.model.avatar_default');
        $pic = $this->getMedia($avatarCollection)->last();
        return $pic
            ? $pic->getFullUrl()
            : \Storage::disk('public')->url($defaultAvatar); ; // TODO defaultPicture
    }
    // endregion attributes
}
