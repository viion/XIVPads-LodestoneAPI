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

    public function getAchievementKinds()
    {
        return [
            1 => 'battle',
            2 => 'character',
            4 => 'item',
            5 => 'synthesis',
            6 => 'gathering',
            8 => 'quests',
            11 => 'exploration',
            12 => 'grand company',
            13 => 'legacy',
        ];
    }
}