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
    public static function deadCmd() {
        $return = array();
        foreach (eqLogic::byType('swassist') as $swassist) {
            foreach ($swassist->getCmd() as $cmd) {
                preg_match_all("/#(\d+)#/", $cmd->getConfiguration('cmdLiee'), $matches);
                foreach ($matches[1] as $cmd_id) {
                    if (!cmd::byId(str_replace('#', '', $cmd_id))){
                        $return[] = array('detail' => 'swassist ' . $swassist->getHumanName() . __(' dans le commande ',__FILE) . $cmd->getName(), 'help' => 'cmdLiee', 'who' => '#' . $cmd_id . '#');
                    }
                }
            }
        }
        return $return;
    }

    /*     * *********************Méthodes d'instance************************* */

    public function importEqLogic($eqLogicToImport_Id, $cmdEtatToImport_Id = -1, $cmdOnToImport_Id = -1, $cmdOffToImport_Id = -1, $creerTentatives = "") {
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
	$order = 1;
        foreach ($cmdsToImport as $cmdToImport) {
            if ($cmdToImport->getLogicalId() == "refresh") {
                log::add('swassist','info',sprintf(__("La commande %s n'est pas importée car c'est une commande 'refresh'",__FILE__),$cmdToImport->getHumanName()));
                continue;
            }
            log::add('swassist','debug',__("Import de la commande ",__FILE__) . $cmdToImport->getHumanName() . " (id: " . $cmdToImport->getId() . ")");
            $cmd = swassistCmd::byEqLogicIdCmdName($this->getId(),$cmdToImport->getName());
            if (!is_object($cmd)) {
                $cmd = new swassistCmd();
                $cmd->setEqLogic_id($this->getId());
                $cmd->setName($cmdToImport->getName());
                $cmd->setType($cmdToImport->getType());
                $cmd->setSubType($cmdToImport->getSubType());
                $cmd->setOrder($order);
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
                $cmd->setConfiguration('targetValue', '1');
                $cmd->setConfiguration('repetition', '5');
                $cmd->setConfiguration('delai', '3');
                $cmd->save();
                $cmdOn_Id = $cmd->getId();
            }
            if ($cmdToImport->getId() == $cmdOffToImport_Id) {
                $cmd->setConfiguration('targetValue', '0');
                $cmd->setConfiguration('repetition', '5');
                $cmd->setConfiguration('delai', '3');
                $cmd->save();
                $cmdOff_Id = $cmd->getId();
            }
	    $order += 1;
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
	if ($creerTentatives == 1) {
	    log::add("swassist","debug","Création de la commande de comptage du nombre de tentatives");
            $cmd = new swassistCmd();
            $cmd->setEqLogic_id($this->getId());
	    $cmd->setName("Nb tentatives");
	    $cmd->setLogicalId("nbTentatives");
	    $cmd->setType("info");
	    $cmd->setSubType("numeric");
            $cmd->setOrder($order);
	    $order += 1;
	    $cmd->setTemplate("dashboard","tile");
	    $cmd->setTemplate("mobile","tile");
            $cmd->save();
	}
        $this->refresh();
    }
 
    public function refresh() {
        try {
            foreach ($this->getCmd('info') as $cmd) {
                $value = $cmd->execute();
                if ($cmd->execCmd() != $cmd->formatValue($value)) {
                    $cmd->event($value);
                }
            }
        } catch (Exception $exc) {
            log::add('swassist','error',__('Erreur pour ',__FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
        }
    }

    public function postInsert() {
        $cmd = new swassistCmd();
        $cmd->setEqLogic_id($this->getId());
        $cmd->setLogicalId("refresh");
        $cmd->setName(__("Rafraichir",__FILE__));
        $cmd->setType("action");
        $cmd->setSubType("other");
        $cmd->save();
    }

    public function postSave() {
        $this->refresh();
    }

    /*     * **********************Getteur Setteur*************************** */
}

class swassistCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */

    public function dontRemoveCmd() {
        if ($this->getLogicalId() == 'refresh') {
            return true;
        }
        return false;
    }

    public function preInsert() {
        if ($this->getLogicalId() == "refresh") {
            return;
        }
        if ($this->getLogicalId() == "nbTentatives") {
	    $this->setTemplate("dashboard","tile");
	    $this->setTemplate("mobile","tile");
            return;
        }
        $cmdLiee_id = str_replace("#", "",$this->getConfiguration('cmdLiee'));
        $cmdLiee = cmd::byId($cmdLiee_id);
        if ( ! is_object($cmdLiee)) {
            throw new Exception (sprintf (__("Commande %s: commande liee introuvable",__FILE__),$name));
        }
        $this->setSubType($cmdLiee->getSubType());
        $this->setIsVisible($cmdLiee->getIsVisible());
        $this->setLogicalId($cmdLiee->getLogicalId());
        $this->setUnite($cmdLiee->getUnite());
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

    public function preSave() {
        if ($this->getLogicalId() == "refresh") {
            return;
        }
        if ($this->getLogicalId() == "nbTentatives") {
            return;
        }
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

    function setWaiting($value = '') {
        if ($value == '') {
            $value = $this->getWaiting();
        }
        if ($value == $this->execCmd()) {
            $this->setCache('waiting', '');
        } else {
            if ($value != $this->getWaiting()) {
                $this->setCache('waiting', $value);
            }
        }
    }

    function retry() {
        if ($this->getConfiguration('targetValue') == '') {
            return;
        }
        $cmdLiee = $this->getCmdLiee();
        if ($this->getCmdRetour()->getWaiting() == $cmdLiee->execCmd()) {
            $this->setCache('waiting','');
        }
        $cmdLiee->execCmd();
    }

    // Exécution d'une commande
    public function execute($_options = array()) {
        if ($this->getLogicalId() == 'refresh') {
            $this->getEqLogic()->refresh();
            return;
        }
        if ($this->getType() == 'info') {
            try {
                $result = jeedom::evaluateExpression($this->getConfiguration('cmdLiee'));
                if ($result == $this->getWaiting()) {
                    $this->setCache('waiting', '');
                }
                return $result;
            } catch (Exception $e) {
                log::add('swassist', 'info', $e->getMessages());
                return $this->getConfiguration('cmdLiee');
            }
        }
        if ($this->getType() == 'action') {
            $cmdRetour = $this->getCmdRetour();
            if (is_object($cmdRetour)) {
                $cmdRetour->setWaiting($this->getConfiguration('targetValue'));
            }
            $cmdLiee = $this->getCmdLiee();
            if ($cmdLiee == null) {
                log::add("swassist","error", __("commande Liee introuvable", __FILE__));
            }
            $return = $cmdLiee->execCmd($_options);
            $cmd = __DIR__ . "/../php/repeatCmd.php -i " . $this->getId();
            system::php($cmd . ' >> ' . log::getPathToLog('swassist') . ' 2>&1 &');
            return $return;
        }
    }

    /*     * **********************Getteur Setteur*************************** */

    function getCmdLiee() {
        $cmdLiee_id = $this->getConfiguration('cmdLiee');
        if ($cmdLiee_id == "") {
            return null;
        }
        $cmd = cmd::byId(str_replace('#','',$cmdLiee_id));
        if (! is_object($cmd)) {
            log::add("swassist","error", sprintf (__("commande %s introuvable", __FILE__),$cmdLiee_id));
        }
        log::add("swassist","debug", $cmd->getHumanName());
        return $cmd;
    }

    function getCmdRetour() {
        if ($this->getConfiguration('cmdLiee') == "") {
            return null;
        }
        return cmd::byId(str_replace('#','',$this->getValue()));
    }

    function getDelai() {
        return $this->getConfiguration('delai');
    }

    function getRepetition() {
        return $this->getConfiguration('repetition');
    }

    function getTargetValue() {
        return $this->getConfiguration('targetValue');
    }

    function getWaiting() {
        return $this->getCache('waiting');
    }

}

