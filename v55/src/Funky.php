<?php
namespace Viion\Lodestone;

trait Funky
{
    public function getWebPage($url)
    {
        $settings = new Settings();

        $parseMethod = $settings->get('parse_method');
        $this->Logger->add('Parse method: %s', [$parseMethod]);

        switch($parseMethod)
        {
            case 1: return $this->getWebPageCurl($url);
            case 2: return $this->getWebPageContents($url);
        }
    }

    /**
     * Gets a web page using the file_get_content functions
     *
     * @param $url;
     * @return false or html
     */
    private function getWebPageContents($url)
    {
        $this->Logger->add('Calling file_get_contents on url ...');
        $html = file_get_contents($url);

        return $html;
    }

    /**
     * Gets a web page using the curl functions
     *
     * @param $url;
     * @return false or html
     */
    private function getWebPageCurl($url)
    {
        $this->Logger->add('Calling curl_exec on url ...');
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_POST, false);
        curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 5);
        curl_setopt($handle, CURLOPT_MAXREDIRS, 2);
        curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-type: text/html; charset=utf-8', 'Accept-Language: en']);
        curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.0.0 Safari/537.36');
        curl_setopt($handle, CURLOPT_ENCODING, '');

        $response = curl_exec($handle);
        $hlength  = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $html     = substr($response, $hlength);

        curl_close($handle);
        unset($handle);

        // not found or no response
        if ($httpCode == 404 || $httpCode == 204) {
            return false;
        }

        return $html;
    }

    /**
     * Handles weird SE characters ...
     */
    public function uniord($u)
    {
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));
        return $k2 * 256 + $k1;
    }

    /**
     * Format html between two points, trims lines and removes blank entries
     *
     * @param $html
     * @param $start
     * @param $finish
     * @return String - new html array
     */
    protected function truncateHtml($html, $start, $finish)
    {
        if (!is_array($html)) {
            $html = explode("\n", $html);
        }

        $temp = [];
        $foundStart = false;
        foreach($html as $i => $line)
        {
            // trim
            $line = trim($line);

            // skip if empty
            if (empty($line)) {
                continue;
            }

            // set found start
            if (stripos($line, $start) !== false) {
                $foundStart = true;
            }

            // if found start, start adding lines
            if ($foundStart) {
                if (stripos($line, '<!--') !== false) {
                    continue;
                }

                $temp[] = $line;
            }

            // if found end, end.
            if (stripos($line, $finish) !== false) {
                break;
            }
        }

        return implode("\n", $temp);
    }
}