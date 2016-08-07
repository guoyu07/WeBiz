<?php

namespace Common\Apis;

use Common\Libs\Http;

class AddressApi
{
    protected $ak;

    public function __construct($ak)
    {
        $this->ak = $ak;
    }

    //根据腾讯经纬度获取地址
    public function getAddress($lat, $lng)
    {
        $url = 'http://apis.map.qq.com/ws/geocoder/v1/?location=';
        $url .= $lat . ',' . $lng . '&key=' . $this->ak . '&get_poi=1';
        $output = json_decode(Http::request($url), true);
        if ($output['status'] === 0) {
            $city = $output['result']['address_component']['city'];
            $city = str_replace('市', '', $city);
            $city = str_replace('特别行政区', '', $city);
            $result = array(
                'city' => $city,
                'address' => $output['result']['address'],
                'addrec' => $output['result']['formatted_addresses']['recommend']
            );
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * @desc 根据两点间的经纬度计算距离
     * @param float $lat 纬度值
     * @param float $lng 经度值
     */
    public function getDistance($lat1, $lng1, $lat2, $lng2, $mode = 'driving')
    {
        $url = 'http://apis.map.qq.com/ws/distance/v1/?moding=' . $mode;
        $url .= '&from=' . $lat1 . ',' . $lng1;
        $url .= '&to=' . $lat2 . ',' . $lng2;
        $url .= '&key=' . $this->ak;
        $result = json_decode(Http::request($url), true);
        if ($result['status'] == 0) {
            $result = $result['result']['elements'][0]['distance'];
        } else {
            $result = false;
        }
        return $result;
    }

    //腾讯地图经纬度转化为百度地图经纬度
    /*
     * 中国正常GCJ02坐标---->百度地图BD09坐标
     * 腾讯地图用的也是GCJ02坐标
     * @param double $lat 纬度
     * @param double $lng 经度
     */
    public static function convertT2B($lat, $lng)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng;
        $y = $lat;
        $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta) + 0.0065;
        $lat = $z * sin($theta) + 0.006;
        return array('lng' => $lng, 'lat' => $lat);
    }

    /*
     * 百度地图BD09坐标---->中国正常GCJ02坐标
     * 腾讯地图用的也是GCJ02坐标
     * @param double $lat 纬度
     * @param double $lng 经度
     * @return array();
     */
    public static function convertB2T($lat, $lng)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta);
        $lat = $z * sin($theta);
        return array('lng' => $lng, 'lat' => $lat);
    }
}