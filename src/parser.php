<?php
namespace Viion\Lodestone;

class Parser
{
    public $html = null;
    public $found = null;

    /**
     * __construct
     *
     * Sets up the html
     */
    function __construct($html)
    {
        if (is_array($html)) {
            $html = implode("\n", $html);
            $html = html_entity_decode($html);
        }

        $html = htmlentities($html);
        $html = explode("\n", $html);

        foreach($html as $i => $h) {
            $h = preg_replace('/\s+/', ' ', $h);
            $h = trim($h);

            // skip some stuff
            if (
                substr(html_entity_decode($h), 0, 2) == '</' ||
                substr(html_entity_decode($h), 0, 4) == '<!--'
            ) {
                unset($html[$i]);
                continue;
            }

            $html[$i] = trim($h);
        }

        $html = array_values(array_filter($html));

        $this->html = $html;
        unset($html);
    }

    public function get()
    {
        return $this->html;
    }

    public function show()
    {
        echo '<pre>'. print_r($this->html, true) .'</pre>';
    }

    /**
     * - find
     * find some stuff in the htmls!
     *
     * @param $string - the string to look for.
     * @param $offset (default: 0) - the offset from the string to move down to
     * @return Parser $this;
     */
    public function find($string, $offset = 0)
    {
        $this->found = null;
        $found = null;

        foreach($this->html as $i =>$code)
        {
            if (strpos(html_entity_decode($code), $string)) {

                // if offset is string, treat a bit differently
                if (is_string($offset)) {
                    $temp = array_slice($this->html, $i);

                    // loop through temp to find offset
                    foreach ($temp as $j => $c) {
                        if (strpos(html_entity_decode($c), $offset) !== false) {
                            // replace offset with new index
                            break;
                        }
                    }

                    // replace on $j
                    $found = array_slice($this->html, $i, ($j + 1));
                    break;
                }

                $found = $i + $offset;
                break;
            }
        }

        if ($found !== null) {
            $this->found = $this->html[$found];
        }

        return $this;
    }

    /**
     * - findAll
     * Similar to find, but loops to find all and returns an array
     *
     * @param $startAt - the class of where to start
     * @param $stopAt - the class of where to stop
     * @param $string - the string to look for.
     * @param $offset (default: 0) - the offset from the string to move down to, if string, it goes down to string
     * @return Array;
     */
    public function findAll($string, $offset = 0, $startAt = null, $stopAt = null)
    {
        $found = [];
        $startAtMet = false;

        foreach($this->html as $i => $code) {
            // Check if its time to stop
            if ($stopAt && $startAtMet && strpos(html_entity_decode($code), $stopAt) !== false) {
                break;
            }

            // if start at is set, check if its been met
            if ($startAt && !$startAtMet) {
                // if start at found, start at is met, and continue
                if (strpos($code, $startAt) !== false) {
                    $startAtMet = true;
                }

                continue;
            }

            // Append to found
            if (strpos($code, $string)) {
                // if offset is string, treat a bit differently
                if (is_string($offset)) {
                    $temp = array_slice($this->html, $i);

                    // loop through temp to find offset
                    foreach ($temp as $j => $c) {
                        if (strpos(html_entity_decode($c), $offset) !== false) {
                            // replace offset with new index
                            break;
                        }
                    }

                    // replace on $j
                    $found[] = array_slice($this->html, $i, ($j + 1));

                    continue;
                }

                // Slice!
                $found[] = array_slice($this->html, $i, $offset);
            }
        }

        return $found;
    }

    public function html()
    {
        return $this->found;
    }

    public function text()
    {
        $text = trim($this->found);
        $text = htmlspecialchars_decode($text, ENT_QUOTES);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = trim($text);
        return $text;
    }

    public function numbers()
    {
        $numbers = trim(filter_var($this->found, FILTER_SANITIZE_NUMBER_INT));

        if (is_numeric($numbers)) {
            return $numbers;
        }

        return false;
    }

    public function attr($attribute)
    {
        $html = html_entity_decode($this->found);
        preg_match_all('/('. $attribute .')=("[^"]*")/i', $html, $result);

        if (isset($result[0][0])) {
            $result = str_ireplace([$attribute .'=', '"'], null, $result[0][0]);

            // return
            return html_entity_decode($result, ENT_QUOTES, 'UTF-8');
        }

        return null;
    }
}