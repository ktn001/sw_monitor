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

try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');

	if (!isConnect('admin')) {
		throw new Exception(__('401 - Accès non autorisé', __FILE__));
	}

	if (init('action') == 'getInfosToLink') {

		$eqLogicToLinkId = init('eqLogicToLinkId');
		if ($eqLogicToLinkId == ""){
			throw new Exception(__("Id le l'équipement à lier non défini",__FILE__));
		}
		$id = init("id");
		if ($id == "") {
			throw new Exception(__("ID non défini",__FILE__));
		}
		$swassist = swassist::byid($id);
		if (!is_object($swassist)) {
			throw new Exception(__("Pas de swassist trouvé avec ID : ",__FILE__) . $id);
		}
		if ($swassist->getEqType_name() != "swassist") {
			throw new Exception(__("Function appelée pour un eqLogic qui n'est pas de type swassist mais ",__FILE__) . $swassist->getEqType_name());
		}

		$return = array();
		$eqLogicCmds = cmd::byEqLogicId($eqLogicToLinkId);
		foreach ($eqLogicCmds as $eqLogicCmd) {
			$info = array (	"id"      => $eqLogicCmd->getId(),
					"type"    => $eqLogicCmd->getType(),
					"subType" => $eqLogicCmd->getsubType(),
					"nom"     => $eqLogicCmd->getName());
			$cmd = new cmd();
			$cmd->setEqLogic_id($id);
			$cmd->setName($eqLogicCmd->getName());
			$cmd->setType($eqLogicCmd->getType());
			$cmd->setSubType($eqLogicCmd->getSubType());
			$cmd->setLogicalId($eqLogicCmd->getLogicalId());
			$cmd->setOrder($eqLogicCmd->getOrder());
			$cmd->setTemplate('dashboard',$eqLogicCmd->getTemplate('dashboard'));
			$cmd->setTemplate('mobile',$eqLogicCmd->getTemplate('mobile'));
			$cmd->setIsVisible($eqLogicCmd->getIsVisible());
			if ($cmd->getType() == 'info') {
				$cmd->setValue("#" . $eqLogicCmd->getId() . "#");
				$cmd->setConfiguration('calcul',"#" . $eqLogicCmd->getId() . "#");
				if ($cmd->getSubType() == 'numeric'){
					$cmd->setConfiguration('minValue',$eqLogicCmd->getConfiguration('minValue'));
					$cmd->setConfiguration('maxValue',$eqLogicCmd->getConfiguration('maxValue'));
				}
			}
			if ($cmd->getType() == 'action') {
				$cmd->setConfiguration("actionName","#" . $eqLogicCmd->getId() . "#");
			}
			$cmd->save();

		}
		ajax::success($return);
	}

	if (init('action') == 'linkEqLogic') {
		$id = init("id");
		if ($id == "") {
			throw new Exception(__("ID non défini",__FILE__));
		}
		$swassist = swassist::byid($id);
		if (!is_object($swassist)) {
			throw new Exception(__("Pas de swassist trouvé avec ID : ",__FILE__) . $id);
		}
		if ($swassist->getEqType_name() != "swassist") {
			throw new Exception(__("Function appelée pour un eqLogic qui n'est pas de type swassist mais ",__FILE__) . $swassist->getEqType_name());
		}
		$swassist->linkEqLogic(init ('eqLogicId'));
		ajax::success();
	}

	throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
	/*     * *********Catch exeption*************** */
} catch (Exception $e) {
	ajax::error(displayException($e), $e->getCode());
}

