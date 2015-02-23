<?php
namespace Viion\Lodestone;

class Achievements
{
    public $total = 0;
    public $current = 0;
    public $legacy;
    public $recent;
    public $kinds;
    public $kindsTotal;
    public $list;

    /**
     * - dump
     * Dump all the data in this class
     */
    public function dump($asJson = false)
    {
        $data = get_object_vars($this);

        if ($asJson)
        {
            $data = json_encode($data);
        }

        return $data;
    }

    /**
     * - clean
     * Cleans some of the character Data
     */
    public function clean()
    {

    }
}