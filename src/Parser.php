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
        $found = null;

        foreach($this->html as $i =>$code)
        {

            if (strpos($code, $string)) {
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
     * @param $string - the string to look for.
     * @param $offset (default: 0) - the offset from the string to move down to
     * @return Array;
     */
    public function findAll($string, $offset = 0, $startAt = null)
    {
        $found = [];
        $startAtMet = false;

        foreach($this->html as $i => $code)
        {
            // if start at is set, check if its been met
            if ($startAt && !$startAtMet)
            {
                // if start at found, start at is met, and continue
                if (strpos($code, $startAt) !== false) {
                    $startAtMet = true;
                }

                continue;
            }

            // Append to found
            if (strpos($code, $string))
            {
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
        return trim(strip_tags(htmlspecialchars_decode(trim($this->found), ENT_QUOTES)));
    }

    public function numbers()
    {
        return trim(filter_var($this->found, FILTER_SANITIZE_NUMBER_INT));
    }

    public function attr($attribute)
    {
        $attribute = $attribute .'=';
        $string = explode(' ', $this->found);

        // Loop through to find 'attribute',
        foreach($string as $s)
        {
            if (stripos($s, $attribute) !== false)
            {
                $string = $s;
                break;
            }
        }

        // Strip stuff
        $string = str_replace([$attribute, '&quot;'], null, $string);

        // return
        return $string;
    }
}