<?php namespace drkwolf\Larauser\Traits;

use drkwolf\Larauser\Entities\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

trait LaratrustTeamTrait {

    /*
     |------------------------------------------------------
     | Relationships
     |------------------------------------------------------
     */

    public function Group() {
        return $this->belongsTo(
            config('laratrust.models.group'),
            config('laratrust.foreign_keys.group', 'group_id')
        );
    }

    public function Roles() {
        return $this->hasManyThrough(
            Config::get('laratrust.models.user'),
            Config::get('laratrust.models.role_user'),
            Config::get('laratrust.foreign_keys.team'),
            'id',
            'id',
            Config::get('laratrust.foreign_keys.role')
        );
    }

    /**
     * @param mixed $roles String|Array roles_name
     * @return mixed
     */
    public function usersWithRole($roles) {
        $query = $this->users();
        $role_key = Config::get('laratrust.foreign_keys.role');
        if (is_array($roles)) {
            $role_id = Role::whereIn('name', $roles)->get(['id'])->pluck('id')->toArray();
            return $query->wherePivotIn($role_key, $role_id)
                ->withPivot($role_key);
        } else {
            $role_id = Role::where('name', $roles)->firstOrFail()->id;
            return $query->wherePivot($role_key, $role_id);
        }

    }

    /*
     |------------------------------------------------------
     | User's Role Relationships
     |------------------------------------------------------
     */

    public function detachRoleAllUsers($role) {
        $users = $this->usersWithRole($role)->get();
        $group_key = Config::get('laratrust.foreign_keys.group');
        $ids = [];
        foreach ($users as $user) {
            $ids[] = $user->id;
            $user->detachGroupTeam($role, $this->{$group_key}, $this->id);
        }
        return $ids;
    }

    public function detachRoleUsers($role, $usersIds) {
        $group_key = Config::get('laratrust.foreign_keys.group');
        $usersIds = is_array($usersIds) ? $usersIds : [$usersIds];

        $users = $this->group->usersWithRole($role)
            ->whereIn('id', $usersIds)
            ->distinct('users.id')->get();

        $ids = [];
        foreach ($users as $user) {
            $ids[] = $user->id;
            $user->detachGroupTeam($role, $this->{$group_key}, $this->id);
        }
        return $ids;
    }

    public function syncRoleUsers($role, array $usersIds) {
        $group_key = Config::get('laratrust.foreign_keys.group');

        /** @var Collection $oldIds  */
        $oldUsers = $this->usersWithRole($role)->get(['id']);
        /** @var Collection $users  */
        $users = $this->usersWithRole($role)
            ->whereIn('id', $usersIds)
            ->distinct()->get();

        $oldIds     = $oldUsers->pluck('id');
        $newIds     = $users->pluck('id');
        $detachIds  = $oldIds->diff($newIds);
        $attachIds  = $newIds->diff($oldIds);

        $attach     = collect();
        $detach     = collect();


        // FIXME merge $oldUsers and $users
        $allUsersIds = $newIds->merge($oldIds)->unique()->toArray();
        $users = $this->group->usersWithRole($role)
            ->whereIn('id', $allUsersIds)
            ->get();

        foreach ($users as $user) {
            if ($attachIds->contains($user->id)) {
                $user->attachGroupTeam($role, $this->{$group_key}, $this->id);
                $attach->push($user);
            } else if ($detachIds->contains($user->id)) {
                $user->detachGroupTeam($role, $this->{$group_key}, $this->id);
                $detach->push($user);
            }
        }

        return compact('attach', 'detach');
    }

    public function detachUsersByRole($role, array $usersIds) {
        $group_key = Config::get('laratrust.foreign_keys.group');
        $detached = collect();
        $users = $this->group->usersWithRole($role)
            ->whereIn('id', $usersIds)
            ->distinct()
            ->get();

        foreach ($users as $user) {
            $user->detachGroupTeam($role, $this->{$group_key}, $this->id);
            $detached->push($user);
        }

        return $detached;
    }

    /**
     * users should be in the group before attaching
     * user_id team_id group_id assigned with role_id
     */
    public function attachUsersByRole($role, array $usersIds) {
        $group_key = Config::get('laratrust.foreign_keys.group');
        $attached = collect();
        $users = $this->group->usersWithRole($role)
            ->whereIn('id', $usersIds)->distinct()->get();
        // dump($usersIds, $users);
        foreach ($users as $user) {
            $user->attachGroupTeam($role, $this->{$group_key}, $this->id);
            $attached->push($user);
        }

        return $attached;
    }
}
