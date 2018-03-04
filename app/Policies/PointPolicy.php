<?php

namespace App\Policies;

use App\User;
use App\Point;
use Illuminate\Auth\Access\HandlesAuthorization;

class PointPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the point.
     *
     * @param  \App\User  $user
     * @param  \App\Point  $point
     * @return mixed
     */
    public function view(User $user, Point $point)
    {
        //
    }

    /**
     * Determine whether the user can create points.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the point.
     *
     * @param  \App\User  $user
     * @param  \App\Point  $point
     * @return mixed
     */
    public function update(User $user, Point $point)
    {
        return $point->map->user->id === $user->id;
    }

    /**
     * Determine whether the user can delete the point.
     *
     * @param  \App\User  $user
     * @param  \App\Point  $point
     * @return mixed
     */
    public function delete(User $user, Point $point)
    {
      return $point->map->user->id === $user->id;
    }
}
