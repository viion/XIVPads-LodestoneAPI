<?php
namespace Viion\Lodestone;

class Parser
{
    use Funky;

    protected $Logger;
    private $html;
    private $instance;

    function __construct($url)
    {
        libxml_use_internal_errors(true);

        $this->Logger = Logger::get();

        // get html and decode it
        $this->Logger->add('Getting the html for: %s', [$url]);
        $this->html = $this->getWebPage($url);
        $this->Logger->add('Successfulyl got html for: %s', [$url]);

        // truncate the html
        $this->Logger->add('Truncate HTML');

        // trim the html
        $this->html = $this->truncateHtml($this->html, '<!-- contents -->', '<!-- //#contetnts-->');
        $this->html = explode("\n", $this->html);
    }

    function findElement($name)
}