<?php
namespace Viion\Lodestone;

/*  --------------------------------------------------
 *  XIVPads.com (v5) - Lodestone API
 *  --------------------------------------------------
 */
class LodestoneAPI
{
    public $Search;

    function __construct()
    {
        // Initialize
        $this->Search = new Search();
    }



}