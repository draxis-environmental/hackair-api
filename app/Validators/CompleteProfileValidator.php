<?php

namespace App\Validators;

use Illuminate\Support\Facades\Auth;

class CompleteProfileValidator
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
        $requiredFields = array('year_of_birth', 'gender', 'location');
        $groups = $user->groups();
        $outdoorActivities = $user->outdoorActivities();

        foreach($requiredFields as $field) {
            if (empty($user[$field]) == true) {
                return false;
            }
        }

        if (count($groups) == 0) {
            return false;
        }

        if (count($outdoorActivities) == 0) {
            return false;
        }

        return true;
    }
}
