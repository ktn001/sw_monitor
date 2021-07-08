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

<table class="table table-condensed table-bordered" id="table_mod_eqLogicToLink">
  <tr>
    <th style="width: 150px;">{{Objet}}</th>
    <th style="width: 150px;">{{Equipement}}</th>
  </tr>
  <tr>
    <td id="mod_eqLogicToLink_object">
      <select class='form-control'>
        <?php echo jeeObject::getUISelectList(); ?>
       </select>
     </td>
     <td id="mod_eqLogicToLink_eqLogic"></td>
   </tr>
</table>
<div>
<form class="form-horizontal">
  <fieldset>
    <div class="form-group">
      <label class="control-label col-sm-6">{{Etat}} :</label>
      <div id="mod_eqLogicToLink_cmdEtat"></div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-6">{{Commande EN}} :</label>
      <div id="mod_eqLogicToLink_cmdOn"></div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-6">{{Commande HORS}} :</label>
      <div id="mod_eqLogicToLink_cmdOff"></div>
    </div>
  </fieldset>
</form>
</div>
<script>
  mod_eqLogicToLink = {}

  $('#mod_eqLogicToLink_object').on({
    'change': function(event) {
      select=$(this).find('select').first()
      jeedom.object.getEqLogic({
        id: (select.value() == '' ? -1 : select.value()),
        orderByName : true,
        error: function(error) {
          $('#div_alert').showAlert({message: error.message, level: 'danger'})
        },
        success: function(eqLogics) {
          $('#mod_eqLogicToLink_eqLogic').empty()
          var selectEqLogic = '<select class="form-control">'
          for (var i in eqLogics) {
	    if (eqLogics[i].eqType_name != "swassist") {
              selectEqLogic += '<option value="' + eqLogics[i].id + '">' + eqLogics[i].name + '</option>'
            }
          }
          selectEqLogic += '</select>'
          $('#mod_eqLogicToLink_eqLogic').append(selectEqLogic)
          $('#mod_eqLogicToLink_eqLogic').trigger('change')
        }
      })
    }
  })

  $('#mod_eqLogicToLink_eqLogic').on({
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
              if (cmd.name.search(/\b(on|en)\b/i) >= 0) {
                selected = " selected"
              }
              selectOn  += '<option value="' + cmd.id + '"' + selected + '>' + cmd.name + '</option>'
              selected = ""
              if (cmd.name.search(/\b(off|hors)\b/i) >= 0) {
                selected = " selected"
              }
              selectOff += '<option value="' + cmd.id + '"' + selected + '>' + cmd.name + '</option>'
	    }
	  }
	  selectEtat += '</select>' 
	  selectOn   += '</select>' 
	  selectOff  += '</select>' 
	  $('#mod_eqLogicToLink_cmdEtat').empty().append(selectEtat)
	  $('#mod_eqLogicToLink_cmdOn').empty().append(selectOn)
	  $('#mod_eqLogicToLink_cmdOff').empty().append(selectOff)
	}
      })
    }
  })

  $('#mod_eqLogicToLink_object').trigger('change')

  mod_eqLogicToLink.getSelection = function () {
    retour = {}
    retour.eqLogicId = $('#mod_eqLogicToLink_eqLogic').find('select').first().value()
    retour.cmdEtat = $('#mod_eqLogicToLink_cmdEtat').find('select').first().value()
    retour.cmdOn = $('#mod_eqLogicToLink_cmdOn').find('select').first().value()
    retour.cmdOff = $('#mod_eqLogicToLink_cmdOff').find('select').first().value()
    return (retour)
  }

  $('.mod_eqLogicToLink_object').trigger('change')
</script>
