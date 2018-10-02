<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Measurement;
use App\Libraries\AirQualityIndex;
use App\Libraries\Recommendations;
use App\Libraries\MandrillMailer;
use \Illuminate\Support\Facades\Lang;

class NewsletterController extends Controller
{
    /**
     * Prepare newsletter
     *
     * @param App\User $user The user that the newsletter will be sent to
     * @param string $subject The email's subject
     *
     * @return string $html The newsletter's content
     */
    public function prepare($user, $subject)
    {
        if (empty($user['location']) == true) {
            return null;
        }

        $coordinates = $user['location']['coordinates'];
        $aqi_result = AirQualityIndex::getAQI($coordinates[0], $coordinates[1], date('Y-m-d'));

        if (empty($aqi_result) == true) {
            return null;
        }

        $aqi = $aqi_result[0];
        $aq_icon_name = $aqi['AQI_Index'] == 'perfect' ? 'excellent' : $aqi['AQI_Index'];
        $aq_icon = env('WEB_URL') . '/img/airquality/' . $aq_icon_name . '.png';
        $aq_scale = Lang::get('aqi.' . $aqi['AQI_Index'] . '_caps');

        $recommendations = Recommendations::get();
        $recommendations_msg = array_rand($recommendations);
        $recommendations_more_url = env('WEB_URL');

        switch($aqi['AQI_Index']) {
            case 'perfect':
            case 'good':
                $recommendations_msg = $recommendations_msg ?: Lang::get('newsletter.recommendation_perfect');
                $recommendations_msg_2 = Lang::get('newsletter.more_recommended_activities');
                $recommendations_font_color = "#00a3ac";
                $recommendations_bg_color = "194, 233, 234, 0.4";
                // TODO change depending on recommendation
                $recommendations_icon = env('WEB_URL') . '/img/airquality/Icon_Recommendation_Cycling@4x.png';
                break;
            case 'medium':
            case 'bad':
                $recommendations_msg = $recommendations_msg ?: Lang::get('newsletter.recommendation_bad');
                $recommendations_msg_2 = Lang::get('newsletter.check_app_for_updates');
                $recommendations_font_color = "#db3a3a";
                $recommendations_bg_color = "244, 204, 196, 0.4";
                // TODO change depending on recommendation
                $recommendations_icon = env('WEB_URL') . '/img/icons/icon_alert@4x.png';
                break;
        }

        // Latest photos in your area
        // Find measurements within 5km radius
        // $radius = 5 / 6378.1;
        $radius = 50000 / 6378.1;
        $matchFields = [
            'loc' => [
                '$geoWithin' => [
                    '$centerSphere' => [
                        $user['location']['coordinates'],
                        $radius
                    ]
                ]
            ],
            'source_type' => [
                '$in' => ['flickr', 'webcams', 'mobile']
            ]
        ];
        $groupFields = [];
        $projectFields = [];
        $sortFields = ['datetime' => -1];
        $skip = 0;
        $limit = 3;

        $measurements = Measurement::getRawMeasurements($matchFields, $groupFields, $projectFields, $sortFields, $skip, $limit);

        $image_urls = [];
        foreach($measurements as $measurement) {
            $image_url = array_key_exists('thumb_image_url', $measurement['source_info']) == true ? $measurement['source_info']['thumb_image_url'] : $measurement['source_info']['image_url'];
            $image_urls[] = env('API_URL') . '/misc/thumb?url=' . $image_url . '&width=160&height=120';
        }

        $view_token = md5($user->id . '_' . date('Y-m-d'));
        $view_url = env('API_URL') . '/misc/newsletter/view/' . $user->id . '/' . date('Y-m-d') . '/' . $view_token;

        $unsubscribe_url = env('WEB_URL') . '/unsubscribe/' . $user->unsubscribe_token;
        $template_vars = [
            'subject' => $subject,
            'location_name'=> $user->location_str,
            'current_date' => date('D, j F Y'),
            'first_name' => $user->name,
            'aq_img' => $aq_icon,
            'aq_scale' => $aq_scale,
            'recommendations_msg' => $recommendations_msg,
            'recommendations_msg_2' => $recommendations_msg_2,
            'recommendations_icon' => $recommendations_icon,
            'recommendations_font_color' => $recommendations_font_color,
            'recommendations_bg_color' => $recommendations_bg_color,
            'recommendations_more_url' => $recommendations_more_url,
            'image_urls' => $image_urls,
            'view_url' => $view_url,
            'unsubscribe_url' => $unsubscribe_url
        ];

        $html = view('emails.newsletter', $template_vars)->render();

        return $html;
    }

    /**
     * Send newsletter
     *
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $users = User::where('notify_email', true)->get();

        foreach($users as &$user) {

//            $templateName = 'hackair-aqinfo';
            $templateName = '';
            $templateContent = [];
            $subject = Lang::get('newsletter.subject') . ' | hackAIR';
            $message = [
                'subject' => $subject,
                'to' => array(
                    array(
                        'email' => $user->email,
                        'name' => $user->name . ' ' . $user->surname,
                        'type' => 'to'
                    )
                )
            ];

            $message['html'] = $this->prepare($user, $subject);
            
            // echo  $message['html'];
            // exit;

            if (empty($message['html']) == false) {
                MandrillMailer::send($templateName, $templateContent, $message);
            }
        }
    }

    /**
     * Test newsletter
     *
     * @param string $user_id The user id that the newsletter will be sent to
     *
     * @return \Illuminate\Http\Response
     */
    public function test($user_id)
    {
        $user = User::where('id', $user_id)->first();

        $templateName = '';
        $templateContent = [];
        $subject = Lang::get('newsletter.subject') . ' | hackAIR';
        $message = [
            'subject' => $subject,
            'to' => array(
                array(
                    'email' => $user->email,
                    'name' => $user->name . ' ' . $user->surname,
                    'type' => 'to'
                )
            )
        ];

        $message['html'] = $this->prepare($user, $subject);

        if (empty($message['html']) == false) {
            MandrillMailer::send($templateName, $templateContent, $message);
        }
    }

    /**
     * View newsletter
     *
     * @param string $user_id The user id that the newsletter will be sent to
     *
     * @return \Illuminate\Http\Response
     */
    public function view($user_id, $date, $token)
    {
        $user = User::where('id', $user_id)->first();
        $date = $date ?: date('Y-m-d');
        $newsletter_token = md5($user_id . '_' . $date);

        if ($newsletter_token != $token) {
            echo Lang::get('newsletter.no_view');
        }
        
        $subject = Lang::get('newsletter.subject') . ' | hackAIR';
        echo $this->prepare($user, $subject);
        exit;
    }
}
