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

require_once __DIR__ . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/../class/swassist.class.php';

function  _log ($level, $msg) {
	log::add('swassist', $level, 'repeatCmd [' . getmypid() . '] ' . $msg);
}

_log("debug", __("Lancement de ",__FILE__) . __FILE__ );

$options = getopt ("i:");

if ( ! $options ) {
	_log("error", __FILE__ . " : " . __("option erronée",__FILE__));
	exit (1);
}

if (! array_key_exists("i", $options)) {
	_log("error", __FILE__ . " : " .  __("option -i manquante",__FILE__));
	exit (1);
}

$cmd = cmd::byId($options["i"]);

if (! is_object($cmd) ) {
	_log("error",__("Il n'existe pas de commande avec l'id ",__FILE__) . $options['i'] );
	exit (1);
}

if ($cmd->getEqType() != "swassist") {
	_log("error",sprintf(__("La commande %S n'est pas de type swassist", __FILE__), $options['i']) . '"swassist"');
	exit (1);
}

_log('debug',__('Commande ID : ', __FILE__) . $options['i']);

$delai = $cmd->getConfiguration('delai');
_log("debug",__("Délai: ", __FILE__) . $delai);

while ($cmd->getRetry()) {
	_log("info", __("Relance de la commande ", __FILE__) . $cmd->getHumanName()); 
	$cmd->retry();
	sleep ($delai);
}
sleep ($delai);

if ($cmd->getCmdRetour()->execCmd() != $cmd->getConfiguration('targetValue')) {
	_log('alert', __("Echec de la commande ",__FILE__) . $cmd->getHumanName());
}

exit (0);
