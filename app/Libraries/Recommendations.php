<?php

namespace App\Libraries;

use App\AdditionalProfile;
use App\User;
use App\UserGroup;
use App\OutdoorActivity;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Libraries\AirQualityIndex;




/**
 * Class Recommendations
 * Handles communication with CERTH personalized recommendations API
 * @link http://mriga.iti.gr:8080/hackAIR_project/api
 * @package App\Libraries
 */
class Recommendations
{

    /**
     * Validation rules
     * @var array
     */
    const RULES = [
        'username'              => 'required|string',
        'age'                   => 'required|integer',
        'locationCity'          => 'required|string',
        'locationCountry'       => 'required|string',
        'isPregnant'            => 'boolean',
        'isOutdoorJobUser'      => 'boolean',
        'airPollutant'                      => 'required',
        'airPollutant.airPollutantName'     => 'required',
        'airPollutant.airPollutantValue'    => 'required',
    ];

    /**
     * Returns recommendations for the selected user.
     *
     * @param $user User
     * @return array
     */
    public static function get($user,$dateStart = null,$dateEnd = null,$lang = 'en',$city = null,$lat=null,$lon=null)
    {
        // fetch required user data and form input json

        $response = array();
        $cityCountries = array('Athens'=>'Greece','Berlin'=>'Germany','Brussels'=>'Belgium','Oslo'=>'Norway','Thessaloniki'=>'Greece','London'=>'UK');
        $aqi = array();


        if (isset($lon) == true && isset($lat) == true && isset($dateStart) == true) {
            $aqi = AirQualityIndex::getAQI($lon, $lat, $dateStart,$dateEnd);
        }
        $age = empty($user->year_of_birth) == false ? date('Y') - $user->year_of_birth : null;
        //$aqi[0]['AQI_Value'] = 1;

        if(isset($aqi[0]['AQI_Value'])) {

            $data = [
                'username' => $user->username,
                'gender' => $user->gender,
                'age' => $age,
                'preferredLanguageCode'=> $lang,
                'locationCity' => $city,
                'locationCountry' => $cityCountries[$city],
                'isPregnant' => $user->isPregnant(),
                'isOutdoorJobUser' => $user->isOutdoorJobUser(),
                'isSensitiveTo' => $user->isSensitiveTo(),
                'preferredActivities' => [
                    "preferredOutdoorActivities" => $user->getOutdoorActivities()
                ],
                'airPollutant' => [
                    "airPollutantName" => "PM_fused",
                    "airPollutantValue" => $aqi[0]['AQI_Value'],
                ]
            ];

            if(empty($user->gender))
                unset($data['gender']);

            $additionalProfile = AdditionalProfile::where('user_id',$user->id)->first();

            if($additionalProfile) {

                $outDoorActivitiesTMP = json_decode($additionalProfile->user_activities,true);
                $sensitivitiesTMP     = json_decode($additionalProfile->user_groups,true);
                $pregrantGroupId = UserGroup::where('name', 'Pregnancy')->value('id');

                foreach($outDoorActivitiesTMP as $v) {
                    $outDoorActivitiesIds[]=strtolower($v['id']);
                }

                foreach($sensitivitiesTMP as $v) {
                    if ($v['id'] != $pregrantGroupId) {
                        $sensitivitiesIds[]=strtolower($v['id']);
                    }
                }

                $outDoorActivities = array_map('strtolower',OutdoorActivity::wherein('id', $outDoorActivitiesIds)->pluck('name')->toArray());
                $sensitivities = array_map('strtolower',UserGroup::wherein('id', $sensitivitiesIds)->pluck('name')->toArray());
                $sensitivitiesToRecommendationsMap = array(
                    'cardiovascular diseases' => 'Cardiovascular',
                    'respiratory diseases' => 'GeneralHealthProblem'
                );
                foreach($sensitivities as $key => &$val) {
                    $sensitivities[$key] = $sensitivitiesToRecommendationsMap[$val];
                }
                $age = empty($additionalProfile->year_of_birth) == false ? date('Y') - $additionalProfile->year_of_birth : null;

                $data['relatedProfiles'][] = [
                    'username' => $user->username.'_secondary',
                    'gender' => (empty($additionalProfile->gender)) ? 'other' : $additionalProfile->gender,
                    'age' => $age,
                    'preferredLanguageCode'=> $lang,
                    'locationCity' => $city,
                    'locationCountry' => $cityCountries[$city],
                    'isSensitiveTo' => $sensitivities,
                    'preferredActivities' => [
                        "preferredOutdoorActivities" => $outDoorActivities
                    ],
                    'airPollutant' => [
                        "airPollutantName" => "PM_fused",
                        "airPollutantValue" => $aqi[0]['AQI_Value'],
                    ]
                ];

            }

            $validator = Validator::make($data, self::RULES);

            if ($validator->fails()) {
                Log::error('Validation error while generating user recommendations. user_id: ' . $user->id);
                return [];
            } else {

                // call CERTH API
                try {
                    $client = new Client();
                    $url = env('CERTH_API_URL') . "/requestRecommendation";
                    $res = $client->post($url, ['json' => $data]);
                    $res_new = str_replace('outdoor job','work',$res->getBody());
                    $res_new = str_replace('general personalised','park',$res_new);

                    $response = json_decode($res_new);


                } catch (Exception $e) {
                    $message = 'Error while contacting CERTH Recommendations API: ' . $e->getMessage();
                    Log::error($message);
                    return null;
                }

            }

        }

        return $response;
    }
}
