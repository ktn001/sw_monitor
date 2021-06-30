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

	if (init('action') == 'linkProposition') {

		$eqLogicId = init('eqLogicId');
		if ($eqLogicId == ""){
			throw new Exception(__("Id le l'équipement à lier non défini",__FILE__));
		}

		$id = init("id");
		if ($id == "") {
			throw new Exception(__("ID non défini",__FILE__));
		}
		$swmonitor = swmonitor::byid($id);
		if (!is_object($swmonitor)) {
			throw new Exception(__("Pas de swmonitor trouvé avec ID : ",__FILE__) . $id);
		}
		if ($swmonitor->getEqType_name() != "swmonitor") {
			throw new Exception(__("Function appelée pour un eqLogic qui n'est pas de type swmonitor mais ",__FILE__) . $swmonitor->getEqType_name());
		}

		$return = array();
		$eqLogicCmds = cmd::byEqLogicId($eqLogicId);
		foreach ($eqLogicCmds as $eqLogicCmd) {
			$return["proposition"][] = array ("id"      => $eqLogicCmd->getId(),
							  "type"    => $eqLogicCmd->getType(),
							  "subType" => $eqLogicCmd->getsubType(),
							  "nom"     => $eqLogicCmd->getName());
		}
		$swmonitorCmds = cmd::byEqLogicId($id);
		foreach ($swmonitorCmds as $swmonitorCmd) {
			$return["actuel"][$swmonitorCmd->getType()][] = array("id" => $swmonitorCmd->getId(), "nom" => $swmonitorCmd->getName());
		}
		ajax::success($return);
	}

	if (init('action') == 'linkEqLogic') {
		$id = init("id");
		if ($id == "") {
			throw new Exception(__("ID non défini",__FILE__));
		}
		$swmonitor = swmonitor::byid($id);
		if (!is_object($swmonitor)) {
			throw new Exception(__("Pas de swmonitor trouvé avec ID : ",__FILE__) . $id);
		}
		if ($swmonitor->getEqType_name() != "swmonitor") {
			throw new Exception(__("Function appelée pour un eqLogic qui n'est pas de type swmonitor mais ",__FILE__) . $swmonitor->getEqType_name());
		}
		$swmonitor->linkEqLogic(init ('eqLogicId'));
		ajax::success();
	}

	throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
	/*     * *********Catch exeption*************** */
} catch (Exception $e) {
	ajax::error(displayException($e), $e->getCode());
}

