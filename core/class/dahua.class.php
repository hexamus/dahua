<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

//log::add('dahua', 'debug', 'On est dans la fonction PowerCli - On va interroger l\'portier'); 
//log::add('dahua', 'alert', 'On est dans la boucle PowerCli - Alerte'); 
//log::add('dahua', 'emergency', 'On est dans la boucle PowerCli - Emergency'); 
//log::add('dahua', 'critical', 'On est dans la boucle PowerCli - Critical'); 
//log::add('dahua', 'error', 'On est dans la boucle PowerCli - Error'); 
//log::add('dahua', 'warning', 'On est dans la boucle PowerCli - Warning'); 
//log::add('dahua', 'notice', 'On est dans la boucle PowerCli - Notice'); 
//log::add('dahua', 'info', 'On est dans la boucle PowerCli - Info'); 
		
class dahua extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
    
	
    /*     * *********************Méthodes d'instance************************* */

    /*public function preInsert() {
        
    }

    public function postInsert() {
        
    }*/

    public function preSave() {
		log::add('dahua', 'info', '========================================================');
		log::add('dahua', 'info', '================= Début du log PreSave =================');
		log::add('dahua', 'info', '========================================================');
			  	
		if($this->getConfiguration("type",'none') == 'none'){
			$this->setCategory('automatism', 1);
			$this->setConfiguration('type','portier');
			$this->setConfiguration('name',$this->getName());
			//$this->setConfiguration('portierHost',$this->getName());
			$this->setLogicalId('dahua'.$this->getName());
			log::add('dahua', 'debug', 'C\'est un portier, on vient d\'ajouter des paramètres à sa configuration');
		}		
		log::add('dahua', 'info', 'Fin du log - Fonction preSave');
    }

    public function postSave() {
			log::add('dahua', 'info', '========================================================');
			log::add('dahua', 'info', '================= Début du log PostSave ================');
			log::add('dahua', 'info', '========================================================');
			
			if($this->getConfiguration("type") == 'portier'){ // Création des commandes spécifiques au portier
				$openDoor = $this->getCmd(null, 'openDoor');
				if (!is_object($openDoor)) {
					$openDoor = new dahuaCmd();
					$openDoor->setOrder(0);
				}
					$openDoor->setName(__('Déverouillage', __FILE__));
					$openDoor->setLogicalId('openDoor');
					$openDoor->setEqLogic_id($this->getId());
					$openDoor->setType('action');
					$openDoor->setSubType('other');
					$openDoor->save();
					log::add('dahua', 'info', 'Création/Maj de la commande openDoor dans l\'équipement portier');
				
				$accessControl = $this->getCmd(null, 'accessControl');
				if (!is_object($accessControl)) {
					$accessControl = new dahuaCmd();
					$accessControl->setOrder(1);
				}
					$accessControl->setName(__('Contrôle d\'accès', __FILE__));
					$accessControl->setLogicalId('accessControl');
					$accessControl->setEqLogic_id($this->getId());
					$accessControl->setType('action');
					$accessControl->setSubType('other');
					$accessControl->save();
					log::add('dahua', 'info', 'Création/Maj de la commande accessControl dans l\'équipement portier');	
					
				$videoTalkLog = $this->getCmd(null, 'videoTalkLog');
				if (!is_object($videoTalkLog)) {
					$videoTalkLog = new dahuaCmd();
					$videoTalkLog->setOrder(2);
				}
					$videoTalkLog->setName(__('Log Appel Vidéo', __FILE__));
					$videoTalkLog->setLogicalId('videoTalkLog');
					$videoTalkLog->setEqLogic_id($this->getId());
					$videoTalkLog->setType('action');
					$videoTalkLog->setSubType('other');
					$videoTalkLog->save();	 
					log::add('dahua', 'info', 'Création/Maj de la commande videoTalkLog dans l\'équipement portier');
								
			}      		
		$refresh = $this->getCmd(null, 'refresh');
		if (!is_object($refresh)) {
			$refresh = new dahuaCmd();
			$refresh->setName(__('Rafraichir', __FILE__));
			$refresh->setEqLogic_id($this->getId());
			$refresh->setLogicalId('refresh');
			$refresh->setType('action');
			$refresh->setSubType('other');
			$refresh->save();
			log::add('dahua', 'info', 'Création de la commande Refresh dans l\'équipement portier');
		}
		log::add('dahua', 'info', 'Fin du log - Fonction postSave');
	}
	
	
	public function getPortierInformation() {
      	log::add('dahua', 'info', '========================================================');
		log::add('dahua', 'info', '========== Début du log getPortierInformation ===========');
		log::add('dahua', 'info', '========================================================');
		
		$password = $this->getConfiguration("password"); // on récupère le password
		$login = $this->getConfiguration("login"); // on récupère le login
		$hostIP = $this->getConfiguration("ipAddress"); // on récupère l'adresseIP
		$portierHostName = $this->getConfiguration("portierHost"); // on récupère le nom de l'portier
  
		log::add('dahua', 'debug', 'Login utilisé : ' . $login . ' - Ip du portier : ' . $hostIP); 
			
		log::add('dahua', 'debug', 'On appelle la commande qui liste les informations du portier'); 
	
		log::add('dahua', 'info', 'Fin du log - Fonction getPortierInformation');
	}


	public function actionOnPortier($actionType) { // type d'action / nom du snapshot / descript du snap / avec mémoire ou non
		log::add('dahua', 'info', '========================================================');
		log::add('dahua', 'info', '================= Début du log actionOnPortier ==============');
		log::add('dahua', 'info', '========================================================');		
		
		$login = "";
		$password = "";
		$hostIP = "";
		
		//$portierHostName = $this->getConfiguration("portierHost"); // récupération du nom de l'portier qui héberge la VM pour récupérer les informations de connexion
		//$portierHostIpAddress = $this->getConfiguration("portierHostIpAddress"); // récupération de l'IP de l'portier qui héberge la VM
		$portierHostName = $this->getConfiguration("name"); // récupération du nom de l'équipement
		
		//$eqLogics = eqLogic::byLogicalId('dahua'.$portierHostName,'dahua');
		
		//$password = $eqLogics->getConfiguration("password"); // on récupère le password
		$login = $this->getConfiguration("login"); // on récupère le login
		$hostIP = $this->getConfiguration("ipAddress"); // on récupère l'adresseIP
		log::add('dahua', 'debug', 'Login utilisé : ' . $login . ' - Ip du portier : ' . $hostIP .''); 
		log::add('dahua', 'debug', 'ActionType : ' . $actionType . ' - Nom du portier : ' . $portierHostName . ' - Ip du portier : ' . $hostIP . ''); 
				
		//curl --user $login:$password --digest "http://192.168.50.110/cgi-bin/accessControl.cgi?action=openDoor&channel=1&UserID=101&Type=Remote"
		$request = 'curl --user $login:$password --digest "http://".$hostIP."/cgi-bin/accessControl.cgi?action=".$actionType."&channel=1&UserID=101&Type=Remote";'
		log::add('dahua', 'debug', 'Contenu de la requête : ' . $request .'');
		
				
		log::add('dahua', 'info', 'Fin fonction actionOnPortier'); 
		//return $result; // a voir ce que l'on peut faire de ça, besoin réel ?	
	}
		
	
    /*     * **********************Getteur Setteur*************************** */
}

class dahuaCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    
      public function dontRemoveCmd() {
		return true;
      }
     
	public function execute($_options = array()) {
		log::add('dahua', 'info', '========================================================');
		log::add('dahua', 'info', '================== Début du log execute ================');
		log::add('dahua', 'info', '========================================================');		
		
        $eqlogic = $this->getEqLogic(); 
		
		//$options = array(); // tableau vide
		//if (isset($_options['title'])) { // Si Title est définit (champ nommé Nom - Description sur un scénario)
		//	$options = arg2array($_options['title']); // On convertit en tableau les informations contenu dans title (nom / description) dans $options
		//}
		
		switch ($this->getLogicalId()) {				
		case 'refresh':  
			log::add('dahua', 'debug', 'On est dans le case refresh de la class dahuaCmd '); 
            break;
		case 'openDoor':	
			log::add('dahua', 'info', 'On appelle la fonction actionOnPortier - openDoor'); 
			$action = $eqlogic->actionOnPortier('openDoor');
			break;
		}
		log::add('dahua', 'info', 'Fin fonction execute');
    }

    /*     * **********************Getteur Setteur*************************** */
}

