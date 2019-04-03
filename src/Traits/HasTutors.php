<?php namespace drkwolf\Larauser\Traits;

use App\Packages\User\Entities\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

Trait HasTutors {

    public function tutors() :BelongsToMany
    { // use defaultNULL for anonymous office
        return $this->belongsToMany(
            User::class,
            'tutor_user',
            'user_id',
            'tutor_id'
        );
    }

    public function minors() :BelongsToMany {
        return $this->belongsToMany(
            User::class,
            'tutor_user',
            'tutor_id', // inversed
            'user_id'
        );
    }

    /**
     * check if the current use has a tutor
     * @param User|int $user
     * @return bool
     */
    public function hasTutor($user)
    {
        $id = is_numeric($user) ? $user : $user->id;
        foreach ($this->tutors as $tutor)  {
            if ($tutor->id == $id) return true;
        }
        return false;
    }

    public function hasMinor($user)
    {
        $id = is_numeric($user) ? $user : $user->id;
        foreach ($this->minors as $minor)  {
            if ($minor->id == $id) return true;
        }
        return false;
    }

    public function attachTutor($tutor)
    {
       if (!$this->hasTutor($tutor))  {
          return $this->tutors()->attach($tutor);
       }
    }

    public function attachMinor($user)
    {
        if (!$this->hasTutor($user))  {
            return $this->minors()->attach($user);
        }
    }

    // TODO children, hasChild
}
