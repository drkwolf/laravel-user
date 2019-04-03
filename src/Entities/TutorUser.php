<?php namespace drkwolf\Larauser\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * drkwolf\Larauser\Entities\Role
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Office[] $Offices
 * @property-read \Illuminate\Database\Eloquent\Collection|\drkwolf\Larauser\Entities\User[] $users
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $name
 * @property string $alias
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TutorUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TutorUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TutorUser query()
 * @property int $user_id
 * @property int $tutor_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TutorUser whereTutorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TutorUser whereUserId($value)
 */
class TutorUser extends Model
{
    protected $table = 'tutor_user';
}
