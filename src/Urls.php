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
			'topicDetail' => 'topics/detail/{hash}'
        ];
    }

    public function urlGen($type, $data)
    {
        return $this->urls()['base'] . str_ireplace(array_keys($data), $data, $this->urls()[$type]);
    }
}