<?php
namespace Viion\Lodestone;

class Character
{
    use Funky;

    public $id;
    public $name;
    public $world;
    public $title;
    public $avatar;
    public $bio;
    public $race;
    public $clan;
    public $gender;
    public $nameday;

    public $guardian;
    public $guardianIcon;
    public $city;
    public $cityIcon;
    public $grandCompany;
    public $grandCompanyRank;
    public $grandCompanyIcon;
    public $freeCompany;
    public $freeCompanyIcon;

    public $classjobs;
    public $gear;
    public $attributes;

    public $minions;
    public $mounts;

    /**
     * - dump
     * Dump all the data in this class
     */
    public function dump($asJson = false)
    {
        $data = get_object_vars($this);

        if ($asJson)
        {
            $data = json_encode($data);
        }

        return $data;
    }

    /**
     * - clean
     * Cleans some of the character Data
     */
    public function clean()
    {
        // Trim stuff
        foreach(get_object_vars($this) as $param => $value)
        {
            // Trim (if string)
            if (is_string($value))
            {
                $value = trim($value);
            }

            // Fix some stuff
            switch($param)
            {
                case 'id':
                    $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;

                case 'world':
                    $value = str_ireplace(['(', ')'], null, $value);
                    break;

                case 'avatar':
                    $value = str_ireplace('50x50', '96x96', $value);
                    break;

                case 'gender':
                    $value = $this->uniord($value);
                    $value = ($value == 9792) ? 'female' : 'male';
                    break;

                case 'freeCompanyIcon':
                    foreach($value as $i => $v)
                    {
                        $value[$i] = str_ireplace('40x40', '128x128', $v);
                    }
                    break;
            }

            // Reset
            $this->$param = $value;
        }
    }
}