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

if (!isConnect()) {
  throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<table class="table table-condensed table-bordered" id="table_mod_eqLogicToImport">
  <tr>
    <th style="width: 150px;">{{Objet}}</th>
    <th style="width: 150px;">{{Equipement}}</th>
  </tr>
  <tr>
    <td id="mod_eqLogicToImport_object">
      <select class='form-control'>
        <?php echo jeeObject::getUISelectList(); ?>
       </select>
     </td>
     <td id="mod_eqLogicToImport_eqLogic"></td>
   </tr>
</table>
<div>
<form class="form-horizontal">
  <fieldset>
    <div class="form-group">
      <label class="control-label col-sm-6">{{Etat}} :</label>
      <div id="mod_eqLogicToImport_cmdEtat"></div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-6">{{Commande EN}} :</label>
      <div id="mod_eqLogicToImport_cmdOn"></div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-6">{{Commande HORS}} :</label>
      <div id="mod_eqLogicToImport_cmdOff"></div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-6">{{Créer}} :</label>
      <label class="checkbox-inline"><input type="checkbox" id="mod_eqLogicToImport_tentatives" checked/>{{Compteur de tentatives}}</label>
      <label class="checkbox-inline"><input type="checkbox" id="mod_eqLogicToImport_statut" checked/>{{Statut}}</label>
    </div>
  </fieldset>
</form>
</div>
<script>
  mod_eqLogicToImport = {}

  $('#mod_eqLogicToImport_object').on({
    'change': function(event) {
      select=$(this).find('select').first()
      jeedom.object.getEqLogic({
        id: (select.value() == '' ? -1 : select.value()),
        orderByName : true,
        error: function(error) {
          $('#div_alert').showAlert({message: error.message, level: 'danger'})
        },
        success: function(eqLogics) {
          $('#mod_eqLogicToImport_eqLogic').empty()
          var selectEqLogic = '<select class="form-control">'
          for (var i in eqLogics) {
	    if (eqLogics[i].eqType_name != "swassist") {
              selectEqLogic += '<option value="' + eqLogics[i].id + '">' + eqLogics[i].name + '</option>'
            }
          }
          selectEqLogic += '</select>'
          $('#mod_eqLogicToImport_eqLogic').append(selectEqLogic)
          $('#mod_eqLogicToImport_eqLogic').trigger('change')
        }
      })
    }
  })

  $('#mod_eqLogicToImport_eqLogic').on({
    'change': function(event) {
      select=$(this).find('select').first()
      jeedom.eqLogic.getCmd({
        id: select.value(),
        error: function(error) {
          $('#div_alert').showAlert({message: error.message, level: 'danger'})
        },
	success: function(cmds) {
	  selectEtat = '<select class="form-control col-sm-5">' 
	  selectOn   = '<select class="form-control col-sm-5">' 
	  selectOff  = '<select class="form-control col-sm-5">' 
	  for (var i in cmds) {
	    cmd = cmds[i]
            if (cmd.type == 'info' && cmd.subType == 'binary') {
              selected = ""
              if (cmd.name.search(/\b(etat)\b/i) >= 0) {
                selected = " selected"
              }
              selectEtat += '<option value="' + cmd.id + '">' + cmd.name + '</option>'
            }
	    if (cmd.type == 'action' && cmd.subType == 'other') {
              selected = ""
	      if (jeedom.cmd.normalizeName(cmd.name) == 'on') {
                selected = " selected"
              }
              selectOn  += '<option value="' + cmd.id + '"' + selected + '>' + cmd.name + '</option>'
              selected = ""
	      if (jeedom.cmd.normalizeName(cmd.name) == 'off') {
                selected = " selected"
              }
              selectOff += '<option value="' + cmd.id + '"' + selected + '>' + cmd.name + '</option>'
	    }
	  }
	  selectEtat += '</select>' 
	  selectOn   += '</select>' 
	  selectOff  += '</select>' 
	  $('#mod_eqLogicToImport_cmdEtat').empty().append(selectEtat)
	  $('#mod_eqLogicToImport_cmdOn').empty().append(selectOn)
	  $('#mod_eqLogicToImport_cmdOff').empty().append(selectOff)
	}
      })
    }
  })

  $('#mod_eqLogicToImport_object').trigger('change')

  mod_eqLogicToImport.getSelection = function () {
    retour = {}
    retour.eqLogicId = $('#mod_eqLogicToImport_eqLogic').find('select').first().value()
    retour.cmdEtat = $('#mod_eqLogicToImport_cmdEtat').find('select').first().value()
    retour.cmdOn = $('#mod_eqLogicToImport_cmdOn').find('select').first().value()
    retour.cmdOff = $('#mod_eqLogicToImport_cmdOff').find('select').first().value()
    retour.CreerCmdTentatives = $('#mod_eqLogicToImport_tentatives').value()
    retour.CreerCmdStatut = $('#mod_eqLogicToImport_statut').first().value()
    return (retour)
  }

  $('.mod_eqLogicToImport_object').trigger('change')
</script>
