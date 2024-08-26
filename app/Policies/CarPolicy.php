<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\Owner;

class CarPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given car can be viewed by the user.
     */
    public function view(Owner $owner, Car $car)
    {
        return $owner->id === $car->owner_id;
    }

    /**
     * Determine if the given car can be updated by the user.
     */
    public function update(Owner $owner, Car $car)
    {
        return $owner->id === $car->owner_id;
    }

    /**
     * Determine if the given car can be deleted by the user.
     */
    public function delete(Owner $owner, Car $car)
    {
        return $owner->id === $car->owner_id;
    }
}
