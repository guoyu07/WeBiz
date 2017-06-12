<?php

namespace Common\Libs;

class Time
{
    //获取当天的中文星期几
    public static function getWeek($time = null)
    {
        if ($time === null) $time = time();
        $week = array("日", "一", "二", "三", "四", "五", "六");
        $result = "星期" . $week[date("w", $time)];
        return $result;
    }

    //将日期转化为中文年月日
    public static function getDate($date = null, $y = false)
    {
        if ($date === null) $date = time();
        if ($y == false) $date_cn = ''; else $date_cn = date("Y", $date) . '年';
        $date_cn .= date("m", $date) . '月' . date("d", $date) . '日';
        return $date_cn;
    }

    //获取当前时间距离明天零时还有多少秒
    public static function getSecLeft($time = "00:00:00")
    {
        $diff = strtotime("Tomorrow " . $time) - time();
        return $diff;
    }
}