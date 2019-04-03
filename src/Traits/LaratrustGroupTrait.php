<?php namespace drkwolf\Larauser\Traits;

use drkwolf\Larauser\Entities\Role;
use Illuminate\Support\Facades\Config;

trait LaratrustGroupTrait {

    public function users() {
        return $this->usersWithRole();
    }
    /**
     * Morph by Many relationship between the role and the one of the possible user models.
     *
     * @param  string $relationship
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function getMorphByUserRelation($relationship)
    {
        return $this->morphedByMany(
            Config::get('laratrust.user_models')[$relationship],
            'user',
            Config::get('laratrust.tables.role_user'),
            Config::get('laratrust.foreign_keys.group'),
            Config::get('laratrust.foreign_keys.user')
        );
    }

    public function initializeLaratrustGroupTrait () {
        $this->table = Config::get('laratrust.tables.groups');
    }

    public function usersWithRole($rolename = null) {
        $relation = $this->hasManyThrough(
            Config::get('laratrust.models.user'),
            Config::get('laratrust.models.role_user'),
            'group_id',
            'id',
            'id',
            'user_id'
        );

        if ($rolename === null) return $relation->distinct(); // all users

        $roles = is_array($rolename)
            ? Role::whereIn('name', $rolename)->get(['id'])->pluck('id')->toArray()
            : Role::where('name', $rolename)->firstOrFail()->id;

        is_array($roles)
            ? $relation->whereIn('role_id', $roles)
            : $relation->where('role_id', $roles);

        return $relation->distinct();
    }


    /**
     * Boots the group model and attaches event listener to
     * remove the many-to-many records when trying to delete.
     * Will NOT delete any records if the group model uses soft deletes.
     *
     * @return void|bool
     */
    public static function bootLaratrustGroupTrait()
    {
        static::deleting(function ($group) {
            if (method_exists($group, 'bootSoftDeletes') && !$group->forceDeleting) {
                return;
            }

            foreach (array_keys(Config::get('laratrust.user_models')) as $key) {
                $group->$key()->sync([]);
            }
        });
    }

    public function attachUsersByRole($role, array $usersIds) {
        $User = Config::get('laratrust.models.user');
        $users = $User::whereIn('id', $usersIds)
                    ->distinct()->get();
        $attached = collect();

        foreach ($users as $user) {
            if (! $user->hasGroupRole($role, $this->id) ) {
                $user->attachGroupTeam($role, $this->id, null);
                $attached->push($user);
            }
        }

        return $attached;
    }


    public function detachUsersByRole($role, array $usersIds) {
        $users = $this->usersWithRole($role)
            ->whereIn('id', $usersIds)
            ->distinct()->get();

        foreach ($users as $user) {
            $res = $user->detachGroupTeam($role, $this->id, null);
        }

        return $users;
    }

    // FIXME merge with laraTrustTeamTrait
    public function syncUsersByRole($role, array $usersIds) {
        $User = Config::get('laratrust.models.user');
        /** @var Collection $oldIds  */
        $oldUsers = $this->usersWithRole($role)->get(['id']);
        /** @var Collection $users  */
        $users = $User::whereIn('id', $usersIds)
                     ->distinct()->get();

        $oldIds     = $oldUsers->pluck('id');
        $newIds     = $users->pluck('id');
        $detachIds  = $oldIds->diff($newIds);
        $attachIds  = $newIds->diff($oldIds);

        $attach     = collect();
        $detach     = collect();


        // FIXME merge $oldUsers and $users
        $allUsersIds = $newIds->merge($oldIds)->unique()->toArray();
        $users = $User::whereIn('id', $allUsersIds)
                       ->get();

        foreach ($users as $user) {
            if ($attachIds->contains($user->id)) {
                $user->attachGroupTeam($role, $this->id, null);
                $attach->push($user);
            } else if ($detachIds->contains($user->id)) {
                $user->detachGroupTeam($role, $this->id, null);
                $detach->push($user);
            }
        }

        return compact('attach', 'detach');
    }

    /*
     |---------------------------------------------------------------------------
     | Helpers methods
     |---------------------------------------------------------------------------
     */

    /**
     * @param $team_id number array of number
     * @return bool
     */
    public function hasTeams($team_id) {
        return is_array($team_id)
            ? $this->teams()->whereIn('id', $team_id)->exists()
            : $this->teams()->where('id', $team_id)->exists()
            ;
    }
}
