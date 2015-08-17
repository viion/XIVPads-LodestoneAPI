<?php
namespace Viion\Lodestone;

class Settings
{
    private $options;

    function __construct()
    {
        $file = __DIR__.'/../settings.yml';
        if (!file_exists($file)) {
            die('The settings.yml file is missing, please include it.');
        }

        // get settings
        $settings = file_get_contents($file);
        $settings = explode("\n", $settings);

        // remove comments
        foreach($settings as $i => $line) {
            if (empty($line) || $line[0] == '#') {
                unset($settings[$i]);
                continue;
            }

            $values = explode(':', $line);
            $key = trim($values[0]);
            $val = trim($values[1]);

            $settings[$key] = $val;
            unset($settings[$i]);
        }

        $this->options = $settings;
    }

    /**
     * Get the value of an option
     *
     * @param $option
     * @return String - the options value
     */
    public function get($option)
    {
        if (!isset($this->options[$option])) {
            die(sprintf('The option: %s does not exist in the settings.yml file.', $option));
        }

        return $this->options[$option];
    }
}