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

    public function getClassListFull()
    {
        return [
            1 => 'gladiator',
            2 => 'pugilist',
            3 => 'marauder',
            4 => 'lancer',
            5 => 'archer',
            6 => 'conjurer',
            7 => 'thaumaturge',
            8 => 'carpenter',
            9 => 'blacksmith',
            10 => 'armorer',
            11 => 'goldsmith',
            12 => 'leatherworker',
            13 => 'weaver',
            14 => 'alchemist',
            15 => 'culinarian',
            16 => 'miner',
            17 => 'botanist',
            18 => 'fisher',
            19 => 'paladin',
            20 => 'monk',
            21 => 'warrior',
            22 => 'dragoon',
            23 => 'bard',
            24 => 'white mage',
            25 => 'black mage',
            26 => 'arcanist',
            27 => 'summoner',
            28 => 'scholar',
            29 => 'rogue',
            30 => 'ninja',
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

    public function getExperiencePoints()
    {
        return [
            0 => 0,
            1 => 300,
            2 => 600,
            3 => 1100,
            4 => 1700,
            5 => 2300,
            6 => 4200,
            7 => 6000,
            8 => 7350,
            9 => 9930,
            10 => 11800,
            11 => 15600,
            12 => 19600,
            13 => 23700,
            14 => 26400,
            15 => 30500,
            16 => 35400,
            17 => 40500,
            18 => 45700,
            19 => 51000,
            20 => 56600,
            21 => 63900,
            22 => 71400,
            23 => 79100,
            24 => 87100,
            25 => 95200,
            26 => 109800,
            27 => 124800,
            28 => 140200,
            29 => 155900,
            30 => 162500,
            31 => 175900,
            32 => 189600,
            33 => 203500,
            34 => 217900,
            35 => 232320,
            36 => 249900,
            37 => 267800,
            38 => 286200,
            39 => 304900,
            40 => 324000,
            41 => 340200,
            42 => 356800,
            43 => 373700,
            44 => 390800,
            45 => 408200,
            46 => 437600,
            47 => 467500,
            48 => 498000,
            49 => 529000,
            50 => 0
        ];
    }
}