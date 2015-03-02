<?php
namespace Viion\Lodestone;

/*  --------------------------------------------------
 *  XIVPads.com (v5) - Lodestone API
 *  --------------------------------------------------
 */
class LodestoneAPI
{
    /**
     * SearchObject
     * @var Search
     */
    public $Search;

    function __construct()
    {
        // Initialize
        $this->Search = new Search();
    }

    /**
     * Quick call to Basic Parsing
     */
    public function useBasicParsing()
    {
        if ($this->Search) {
            $this->Search->useBasicParsing();
        }
    }
}