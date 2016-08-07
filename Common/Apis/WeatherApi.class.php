<?php

namespace Common\Apis;

use Common\Libs\Http;
use Common\Libs\Time;

class WeatherApi
{
    protected $baidu_ak;

    public function __construct($ak)
    {
        $this->baidu_ak = $ak;
    }

    public function getWeather($city)
    {
        $url = 'http://apis.baidu.com/heweather/weather/free?city=' . $city;
        $header = array('apikey:' . $this->baidu_ak,);
        $res = json_decode(Http::request(['url' => $url, 'header' => $header]), true);
        $res = $res['HeWeather data service 3.0'][0];
        $response = array();
        for ($i = 0; $i <= 2; $i++) {
            $weather[$i] = array(
                'city_no' => $res['basic']['id'],
                'city_name' => $city,
                'weather_day' => $i,
                'aqi_aqi' => $res['aqi']['city']['aqi'],
                'aqi_pm25' => $res['aqi']['city']['pm25'],
                'aqi_qlty' => $res['aqi']['city']['qlty'],
                'weather_hightmp' => $res['daily_forecast'][$i]['tmp']['max'],
                'weather_lowtmp' => $res['daily_forecast'][$i]['tmp']['min'],
                'weather_condition' => $res['daily_forecast'][$i]['cond']['txt_d'],
                'wind_dir' => $res['daily_forecast'][$i]['wind']['dir'],
                'wind_sc' => $res['daily_forecast'][$i]['wind']['dir']
            );
            $response[$i] = $this->_assembleMessage($city, $weather[$i]);
        }
        return $response;
    }

    private function _assembleMessage($city, $weather_i)
    {
        switch ($weather_i['weather_day']) {
            case 0:
                $contentStr = '今天是' . Time::getDate() . Time::getWeek() . '，';
                $contentStr .= $city . "最高气温" . $weather_i['weather_hightmp'] . "度，最低气温" . $weather_i['weather_lowtmp'] . "度，" . $weather_i['weather_condition'] . "。";
                $contentStr .= "空气质量指数" . $weather_i['aqi_aqi'] . "。";
                break;
            case 1:
                $day = strtotime("+" . $weather_i['weather_day'] . " day");
                $contentStr = '明天是' . Time::getDate($day) . Time::getWeek() . '，';
                $contentStr .= $city . "最高气温" . $weather_i['weather_hightmp'] . "度，最低气温" . $weather_i['weather_lowtmp'] . "度，" . $weather_i['weather_condition'] . "。";
                break;
            default:
                $day = strtotime("+" . $weather_i['weather_day'] . " day");
                $contentStr = '后天是' . Time::getDate($day) . Time::getWeek() . '，';
                $contentStr .= $city . "最高气温" . $weather_i['weather_hightmp'] . "度，最低气温" . $weather_i['weather_lowtmp'] . "度，" . $weather_i['weather_condition'] . "。";
                break;
        }
        return $contentStr;
    }
}