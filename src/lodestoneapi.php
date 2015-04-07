<?php
namespace Viion\Lodestone;

/*  --------------------------------------------------
 *  XIVPads.com (v5) - Lodestone API (PHP)
 *  --------------------------------------------------
 */
class LodestoneAPI
{
    use Data;

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