<?php
namespace Viion\Lodestone;

trait Urls
{
    public function urls()
    {
        return [
            'base' => 'http://eu.finalfantasyxiv.com/lodestone/',
            'characterSearch' => 'character/?q={name}&worldname={world}',
            'characterProfile' => 'character/{id}',
        ];
    }

    public function urlGen($type, $data)
    {
        return $this->urls()['base'] . str_ireplace(array_keys($data), $data, $this->urls()[$type]);
    }
}