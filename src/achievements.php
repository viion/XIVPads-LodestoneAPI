<?php
namespace Viion\Lodestone;

class Achievements
{
    public $pointsTotal = 0;
    public $pointsCurrent = 0;
    public $countTotal = 0;
    public $countCurrent = 0;

    public $public = false;
    public $legacy;
    public $legacyPoints = 0;
    public $legacyPointsTotal = 0;
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
        if ($this->pointsCurrent) {
            $this->public = true;
        }

        // Sort achievements by ID.
        if (isset($this->list) && $this->list)
        {
            ksort($this->list);
            ksort($this->kinds);
            ksort($this->kindsTotal);
        }

        // Set hash
        $this->hash = sha1($this->dump(true));
    }
}