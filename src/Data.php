<?php
namespace Viion\Lodestone;

trait Data
{
    public function getClassList()
    {
        return [
            'gladiator', 'pugilist', 'marauder', 'lancer', 'archer', 'rogue',
            'conjurer', 'thaumaturge', 'arcanist',
            'carpenter', 'blacksmith', 'armorer', 'goldsmith', 'leatherworker', 'weaver', 'alchemist', 'culinarian',
            'miner', 'botanist', 'fisher',
        ];
    }
}