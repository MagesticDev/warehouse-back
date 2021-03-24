<?php

class Generation {

	private $fieldName;
	
	private $typeList;
	
	private $levelUpPointList;
    
    private $isTotalGeneration;

    private $number_generation;
    
	var $generationFieldName;
	
    var $golds;
    
	var $Quest;
	
	var $reset_zen;
    
    function __construct($generationFieldName, $class, $number_generation){
		$this->fieldName = $generationFieldName;
		$this->typeList = Array();
		$this->levelUpPointList = Array();
		$this->number_generation = $number_generation;
		$this->golds = 0;
		$countGeneration = MYSQL::query('SELECT COUNT(id) as TOTALFOUND, class, inventory, nbr_'.$this->fieldName.' FROM '.$this->fieldName.' WHERE class=\''.$class.'\'');
        $totalCount = mysqli_fetch_object($countGeneration);
        $this->isTotalGeneration = $totalCount->TOTALFOUND;
		$this->Quest = 0;
		$this->reset_zen = 0;
    }
	
	public function GenerationAction($pseudo, $class, $PersoZen = 0){
		$load_reset_settings = simplexml_load_file('classes/config_mods/grandreset_character_settings.xml');
		$active = trim($load_reset_settings->active);
		if($active == '0' AND !USER::isAdmin()){
			UTILS::notification('warning', 'Désolé, cette fonctionnalité est temporairement indisponible.', false, true);
			header('location: /Compte/Personnages');
			exit;
		}else{
			
			$reset_resets_need = trim($load_reset_settings->resets_need);
			$reset_level = trim($load_reset_settings->level);
			$reset_zen  = trim($load_reset_settings->zen);
			$reset_points = trim($load_reset_settings->bpoints);
			$reset_points_formula = trim($load_reset_settings->bpoints_formula);
			$reset_clear_skills = trim($load_reset_settings->clear_skills);
			$reset_clear_inv = trim($load_reset_settings->clear_inv);
			$reset_stats = trim($load_reset_settings->reset_stats);
			$reset_limit = trim($load_reset_settings->reset_limit);
			$this->golds = ($this->number_generation  + 1) * (trim($load_reset_settings->bcredits));
			$reset_credits_formula = trim($load_reset_settings->bcredits_formula);
			
			$_SESSION['GOLDS'] = $this->golds;
			
			switch ($reset_points_formula){
				case '0': $reset_points = number_format(($this->number_generation  + 1) * $reset_points);  break;
				case '1': $reset_points = '('.number_format($reset_points).'* resets number) - The * amount between levelup bonus points witch is '.number_format($reset_points).' and number of resets that your character have.'; break;
			}
			
			switch ($reset_clear_skills){
				case '0': $reset_clear_skills = 'Non'; break;
				case '1': $reset_clear_skills = 'Oui'; break;
			}
			
			switch ($reset_clear_inv){
				case '0': $reset_clear_inv = 'Non'; break;
				case '1': $reset_clear_inv = 'Oui'; break;
			}
			
			switch ($reset_stats){
				case '0': $reset_stats = 'Non'; break;
				case '1': $reset_stats = 'Oui'; break;
			}
			
			$tplGeneration = new Template();
			$tplGeneration->setFile('generation', './account/generation.html'); 
			 
            // si des générations éxiste pour cette class
            if($this->isTotalGeneration){ 
                //si le joueur n'a pas atteint le nombre maximum de generation
                if($this->number_generation  < $this->isTotalGeneration){ 
                   
                    include('items/warehouse.php');
                    $inventory = new warehouse;
                    
                    $tplGeneration->bloc('GENERATION', array(
                        'PSEUDO' => $pseudo,
                        'RESETS' => $reset_resets_need,
                        'LEVEL' => $reset_level,
                        'ZEN' => number_format($reset_zen),
                        'RESETS_LIMIT' => number_format($reset_limit),
                        'IMG_CLASSE' => UTILS::Classe($class, true),
                        'NUMBER_GENERATION' => $this->number_generation ,
                        'TOTAL_GENERATION' => $this->isTotalGeneration,
                        'GOLDS' => $this->golds,
                        'GENERATION_POINT' => $reset_points,
                        'CLEAR_SKILL' => $reset_clear_skills,
                        'CLEAR_INV' => $reset_clear_inv,
                        'CLEAR_STAT' => $reset_stats,
                        'INVENTORY' => $inventory->inventory(false, UTILS::Classe($class, false, true), $this->number_generation + 1, -$reset_zen),
						'URL' => $_SERVER['REQUEST_URI'],
						'NBR_ZEN' => number_format($PersoZen),
						'REQUIS_ZEN' => number_format($reset_zen),
						'ACTION' => $this->fieldName
					));
					
					if($reset_zen <= $PersoZen){
						$tplGeneration->bloc('GENERATION.IF_OK');
					}else{
						$tplGeneration->bloc('GENERATION.IF_PAS_OK');
					}

                    // On check si un équipement est en cours d'utilisation sur le personnage si oui on le notifie
                    $count = false;
                    $i = 0;
                    while($i < 12){ //768
                        $g_items = MSSQL::query("select substring(Inventory," . ($i * 32 + 1) . ",32) from Character where Name='".$pseudo."'");
                        $i++;
                        $i_info = sqlsrv_fetch_array($g_items, SQLSRV_FETCH_NUMERIC);
                        $count .=  strtoupper(bin2hex($i_info[0]));
                    }

                    if(substr_count($count, 'F') != '768'){
                        UTILS::notification('warning', 'Votre personnage porte un équipement, Attention lors de l\'action '.$this->fieldName.'  l\'équipement sera supprimé définitivement.', false, true);
                    }
                    
                    return $tplGeneration->construire('generation');
                }else{
                    
                    UTILS::notification('warning', 'Vous avez atteint le maximum de '.$this->fieldName.' possible pour ce personnage.', false, true);
                    header('location: /Compte/Personnages');
                    exit; 
                }
            }else{
                UTILS::notification('warning', 'Désolé, les '.$this->fieldName.'s ne sont pas disponibles pour cette classe de personnage.', false, true);
                header('location: /Compte/Personnages');
                exit; 
            }
		}
	}
	
	public function addGenerationType($classes, $generationLevel, $items, $Quest) {
        $this->typeList[$classes][$generationLevel] = &$items;
        $this->Quest = $Quest;
	}
	
	public function setGenerationLevelUpPoints($generationLevel, $points) {
		$this->levelUpPointList[$generationLevel] = $points;
	}
	
	public function getPossibleGenerationList() {
		$query = MSSQL::query('SELECT Name, Class, '.$this->fieldName.', Vitality, Strength, Energy, Dexterity, cLevel FROM Character WHERE AccountID=\''.USER::getPseudo().'\' ORDER BY Name;');
		$list = Array();
		while($row = sqlsrv_fetch_array($query)) {
			if(($row['Vitality'] >= 32000 || $row['Vitality'] < 0) && ($row['Strength'] >= 32000 || $row['Strength'] < 0) && ($row['Energy'] >= 32000 || $row['Energy'] < 0) && ($row['Dexterity'] >= 32000 || $row['Dexterity'] < 0) && isset($this->typeList[$row['Class']]) && ($row[$this->fieldName] < $this->isTotalGeneration)) {
				$list[$row['Name']] = $row[$this->fieldName] + 1;
			}
		}
		
		return $list;
	}
	
	public function doGeneration($characterName) {
		$possibleG = $this->getPossibleGenerationList();
		
		if(!isset($possibleG[$characterName])) { 
			UTILS::addHack('Tentative de ('.$this->fieldName.') d\'un personnage n\'appartenant pas au compte ou n\'ayant pas les stats nécéssaires.', true);
			UTILS::notification('danger', 'L\'action '.$this->fieldName.' est impossible.', false, true);
			header('location: /Compte/Personnages');
			exit;
		}
		
		if(USER::isConnecteIG()){
			UTILS::notification('warning', $this->fieldName.' Votre compte doit être deconnecté du serveur pour procéder à la transaction.', false, true);
			header('location: /Compte/Personnages');
			exit;
		}
		
		
		$character = MSSQL::query('SELECT Class FROM Character WHERE Name=\''.$characterName.'\'');
		$character = sqlsrv_fetch_array($character);
		
		$gToDo = $possibleG[$characterName];
		
		$levelUpPoint = $this->levelUpPointList[$gToDo];
		
		$items = $this->typeList[$character['Class']][$gToDo];
        $i = 0;
        $inventoryViewItem = false;
        while ($i < 12) {
			$g_items = MSSQL::query("select substring(Inventory," . ($i * 632 + 1) . ", 632) from Character where Name='".$characterName."'");
			$i++;
            $i_info = sqlsrv_fetch_array($g_items, SQLSRV_FETCH_NUMERIC);
            $inventoryViewItem .=  strtoupper(bin2hex($i_info[0])); 
        }

        $inventoryViewItem = substr($inventoryViewItem, '768', '15168');
		$fullHexadecimal = $items.$inventoryViewItem; // repacktage des items
        
        $quest ='AAEAFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF000000000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000';
        
        MSSQL::query('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; BEGIN TRANSACTION;');
        MSSQL::query('
            UPDATE Character SET 
				Strength=\'26\', 
				Vitality=\'26\', 
				Energy=\'26\', 
				Dexterity=\'26\', 
				cLevel=\'10\', 
				mLevel=\'0\', 
				mlPoint =\'0\',
				Leadership=\'500\', 
                RESETS=\'0\', 
				Class=\''.$this->Quest.'\',
				Money =\''.$this->reset_zen.'\',
                LevelUpPoint= LevelUpPoint + \''.str_replace(',', '',$levelUpPoint).'\',
				'.$this->fieldName.'='.$this->fieldName.'+1
			    WHERE Name=\''.$characterName.'\'; 
		');
		
		MSSQL::query('
			UPDATE MEMB_INFO SET 
			golds= golds + \''.$_SESSION['GOLDS'].'\' WHERE memb___id =\''.USER::getPseudo().'\'
		');

		unset($_SESSION['GOLDS']); 
        
        
		MSSQL::query('UPDATE Character SET Inventory=0x'.$fullHexadecimal.', Quest=0x'.$quest.' WHERE Name=\''.$characterName.'\'');
        UTILS::notification('success', $this->fieldName.' pour le personnage '.$characterName.' a été éffectué avec succès.', false, true);
        header('location: /Compte/Personnages');
        exit;
	}
}
?>