<?php
namespace Viion\Lodestone;

class Logger
{
    private static $instance;
    private $entries = [];

    private function __construct() {}
    private function __clone() {}

    /**
     * Ensure static logger so
     * the same logging is used for
     * all classes
     */
    public static function get()
    {
        if (!Logger::$instance instanceof self) {
             Logger::$instance = new self();
        }

        return Logger::$instance;
    }

    /**
     * Add some string to the Logger
     *
     * @param $text - the text to add
     * @param $vars - (optional) variables to replace using sprintf %s
     */
    public function add($text, $vars = [])
    {
        // get time
        $ms = explode('.', microtime(true));
        $ms = $ms[0] . '.'. str_pad($ms[1], 4, '0');
        $ms = sprintf('[%s]', $ms);

        // get memory
        $mem = sprintf('[%s]', $this->getMemoryUsage());

        // add to log
        $text = vsprintf($text, $vars);
        $this->entries[] = $ms . $mem . ' - '. $text;
    }

    /**
     * Shows the log
     *
     * echo <log>
     */
    public function show()
    {
        echo '<pre>'. print_r($this->entries, true) .'</pre>';
    }

    /**
     * Get the memory usage in human values
     *
     * @return String - memory usage
     */
    private function getMemoryUsage()
    {
        $size = memory_get_usage(true);
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2) . $unit[$i];
    }
}