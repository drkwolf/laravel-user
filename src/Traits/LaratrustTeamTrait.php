<?php namespace drkwolf\Larauser\Traits;

use drkwolf\Larauser\Entities\Role;
use drkwolf\Larauser\Entities\RoleUser;
use Illuminate\Support\Collection;

trait LaratrustTeamTrait {

    /*
     |------------------------------------------------------
     | Relationships
     |------------------------------------------------------
     */

    public function Roles() {
        return $this->hasManyThrough(
            Config::get('laratrust.models.user'),
            Config::get('laratrust.models.role_user'),
            'team_id',
            'id',
            'id',
            'role_id'
        );
    }

    /**
     * @param mixed $roles String|Array roles_name
     * @return mixed
     */
    public function usersWithRole($roles) {
        $query = $this->users();
        if (is_array($roles)) {
            $role_id = Role::whereIn('name', $roles)->get(['id'])->pluck('id')->toArray();
            return $query->wherePivotIn('role_id', $role_id)
                ->withPivot('role_id');
        } else {
            $role_id = Role::where('name', $roles)->firstOrFail()->id;
            return $query->wherePivot('role_id', $role_id);
        }

    }

    /*
     |------------------------------------------------------
     | User's Role Relationships
     |------------------------------------------------------
     */

    public function detachRoleAllUsers($role) {
        $users = $this->usersWithRole($role)->get();
        $ids = [];
        foreach ($users as $user) {
            $ids[] = $user->id;
            $user->detachGroupTeam($role, $this->group_id, $this->id);
        }
        return $ids;
    }

    public function detachRoleUsers($role, $usersIds) {
        $usersIds = is_array($usersIds) ? $usersIds : [$usersIds];

        $users = $this->group->usersWithRole($role)
            ->whereIn('id', $usersIds)
            ->distinct()->get();

        $ids = [];
        foreach ($users as $user) {
            $ids[] = $user->id;
            $user->detachGroupTeam($role, $this->group_id, $this->id);
        }
        return $ids;
    }

    public function syncRoleUsers($role, array $usersIds) {
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
//        dump(
////            'usersIds', $usersIds,
//            'newIds', $newIds,
////            'attachIds', $attachIds,
////            'detachIds', $detachIds
//        'union', $users->pluck('id')
//        );

        foreach ($users as $user) {
            if ($attachIds->contains($user->id)) {
                $user->attachGroupTeam($role, $this->group_id, $this->id);
                $attach->push($user);
            } else if ($detachIds->contains($user->id)) {
                $user->detachGroupTeam($role, $this->group_id, $this->id);
                $detach->push($user);
            }
        }

        return compact('attach', 'detach');
    }

    public function detachUsersByRole($role, array $usersIds) {
        $detached = collect();
        $users = $this->group->usersWithRole($role)
            ->whereIn('id', $usersIds)
            ->distinct()
            ->get();

        foreach ($users as $user) {
            $user->detachGroupTeam($role, $this->group_id, $this->id);
            $detached->push($user);
        }

        return $detached;
    }

    /**
     * users should be in the group before attaching
     * user_id team_id group_id assigned with role_id
     */
    public function attachUsersByRole($role, array $usersIds) {
        $attached = collect();
        $users = $this->group->usersWithRole($role)
            ->whereIn('id', $usersIds)->distinct()->get();
        // dump($usersIds, $users);
        foreach ($users as $user) {
            $user->attachGroupTeam($role, $this->group_id, $this->id);
            $attached->push($user);
        }

        return $attached;
    }
}
