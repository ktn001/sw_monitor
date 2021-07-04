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

	public function linkEqLogic ($eqLogicId) {
		if ($eqLogicId == "") {
			throw new Exception(__("EqLogic ID non défini",__FILE__));
		}
		$eqLogic = eqLogic::byId($eqLogicId);

	}

    /*     * **********************Getteur Setteur*************************** */
}

class swassistCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

	// Exécution d'une commande
	public function execute($_options = array()) {
		if ($this->getType() == 'info') {
			try {
				$result = jeedom::evaluateExpression($this->getConfiguration('calcul'));
			 	if(is_string($result)){
					$result = str_replace('"', '', $result);
			 	}
				return $result;
			} catch (Exception $e) {
				log::add('swassist', 'info', $e->getMessages());
				return $this->getConfiguration('calcul');
			}
		}
		if ($this->getType() == 'action') {
			$cmdId = $this->getConfiguration('actionName');
			$cmd = cmd::byId(str_replace('#', '', $cmdId));
			$cmd->execCmd($_options);
		}
	}

    /*     * **********************Getteur Setteur*************************** */
}


