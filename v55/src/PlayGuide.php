<?php
namespace Viion\Lodestone;

class PlayGuide
{
    public $Items;

    function __construct()
    {
        $this->Items = new PlayGuideItems();
    }
}