<?php
namespace Viion\Lodestone;

trait Funky
{
    public function curl($url, $timeout = 5)
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_POST, false);
        curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($handle, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($handle, CURLOPT_MAXREDIRS, 2);
        curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-type: text/html; charset=utf-8', 'Accept-Language: en']);
        curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.0.0 Safari/537.36');
        curl_setopt($handle, CURLOPT_ENCODING, '');

        $response = curl_exec($handle);
        $hlength  = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $data     = substr($response, $hlength);

        curl_close($handle);
        unset($handle);

        // not found or no response
        if ($httpCode == 404 || $httpCode == 204) {
            return false;
        }

        return $data;
    }

    public function trim($html, $start, $end)
    {
        $temp = $html;

        // Start position
        $start  = strpos($temp, $start);

        // cut to start
        $temp   = substr($html, $start);

        // Cut to end
        $end    = strpos($temp, $end) + strlen($end);

        // sub from entire
        $html   = substr($html, $start, $end);

        return $html;
    }

	public function clearRegExpArray(&$array)
    {
		$tmp = array();
		foreach($array as $key => $value) {
			if(is_array($value)){
				$tmp[$key] = $this->clearRegExpArray($value);
			}else if(!is_numeric($key) ){
				$tmp[$key] = $value;
			}
		}
		$array = $tmp;
		unset($tmp);
		return $array;
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

    public function hashed($string)
    {
        $string = trim(strip_tags(htmlspecialchars_decode(trim($string), ENT_QUOTES)));
        $string = strtolower($string);
        $string = preg_replace('/[^\w]/', null, $string);
        $string = sha1($string);
        $string = substr($string, 0, 10);

        return $string;
    }

	public function getRegExp($type,$name=""){
		$types = array(
			'image' => '<img.+?src="(?<%1$s>[^\?"]+)(?:\?(?(?=[\d\w]+")(?<%1$sTimestamp>[\d\w^\?"]+)|(?<%1$sQueryString>[^\?"=]+=[^\?"]+?)))?".*?>'
		);
		return sprintf($types[$type],$name);
	}

}