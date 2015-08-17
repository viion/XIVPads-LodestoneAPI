<?php
namespace Viion\Lodestone;

class PlayGuideItems
{
    use Urls;

    protected $Logger;

    public function find($name)
    {
        $this->Logger = Logger::get();

        // get play guide url
        $this->Logger->add('Finding item: %s', [$name]);
        $url = $this->generateUrl('playguide_item', [
            'category' => 2,
            'string' => $name,
        ]);

        // create parser
        $parser = new Parser($url);

        $table = $parser->findElement('table')->withId('character');

        show($table);

    }
}