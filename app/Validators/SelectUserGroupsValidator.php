<?php

namespace App\Validators;

use Illuminate\Support\Facades\Auth;

class SelectUserGroupsValidator
{
    /**
     * Validates the specific action.
     *
     * @param array $payload
     * @param array $restrictions
     *
     * @return bool
     */
    public static function validate($payload = [], $restrictions = [])
    {
        $user = Auth::user();
        $groups = $user->groups();

        if (count($groups) == 0) {
            return false;
        }

        return true;
    }
}
