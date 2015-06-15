<?php
namespace Viion\Lodestone;

class Character
{
    use Funky;
    use Data;
    use Urls;

    public $id;
    public $name;
    public $world;
    public $title;
    public $avatar;
    public $avatarTimestamp;
    public $avatarHash;
    public $portrait;
    public $portraitTimestamp;
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
    public $gearBonus = [];
    public $gearStats = [];
    public $attributes = [];

    public $activeClass;
    public $activeJob;
    public $activeLevel;

    public $minions = [];
    public $mounts = [];

    public $hash;
    public $events;
    public $all50;

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
                case 'portrait':
                case 'guardianIcon':
                case 'cityIcon':
                case 'grandCompanyIcon':
                    $value = explode('?', $value)[0];
                    break;
            }

            // Reset
            $this->$param = $value;
        }

        // Avatar hash
        $this->avatarHash = str_ireplace(['http://img2.finalfantasyxiv.com/f/', '_50x50.jpg', '_96x96.jpg', '_64x64.jpg'], null, $this->avatar);

        // Set basic stuff
        $curve = $this->getExperiencePoints();
        $jobclass = $this->getClassListFull();

        // Set max EXP
        $this->all50 = true;
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

            if ($d['level'] < $this->getMaxLevel()) {
                $this->all50 = false;
            }

            // real id
            $this->classjobs[$i]['real_id'] = array_search(strtolower(str_ireplace(' ', null, $d['name'])), $this->getClassListFull());
        }

        unset($curve);
        unset($jobclass);

        // Sort attributes
        ksort($this->attributes);

        // Set hash
        $this->hash = sha1($this->dump(true));

        // Get item ids
        // http://xivdb.com/api/?type=item&name=all
        // $xivdb = json_decode($this->curl($this->urls()['xivdb'] . '?type=item&name=all'), true);
        $xivdb = require __DIR__ .'/xivdb.php';
        $xivdb = json_decode($xivdb, true);

        // Mape real id to array
        foreach($this->gear as $i => $g)
        {
            // Real ID
            $hash = $this->hashed($g['name']);
            if (isset($xivdb[$hash])) {
                $this->gear[$i]['realId'] = $xivdb[$hash];
				if(isset($g['mirageItemName']) && !empty($g['mirageItemName'])){
					$mirageHash = $this->hashed($g['mirageItemName']);
					$this->gear[$i]['mirageItemRealId'] = isset($xivdb[$mirageHash]) ? $xivdb[$mirageHash] : null;
				}
            }
        }
        unset($xivdb);
    }

	/**
	 * Returns an array of stats, calculated out of the gearbonus
	 * @return array
	 */
	public function getGearBonus(){
		$bonus = [];
		foreach($this->gear as $g){
			if(array_key_exists('bonuses', $g)){
				foreach($g['bonuses'] as $b){
					$keyCleaned = strtolower(str_ireplace(' ', '-', $b['type']));
					if(!array_key_exists($keyCleaned, $bonus)){
						$bonus[$keyCleaned] = [
							'total' => 0,
							'items' => []
						];
					}
					$bonus[$keyCleaned]['total'] += intval($b['value']);
					$bonus[$keyCleaned]['items'][] = [
						'value' => intval($b['value']),
						'name' => $g['name']
					];
				}
			}
		}
		return $bonus;
	}
}
