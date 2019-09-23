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
					$openDoor->setType('info');
					$openDoor->setSubType('string');
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
					$accessControl->setType('info');
					$accessControl->setSubType('string');
					$accessControl->save();
					log::add('dahua', 'info', 'Création/Maj de la commande accessControl dans l\'équipement portier');	
					
				$videoTalkLog = $this->getCmd(null, 'videoTalkLog');
				if (!is_object($videoTalkLog)) {
					$videoTalkLog = new dahuaCmd();
					$videoTalkLog->setOrder(2);
				}
					$videoTalkLog->setName(__('Log Appel Vidéo', __FILE__));
					$videoTalkLog->setLogicalId('cpuNumber');
					$videoTalkLog->setEqLogic_id($this->getId());
					$videoTalkLog->setType('info');
					$videoTalkLog->setSubType('string');
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
		//$login = $eqLogics->getConfiguration("login"); // on récupère le login
		$hostIP = $eqLogics->getConfiguration("ipAddress"); // on récupère l'adresseIP
		log::add('dahua', 'debug', 'Login utilisé : ' . $login . ' - Ip du portier : ' . $hostIP); 
		log::add('dahua', 'debug', 'ActionType : ' . $actionType . ' - Nom du portier : ' . $portierHostName . ' - Ip du portier : ' . $portierHostIpAddress . ''); 
		
		log::add('dahua', 'debug', 'Liste des paramètres transmis : ');
		//log::add('dahua', 'debug', 'SnapName : '. $snapName);
		// log::add('dahua', 'debug', 'snapDescription : '. $snapDescription );
		// log::add('dahua', 'debug', 'memory : '. $memory);
		
		// Récupération de l'ID et execution de la commande souhaitée dans la foulée (xargs ne fonctionne pas PIPE ne fonctionne pas)
		log::add('dahua', 'debug', 'ELSE - action autre qu\'un snapshot create ou remove');
		//curl --user $login:$password --digest "http://192.168.50.110/cgi-bin/accessControl.cgi?action=openDoor&channel=1&UserID=101&Type=Remote"
		curl --user $login:$password --digest "http://".$hostIP."/cgi-bin/accessControl.cgi?action=".$actionType."&channel=1&UserID=101&Type=Remote"
				
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
			// if($eqlogic->getConfiguration("type") == 'portier'){
				// log::add('dahua', 'debug', 'On appel la fonction getportierInformationList '); 
				// $return = $eqlogic->getportierInformationList() ;
				// log::add('dahua', 'debug', 'On appel la fonction getvmInformationList '); 
				// $vmListing = $eqlogic->getVmInformationList() ; //Lance la fonction pour récupérer la liste des VMs et stocke le résultat dans vmListing
				// $eqlogic->checkAndUpdateCmd('nbVM', $vmListing[1]); // stocke le contenu de vmListing dans la commande nbVM
				// $eqlogic->checkAndUpdateCmd('vmList', $vmListing[0]); // stocke le contenu de vmListing dans la commande vmList
				// $eqlogic->setConfiguration('portierHost',$vmListing[2]); // stocke le contenu de vmListing dans la commande hoteportier
				// $eqlogic->setConfiguration('type',$vmListing[3]); // stocke le contenu de vmListing dans la commande type
			// }else if($eqlogic->getConfiguration("type") == 'vm') {
				// log::add('dahua', 'debug', 'C\'est une VM que l\'on va mettre à jour '); 
				// $eqlogic->updateVmInformations($eqlogic->getConfiguration("name"),$eqlogic->getConfiguration("portierHost")) ;
			// }else {
				// log::add('dahua', 'debug', 'Ca n\'est pas un portier, on ne rafraichit rien'); 
			// }
            break;
		// case 'takeSnapshot': 
				// log::add('dahua', 'info', 'On appelle la fonction actionOnPortier - takesnapshot pour : ' . $eqlogic->getConfiguration("portierHost") . ' '); 
				// log::add('dahua', 'debug', 'Valeur retrouvée dans l\'appel du scénario : champ Nom : ' . $options['Nom'] . ' ');
				// log::add('dahua', 'debug', 'Valeur retrouvée dans l\'appel du scénario : champ Description :  ' . $options['Description'] . ' ');
				// log::add('dahua', 'debug', 'Valeur retrouvée dans l\'appel du scénario : champ Memory : ' . $_options['message']  .' '); // Attention on attaque bien le champ $_options (transmis à la fonction execute, pas au tableau créé un peu plus haut
				// $memory = $_options['message'];
				// log::add('dahua', 'debug', 'Valeur retrouvée dans l\'appel du scénario : champ Memory dans variable memory : ' . $memory . ' '); // Attention on attaque bien le champ $_options (transmis à la fonction execute, pas au tableau créé un peu plus haut
				// $memory = str_replace(array("NON","OUI"), array("0","1"), $memory ); // On envoi 0 ou 1 selon l'état de la mémoire souhaité lors du snapshot
				// $action = $eqlogic->actionOnPortier('snapshot.create',$options['Nom'],$options['Description'],$memory);
			// break;
		// case 'deleteSnapshot':	
				// log::add('dahua', 'info', 'On appelle la fonction actionOnPortier - snapshot delete');  
				// $action = $eqlogic->actionOnPortier('snapshot.remove',$options['Nom'],'',''); // liste ou saisie manuelle ?)
			// break;
		case 'openDoor':	
				log::add('dahua', 'info', 'On appelle la fonction actionOnPortier - openDoor'); 
				$action = $eqlogic->actionOnPortier('openDoor');
			break;
		// case 'rebootOS':	
				// log::add('dahua', 'info', 'On appelle la fonction actionOnPortier - rebootOS'); 
				// $action = $eqlogic->actionOnPortier('power.reboot');
			// break;
		// case 'stop':	
				// log::add('dahua', 'info', 'On appelle la fonction actionOnPortier - stop'); 
				// $action = $eqlogic->actionOnPortier('power.off');
			// break;
		// case 'stopOS':	
				// log::add('dahua', 'info', 'On appelle la fonction actionOnPortier - stopOS'); 
				// $action = $eqlogic->actionOnPortier('power.shutdown');
			// break;
		// case 'powerOn':	
				// log::add('dahua', 'info', 'On appelle la fonction actionOnPortier - powerOn'); 
				// $action = $eqlogic->actionOnPortier('power.on');
			// break;
		}
		log::add('dahua', 'info', 'Fin fonction execute');
    }

    /*     * **********************Getteur Setteur*************************** */
}

