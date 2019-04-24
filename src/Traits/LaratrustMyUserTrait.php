<?php namespace drkwolf\Larauser\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laratrust\Helper;
use Laratrust\Traits\LaratrustUserTrait;

Trait LaratrustMyUserTrait {
    use LaratrustUserTrait {
        LaratrustUserTrait::roles as _old_roles;
    }

    public function roles() {
        $roles = $this->morphToMany(
            Config::get('laratrust.models.role'),
            'user',
            Config::get('laratrust.tables.role_user'),
            Config::get('laratrust.foreign_keys.user'),
            Config::get('laratrust.foreign_keys.role')
        );

        if (Config::get('laratrust.use_teams')) {
            $roles->withPivot(Config::get('laratrust.foreign_keys.team'));
        }

        if (Config::get('laratrust.use_groups')) {
            $roles->withPivot(Config::get('laratrust.foreign_keys.group'));
        }

        return $roles;
    }

    public function systemRoles() {
        $group_key = Config::get('laratrust.foreign_keys.group');
        $team_key = Config::get('laratrust.foreign_keys.team');
        return $this->roles()->whereNull($group_key)->whereNull($team_key);
    }

    // TODO move to group Trait
    public function attachGroupTeam($role, $group, $team = null)
    {
        return $this->attachGroupModel('roles', $role, $group, $team);
    }

    public function detachGroupTeam($role, $group, $team) {
        return $this->detachGroupModel('roles', $role, $group, $team);
    }

    public function syncGroupTeam($role, $group, $team) {
        return $this->detachGroupModel('roles', $role, $group, $team);
    }

    private function attachGroupModel($relationship, $object, $group, $team) {
        if (!Helper::isValidRelationship($relationship)) {
            throw new \InvalidArgumentException;
        }

        $group_key = Config::get('laratrust.foreign_keys.group');
        $attributes = [$group_key => Helper::getIdFor($group, 'group')];
        $objectType = Str::singular($relationship);
        $object = Helper::getIdFor($object, $objectType);

        if (\Config::get('laratrust.use_teams')) {
            $team = Helper::getIdFor($team, 'team');

            if (
            $this->$relationship()
                ->wherePivot(Helper::teamForeignKey(), $team)
                ->wherePivot(\Config::get("laratrust.foreign_keys.{$objectType}"), $object)
                ->count()
            ) {
                return $this;
            }

            $attributes[Helper::teamForeignKey()] = $team;
        }

        $this->$relationship()->attach(
            $object,
            $attributes
        );

        $this->flushCache();
        $this->fireLaratrustEvent("{$objectType}.attached", [$this, $object, $team]);

        return $this;
    }

    public function hasGroupRole($name, $group, $team = null, $requireAll = false)
    {
        $name = Helper::standardize($name);
        list($team, $requireAll) = Helper::assignRealValuesTo($team, $requireAll, 'is_bool');

        if (is_array($name)) {
            if (empty($name)) {
                return true;
            }

            foreach ($name as $roleName) {
                $hasRole = $this->hasGroupRole($roleName, $group, $team);

                if ($hasRole && !$requireAll) {
                    return true;
                } elseif (!$hasRole && $requireAll) {
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the roles were found.
            // If we've made it this far and $requireAll is TRUE, then ALL of the roles were found.
            // Return the value of $requireAll.
            return $requireAll;
        }

        $team = Helper::fetchTeam($team);

        // TODO cache me
        foreach ($this->roles as $role) {
            if ($role['name'] == $name && self::isInSameGroupTeam($role, $group, $team)) {
                return true;
            }
        }

        return false;
    }

    public static function GroupForeignKey()
    {
        return Config::get('laratrust.foreign_keys.group');
    }


    /**
     * Alias to eloquent many-to-many relation's sync() method.
     * TOD
     * @param  string  $relationship
     * @param  mixed  $objects
     * @param  mixed  $team
     * @param  boolean  $detaching
     * @return static
     */
    private function syncGroupModel($relationship, $objects, $group, $team, $detaching) {
        if (!Helper::isValidRelationship($relationship)) {
            throw new InvalidArgumentException;
        }

        $objectType = Str::singular($relationship);
        $mappedObjects = [];
        $useTeams = Config::get('laratrust.use_teams');
        $team = $useTeams ? Helper::getIdFor($team, 'team') : null;

        foreach ($objects as $object) {
            if ($useTeams && $team) {
                $mappedObjects[Helper::getIdFor($object, $objectType)] = [Helper::teamForeignKey() => $team];
            } else {
                $mappedObjects[] = Helper::getIdFor($object, $objectType);
            }
        }

        $relationshipToSync = $this->$relationship();

        if ($useTeams && $team) {
            $relationshipToSync->wherePivot(Helper::teamForeignKey(), $team);
        }

        $result = $relationshipToSync->sync($mappedObjects, $detaching);

        $this->flushCache();
        $this->fireLaratrustEvent("{$objectType}.synced", [$this, $result, $team]);

        return $this;
    }

    /**
     * Alias to eloquent many-to-many relation's detach() method.
     *
     * @param  string  $relationship
     * @param  mixed  $object
     * @param  mixed  $team
     * @return static
     */
    private function detachGroupModel($relationship, $object, $group, $team)
    {
        if (!Helper::isValidRelationship($relationship)) {
            throw new InvalidArgumentException;
        }

        $group_key = Config::get('laratrust.foreign_keys.group');
        $attributes = [$group_key => Helper::getIdFor($group, 'group')];
        $objectType = Str::singular($relationship);
        $relationshipQuery = $this->$relationship();

        if (Config::get('laratrust.use_teams')) {
            $relationshipQuery->wherePivot(
                Helper::teamForeignKey(),
                Helper::getIdFor($team, 'team')
            );

            $attributes[Helper::teamForeignKey()] = $team;
        }

        $object = Helper::getIdFor($object, $objectType);
//        dump('detach', $object, $attributes);
        $relationshipQuery->detach(
            $object,
            $attributes
        );

        $this->flushCache();
        $this->fireLaratrustEvent("{$objectType}.detached", [$this, $object, $team]);

        return $this;
    }


    public static function isInSameGroupTeam($rolePermission, $group, $team)
    {
        if (
            !Config::get('laratrust.use_teams')
            || (!Config::get('laratrust.teams_strict_check') && is_null($team))
        ) {
            return true;
        }

        $teamForeignKey = Helper::teamForeignKey();
        $groupForeignKey = self::groupForeignKey();

        return $rolePermission['pivot'][$teamForeignKey] == $team &&
         $rolePermission['pivot'][$groupForeignKey] == $group;
    }

    /**
     * user's teams with role_name
     * @param array|string $role_name
     * @param Integer $club_id
     * @return teams
     */
    public function teamsWithRoles($role_name, $club_id = null) {
        $relation = $this->hasManyThrough(
        Config::get('laratrust.models.team'),
        Config::get('laratrust.models.role'),
            Config::get('laratrust.foreign_keys.user'),
            'id',
            'id',
            Config::get('laratrust.foreign_keys.team')
        );

        $RoleModel = Config::get('laratrust.models.role');
        $roles = is_array($role_name)
            ? $RoleModel::whereIn('name', $role_name)->get(['id'])->pluck('id')->toArray()
            : $RoleModel::where('name', $role_name)->firstOrFail()->id;

        $club_id ? $relation->where('role_user.club_id', $club_id) : $relation->whereNull('role_user.club_id');

        is_array($roles)
            ? $relation->whereIn('role_id', $roles)
            : $relation->where('role_id', $roles);

        return $relation->distinct();
    }
}
