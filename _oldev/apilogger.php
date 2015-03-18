<?php
// Namespace
namespace Viion\Lodestone;

/*  class 'logger'
 *  - Logs things!
 */
class APILogger
{
    // public
    public $start;
    public $finish;
    public $duration;

    // private
    private $previous = false;
    private $messages = [];

    // init
    function __construct() { }

    // Get the time now
    public function now()
    {
        $time = round(microtime(true) * 1000);
        return $time;
    }

    // calculate: will set start, finish and duration based on log
    public function calculate()
    {
        // Sort it
        ksort($this->messages);

        // Get start
        reset($this->messages);
        $this->start = str_ireplace('-', null, key($this->messages));

        // Get finish
        end($this->messages);
        $this->finish = str_ireplace('-', null, key($this->messages));

        // Get duration
        $this->duration = $this->finish - $this->start;

        // Format it to be more readable
        if ($this->duration > 0)
        {
            $this->duration = round(($this->duration / 1000), 5);
        }
    }

    // show results
    public function show()
    {
        // If no messages, return right away
        if (empty($this->messages))
        {
            return false;
        }

        // Calculate times
        $this->calculate();

        // ksort
        ksort($this->messages);
        show($this->messages);
    }

    // get results
    public function get()
    {
        // If no messages, return right away
        if (empty($this->messages))
        {
            return false;
        }

        // Calculate times
        $this->calculate();

        // ksort
        ksort($this->messages);
        return $this->messages;
    }

    public function log($text)
    {
        // Get the delay from the previous entry
        $delay = '-';
        if ($this->previous !== false)
        {
            $delay = ($this->now() - $this->previous);
        }

        // Set the previous time to now
        $this->previous = $this->now();

        // Style delay
        $delay = $this->styleDelay($delay);

        // Log it
        $timeformat = substr_replace($this->now(), '-', -4, 0);
        $this->messages[$timeformat][] = '('. $delay .' ms) '. $text;
    }

    private function styleDelay($delay)
    {
        $html = '{{delay}}';

        if ($delay > 2000) { $html = '<span style="background-color:#f00;color:#fff;font-weight:bold;padding:0 8px;border-radius:3px;">{{delay}}</span>'; }
        else if ($delay > 500) { $html = '<span style="background-color:#f00;color:#fff;font-weight:bold;border-radius:3px;">{{delay}}</span>'; }
        else if ($delay > 250) { $html = '<span style="background-color:#AC7206;color:#fff;font-weight:bold;border-radius:3px;">{{delay}}</span>'; }
        else if ($delay > 100) { $html = '<span style="background-color:#8EC018;color:#fff;font-weight:bold;border-radius:3px;">{{delay}}</span>'; }
        else if ($delay < 10) { $html = '<span style="color:#5CA21E;font-weight:bold;">{{delay}}</span>'; }

        $html = str_ireplace('{{delay}}', $delay, $html);

        return $html;
    }
}

// Alias for APILogger()
class_alias('Viion\Lodestone\APILogger', 'API\Logger');
class_alias('Viion\Lodestone\APILogger', 'LodestoneLogger');
class_alias('Viion\Lodestone\APILogger', '_Shared\Lodestone\APILogger');