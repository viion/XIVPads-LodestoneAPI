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
    public $seeking;
    public $members;

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