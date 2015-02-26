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

    public function getTwoHandedItems()
    {
        /**
         * In the game, the Item Level for 2 handed equipment (or where you cannot
         * equip anything in the offhand slot) is doubled to balance the overall
         * item level with other classes that can have an offhand. The equipment
         * type below all have double item level.
         *
         * Items in this array are added twice to the Average
         */
        return [
            "Pugilist's Arm",
            "Marauder's Arm",
            "Archer's Arm",
            "Lancer's Arm",
            "Rogue's Arms",
            "Two-handed Thaumaturge's Arm",
            "Two-handed Conjurer's Arm",
            "Arcanist's Grimoire",
            "Fisher's Primary Tool",
        ];
    }
}