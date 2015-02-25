<?php
namespace Viion\Lodestone;

trait Funky
{
    public function uniord($u)
    {
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));
        return $k2 * 256 + $k1;
    }

    public function extractTime($time)
    {
        if (!$time || empty($time)) {
            return false;
        }

        $time = explode('=', $time);
        $time = $time[1];
        $time = filter_var($time, FILTER_SANITIZE_NUMBER_INT);
        return $time;
    }
}