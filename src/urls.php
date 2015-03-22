<?php
namespace Viion\Lodestone;

trait Urls
{
    public function urls()
    {
        return [
            'xivdb' => 'http://45.56.73.127/api/',
            'base' => 'http://eu.finalfantasyxiv.com/lodestone/',
            'characterSearch' => 'character/?q={name}&worldname={world}',
            'characterProfile' => 'character/{id}',
            'achievements' => 'character/{id}/achievement/',
            'achievementsKind' => 'character/{id}/achievement/kind/{kind}/',
            'sixthAstral' => 'character/{id}/sixth_astral_era/',
            'worldstatus'	=> 'worldstatus/',
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
			'status' => 'news/category/4'
        ];
    }

    public function urlGen($type, $data)
    {
        return $this->urls()['base'] . str_ireplace(array_keys($data), $data, $this->urls()[$type]);
    }
}