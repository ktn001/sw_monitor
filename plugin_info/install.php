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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

// Fonction exécutée automatiquement après l'installation du plugin
  function swassist_install() {

  }

// Fonction exécutée automatiquement après la mise à jour du plugin
function swassist_update() {
	$updateState = (int) config::byKey("update::state","swassist", 0);
	log::add("swassist","info",__("Mise à niveau du plugin",__FILE__));
	if ($updateState == 0) {
		log::add("swassist","info",__("Upgrade depuis état 0",__FILE__));
		$cmds = cmd::byLogicalId('nbTentatives');
		log::add("swassist","info","XXX " . print_r($cmds,true));
		foreach ($cmds as $cmd) {
			if ($cmd->getEqType_name() != 'swassist') {
				continue;
			}
			log::add("swassist","info",__("  Upgrade de la commande ",__FILE__) . $cmd->getHumanName());
			if ($cmd->getIsHistorized() != 1) {
				log::add("swassist","info",__("    Activation de l'historisation.",__FILE__));
				$cmd->setIsHistorized(1);
			}
			if ($cmd->getConfiguration('historizeMode', "avg") == 'avg'){
				log::add("swassist","info",__("    Modification du lissage.",__FILE__));
				$cmd->setConfiguration('historizeMode','none');
			}
			if ($cmd->getDisplay('graphType', 'area') == '') {
				log::add("swassist","info",__("    Modification du type de graphique.",__FILE__));
				$cmd->setDisplay("graphType", "column");
			}
		}
		$updateState = 1;
		config::save("update::state",$updateState,"swassist");
		log::add("swassist","info",__("Upgrade état 1 atteint",__FILE__));
	}

}

// Fonction exécutée automatiquement après la suppression du plugin
  function swassist_remove() {

  }

?>
