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
 * @property-read \App\Packages\Club\Entities\Club $Club
 * @property-read \drkwolf\Larauser\Entities\Role $Role
 * @property-read \App\Packages\Team\Entities\Team $Team
 * @property-read \drkwolf\Larauser\Entities\User $User
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser query()
 * @property int $counter
 * @property string $user_type
 * @property int $user_id
 * @property int $role_id
 * @property int|null $team_id
 * @property int|null $club_id
 * @property array|null $metadata
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser whereClubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser whereCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\drkwolf\Larauser\Entities\RoleUser whereUserType($value)
 */
class RoleUser extends Model {
    protected $table = 'role_user';

    protected $casts = [
        'metadata' => 'array'
    ];


    public function User()
    {
        return  $this->belongsTo(User::class);
    }

    public function Role()
    {
        return  $this->belongsTo(Role::class);
    }
}
