<?php
namespace Viion\Lodestone;

class Character
{
    use Funky;
    use Data;

    public $id;
    public $name;
    public $world;
    public $title;
    public $avatar;
    public $avatarLarge;
    public $portrait;
    public $portraitLarge;
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
    public $freeCompanyId;
    public $freeCompanyIcon;

    public $classjobs = [];
    public $gear = [];
    public $gearStats = [];
    public $attributes = [];

    public $activeClass;
    public $activeJob;
    public $activeLevel;

    public $minions = [];
    public $mounts = [];

    public $hash;

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

                case 'gender':
                    $value = $this->uniord($value);
                    $value = ($value == 9792) ? 'female' : 'male';
                    break;

                case 'freeCompanyIcon':
                    if (is_array($value))
                    {
                        foreach($value as $i => $v)
                        {
                            $v = explode('?', $v)[0];
                            $value[$i] = str_ireplace('40x40', '128x128', $v);
                        }
                    }
                    break;

                // Remove timestamp from images
                case 'avatar':
                case 'avatarLarge':
                case 'portrait':
                case 'portraitLarge':
                case 'guardianIcon':
                case 'cityIcon':
                case 'grandCompanyIcon':
                    $value = explode('?', $value)[0];
                    break;
            }

            // Reset
            $this->$param = $value;
        }

        $curve = $this->getExperiencePoints();
        $jobclass = $this->getClassListFull();

        // Set max EXP
        foreach($this->classjobs as $i => $d)
        {
            // Handle classjobs
            $temp = $curve;
            $realTotal = 0;

            if ($d['level'] > 0)
            {
                $realTotal = array_sum(array_splice($temp, 0, $d['level'])) + intval($d['exp_current']);
            }

            $this->classjobs[$i]['exp_total'] = $realTotal;

            // Handle classjob id
            $this->classjobs[$i]['id'] = array_search(strtolower($d['name']), $jobclass);

            // Handle blanks
            if ($d['level'] == '-') { $this->classjobs[$i]['level'] = 0; }
            if ($d['exp_current'] == '-') { $this->classjobs[$i]['exp_current'] = 0; }
            if ($d['exp_level'] == '-') { $this->classjobs[$i]['exp_level'] = 0; }
        }

        // Sort attributes
        ksort($this->attributes);

        // Set hash
        $this->hash = sha1($this->dump(true));
    }
}