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
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class swassist extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Méthodes d'instance************************* */

	// Fonction exécutée automatiquement avant la création de l'équipement
	public function preInsert() {

	}

	// Fonction exécutée automatiquement après la création de l'équipement
	public function postInsert() {

	}

	// Fonction exécutée automatiquement avant la mise à jour de l'équipement
	public function preUpdate() {

	}

	// Fonction exécutée automatiquement après la mise à jour de l'équipement
	public function postUpdate() {

	}

	// Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
	public function preSave() {

	}

	// Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
	public function postSave() {

	}

	// Fonction exécutée automatiquement avant la suppression de l'équipement
	public function preRemove() {

	}

	// Fonction exécutée automatiquement après la suppression de l'équipement
	public function postRemove() {

	}

	public function importEqLogic ($eqLogicToImport_Id, $cmdEtatToImport_Id = -1, $cmdOnToImport_Id = -1, $cmdOffToImport_Id = -1) {
		$eqLogicToImport = eqLogic::byId($eqLogicToImport_Id);
		if (!is_object($eqLogicToImport)) {
		    throw new Exception(__("Equipement introuvable : " ,__FILE__) . $eqLogicToImport_Id);
		}
		log::add('swassist','info',sprintf(__("%s : Import de %s",__FILE__), $this->getHumanName(), $eqLogicToImport->getHumanName()));
		log::add('swassist','debug',sprintf(__("commande etat : ",__FILE__) . $cmdEtatToImport_Id));
		log::add('swassist','debug',sprintf(__("commande On : ",__FILE__) . $cmdOnToImport_Id));
		log::add('swassist','debug',sprintf(__("commande Off : ",__FILE__) . $cmdOffToImport_Id));
		$cmdEtat_Id = -1;
		$cmdOn_Id = -1;
		$cmdOff_Id = -1;
		$cmdsToImport = cmd::byEqLogicId($eqLogicToImport_Id);
		foreach ($cmdsToImport as $cmdToImport) {
			log::add('swassist','debug',__("Import de la commande ",__FILE__) . $cmdToImport->getHumanName() . " (id: " . $cmdToImport->getId() . ")");
			$cmd = swassistCmd::byEqLogicIdCmdName($this->getId(),$cmdToImport->getName());
			if (!is_object($cmd)) {
				$cmd = new swassistCmd();
				$cmd->setEqLogic_id($this->getId());
				$cmd->setName($cmdToImport->getName());
				$cmd->setType($cmdToImport->getType());
				$cmd->setSubType($cmdToImport->getSubType());
				$cmd->setOrder($cmdToImport->getOrder());
				$cmd->setConfiguration('cmdLiee',"#" . $cmdToImport->getId() . "#");
			}
			if ($cmd->getType() != $cmdToImport->getType()){
				throw new Exception(sprintf(__('Le type de la commande "%s" existante est imcompatible avec la commande à importer', __FILE__), $cmd->getName()));
			}
			$cmd->save();
			if ($cmdToImport->getId() == $cmdEtatToImport_Id) {
				$cmdEtat_Id = $cmd->getId();
			}
			if ($cmdToImport->getId() == $cmdOnToImport_Id) {
				$cmdOn_Id = $cmd->getId();
			}
			if ($cmdToImport->getId() == $cmdOffToImport_Id) {
				$cmdOff_Id = $cmd->getId();
			}
		}
		if ($cmdEtat_Id >= 0) {
			if ($cmdOn_Id >= 0) {
				$cmd = swassistCmd::byId($cmdOn_Id);
				$cmd->setValue($cmdEtat_Id);
				$cmd->save();
			}
			if ($cmdOff_Id >= 0) {
				$cmd = swassistCmd::byId($cmdOff_Id);
				$cmd->setValue($cmdEtat_Id);
				$cmd->save();
			}
		}
	}

    /*     * **********************Getteur Setteur*************************** */
}

class swassistCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

	public function preInsert () {
		$cmdLiee_id = str_replace("#", "",$this->getConfiguration('cmdLiee'));
		$cmdLiee = cmd::byId($cmdLiee_id);
		if ( ! is_object($cmdLiee)) {
			throw new Exception (sprintf (__("Commande %s: commande liee introuvable",__FILE__),$name));
		}
		$this->setSubType($cmdLiee->getSubType());
		$this->setIsVisible($cmdLiee->getIsVisible());
		$this->setLogicalId($cmdLiee->getLogicalId());
		$cfgCmdLiee = $cmdLiee->getConfiguration();
		$configs = array('minValue', 'maxValue', 'listValue');
		foreach ($configs as $config) {
			if (array_key_exists($config, $cfgCmdLiee)) {
				if ($cfgCmdLiee[$config] != "") {
					$this->setConfiguration($config,$cfgCmdLiee[$config]);
				}
			}
		}
		$this->setTemplate('dashboard',$cmdLiee->getTemplate('dashboard'));
		$this->setTemplate('mobile',$cmdLiee->getTemplate('mobile'));
	}

	public function preSave () {
		$name = trim ($this->getName());
		$cmdLiee_id = str_replace("#", "",$this->getConfiguration('cmdLiee'));
		$cmdLiee = cmd::byId($cmdLiee_id);
		if ( ! is_object($cmdLiee)) {
			throw new Exception (sprintf (__("Commande %s: commande liee introuvable",__FILE__),$name));
		}
		if ($this->getType() == 'info') {
			$this->setValue($cmdLiee_id);
		}
	}

	// Exécution d'une commande
	public function execute($_options = array()) {
		if ($this->getType() == 'info') {
			try {
				$result = jeedom::evaluateExpression($this->getConfiguration('cmdLiee'));
				if(is_string($result)){
					$result = str_replace('"', '', $result);
				}
				return $result;
			} catch (Exception $e) {
				log::add('swassist', 'info', $e->getMessages());
				return $this->getConfiguration('cmdLiee');
			}
		}
		if ($this->getType() == 'action') {
			$cmdId = $this->getConfiguration('cmdLiee');
			$cmd = cmd::byId(str_replace('#', '', $cmdId));
			return $cmd->execCmd($_options);
		}
	}

    /*     * **********************Getteur Setteur*************************** */
}


