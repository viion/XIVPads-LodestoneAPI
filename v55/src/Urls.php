<?php
namespace Viion\Lodestone;

trait Urls
{
    /**
     * List of urls
     *
     * @param $type - the type of url to get
     * @return String - a url from a type
     */
    private function urls($type)
    {
        $urls =
        [
            'base' => 'http://eu.finalfantasyxiv.com/lodestone/',
            'characterSearch' => 'character/?q={name}&worldname={world}',
            'characterProfile' => 'character/{id}',
            'achievements' => 'character/{id}/achievement/',
            'achievementsKind' => 'character/{id}/achievement/kind/{kind}/',
            'sixthAstral' => 'character/{id}/sixth_astral_era/',
            'worldstatus'   => 'worldstatus/',
            'freecompany' => 'freecompany/{id}',
            'freecompanyMember' => 'freecompany/{id}/member',
            'freecompanyMemberPage' => 'freecompany/{id}/member/?page={page}',
            'linkshell' => 'linkshell/{id}',
            'linkshellPage' => 'linkshell/{id}/?page={page}',
            'topics' => 'topics/',
            'topicsDetail' => 'topics/detail/{hash}',
            'newsDetail' => 'news/detail/{hash}',
            'notices' => 'news/category/1',
            'maintenance' => 'news/category/2',
            'updates' => 'news/category/3',
            'status' => 'news/category/4',
            'playguide_item' => 'playguide/db/item/?db_search_category=item&category{category}=&q={string}'
        ];

        return isset($urls[$type]) ? trim($urls[$type]) : false;
    }

    /**
     * Generate a lodestone url
     *
     * @param $type - the type of url to generate
     * @param $data - any params for the url
     * @return String - the url
     */
    protected function generateUrl($type, $data)
    {
        foreach($data as $i => $value) {
            unset($data[$i]);
            $data[sprintf('{%s}', $i)] = $value;
        }

        $path = str_ireplace(array_keys($data), $data, $this->urls($type));

        return $this->urls('base') . $path;
    }
}