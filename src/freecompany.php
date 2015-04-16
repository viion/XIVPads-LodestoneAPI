<?php
namespace Viion\Lodestone;

class FreeCompany
{
    public $id;
    public $name;
    public $server;
    public $emblum;
    public $company;
    public $tag;
    public $formed;
    public $memberCount;
    public $slogan;
    public $active;
    public $recruitment;
    public $ranking;
    public $estate;
    public $focus;
    public $roles;
    public $members;

    /**
     * - clean
     * Cleans some of the character Data
     */
    public function clean()
    {
        // Trim stuff
        foreach(get_object_vars($this) as $param => $value)
        {
            // Trim (if string)
            if (is_string($value))
            {
                $value = trim($value);
            }

            // Fix some stuff
            switch($param)
            {
                case 'id':
                    $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'emblum':
                    if (is_array($value))
                    {
                        foreach($value as $i => $v)
                        {
                            $value[$i] = str_ireplace('64x64', '128x128', $v);
                        }
                    }
                    break;
            }

            // Reset
            $this->$param = $value;
        }

        // Set hash
        $this->hash = sha1($this->dump(true));
    }

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
}