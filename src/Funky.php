<?php
namespace Viion\Lodestone;

trait Funky
{
    public function curl($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER  => true,            // return web page
            CURLOPT_HEADER          => false,           // return headers
            CURLOPT_FOLLOWLOCATION  => false,           // follow redirects
            CURLOPT_ENCODING        => "",              // handle all encodings
            CURLOPT_AUTOREFERER     => true,            // set referer on redirect
            CURLOPT_CONNECTTIMEOUT  => 15,              // timeout on connects
            CURLOPT_TIMEOUT         => 15,              // timeout on response
            CURLOPT_MAXREDIRS       => 5,               // stop after 10 redirects
            CURLOPT_USERAGENT       => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36",
            CURLOPT_HTTPHEADER      => array('Content-type: text/html; charset=utf-8', 'Accept-Language: en'),
        );
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/html; charset=utf-8'));
        $source = curl_exec($ch);
        curl_close($ch);
        $html = htmlentities($source);

        return $html;
    }


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