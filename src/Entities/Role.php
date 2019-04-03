<?php namespace drkwolf\Larauser\Entities;

use Laratrust\Models\LaratrustRole;

/**
 * drkwolf\Larauser\Entities\Role
 */
class Role extends LaratrustRole {

    protected $fillable =  ['id', 'name', 'display_name'];

    /**
     * is club and team are set return users with that condition
     */
    public function users($club_id = null, $team_id = null) {
        return $this->hasManyThrough(
            User::class, RoleUser::class,
            'role_id', 'id',
            'id', 'user_id')
            // ->when($club_id, function($q) use($club_id) { return $q->whereClubId($club_id); })
            // ->when($team_id, function($q) use($team_id) { return $q->whereTeamId($team_id); })
//            ->distinct('users.id')
            ->distinct()
        ;
        // FIXME users->count('users.id') to get the response correct
    }

    public function getUserIdsAttribute() {
       return $this->Users($this->club_id, $this->team_id)
           ->get(['id'])->pluck('id');
    }
}
