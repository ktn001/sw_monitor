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

_log("debug", "Lancement de " . __FILE__ );

$options = getopt ("i:");

if ( ! $options ) {
	_log("error", __FILE__ . " : option erronée");
	exit (1);
}

if (! array_key_exists("i", $options)) {
	_log("error", __FILE__ . " : option -i manquante");
	exit (1);
}

$cmd = cmd::byId($options["i"]);

if (! is_object($cmd) ) {
	_log("error","Il n'existe pas de commande avec l'id " . $options['i'] );
	exit (1);
}

if ($cmd->getEqType() != "swassist") {
	_log("error","La commande " . $options['i'] . " n'est pas de type \"swassist\"");
	exit (1);
}

_log('debug','Commande ID : ' . $options['i']);

$delai = $cmd->getConfiguration('delai');
_log("debug","Délai: $delai");

while ($cmd->getRetry()) {
	_log("info", "Relance de la commande " . $cmd->getHumanName()); 
	$cmd->retry();
	sleep ($delai);
}
sleep ($delai);

if ($cmd->getCmdRetour()->execCmd() != $cmd->getConfiguration('targetValue')) {
	_log('error', $cmd->getHumanName() . ": pas de retour de l'execution");
}

exit (0);
