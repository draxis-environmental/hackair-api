<?php

namespace App\Libraries;

class MongoHelper {

    const SECONDS_IN_A_MILLISECOND = 1000;

    public static function getMongoUTCDateTimeFromUnixTimestamp($timestamp) {
        return new \MongoDB\BSON\UTCDateTime(intval($timestamp * self::SECONDS_IN_A_MILLISECOND));
    }

    public static function getUnixTimestampFromMongoUTCDateTime(\MongoDB\BSON\UTCDateTime $utc_date_time) {
        $datetime = (string) $utc_date_time;
        return intval($datetime) / self::SECONDS_IN_A_MILLISECOND;
    }
}
