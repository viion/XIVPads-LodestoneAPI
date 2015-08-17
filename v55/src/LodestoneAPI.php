<?php
namespace Viion\Lodestone;

// -----------------------------------------------------------
// XIVPads.com (v5.5) - Lodestone API (PHP)
// ------------------------------------------------------------

class LodestoneAPI
{
    protected $Logger;
    public $Search;
    public $PlayGuide;

    function __construct()
    {
        $this->Logger = Logger::get();
        $this->Logger->add('Lodestone API ready');

        $this->Search = new Search();
        $this->PlayGuide = new PlayGuide();
    }

    /**
     * Show logging
     * Calls: Logger->show()
     */
    public function showLog()
    {
        $this->Logger->show();
    }
}