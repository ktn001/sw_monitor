
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

$('#bt_importEqLogic').off('click').on('click',function () {
    if ($('#mod_cmdToImport').length == 0) {
	$('body').append('<div id="mod_cmdToImport" title="{{Sélectionner un équipement à lier}}"/>');
	$("#mod_cmdToImport").dialog({
	    closeText: '',
	    autoOpen: false,
	    modal: true,
	    height: 300,
	    width: 800
	});
	jQuery.ajaxSetup({async: false});
	$('#mod_cmdToImport').load('index.php?v=d&plugin=swassist&modal=cmdToImport');
	jQuery.ajaxSetup({async: true});
    }
    $('#mod_cmdToImport').dialog('option', 'buttons', {
	"{{Annuler}}": function () {
	    $(this).dialog("close");
	},
	"{{Valider}}": function () {
	    var retour = mod_eqLogicToImport.getSelection();
	    $(this).dialog("close");
	    $.ajax({
		type: "POST",
		url: "plugins/swassist/core/ajax/swassist.ajax.php",
		data: {
		    action: 'importEqLogic',
		    id: $('.eqLogicAttr[data-l1key=id]').value(),
		    eqLogicToImport: retour.eqLogicId,
		    cmdEtat: retour.cmdEtat,
		    cmdOn: retour.cmdOn,
		    cmdOff: retour.cmdOff
		},
		dataType: 'json',
		global: false,
		error: function (request, status, error) {
		    handleAjaxError(request, status, error);
		},
		success: function (data) {
		    if (data.state != 'ok') {
			$('#div_alert').showAlert({message: data.result, level: 'danger'});
			return;
		    }
		    $('.eqLogicDisplayCard[data-eqLogic_id='+$('.eqLogicAttr[data-l1key=id]').value()+']').click();
		}
	    })
	}
    });
    $('#mod_cmdToImport').dialog('open');
});

/*
* Permet la réorganisation des commandes dans l'équipement
*/
$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

/*
 * Ajout d'une Info
 */
$('#bt_addSwassistInfo').on('click', function (event) {
    addCmdToTable({type: 'info'});
    modifyWithoutSave = true;
});

/*
 * Ajout d'une Action
 */
$('#bt_addSwassistAction').on('click', function (event) {
    addCmdToTable({type: 'action'});
    modifyWithoutSave = true;
});

/*
 * Choix d'une info à lier
 */
$("#table_cmd").delegate(".listEquipementInfo", "click", function () {
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
	var cmdLiee = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
	cmdLiee.val(result.human);
    });
});

/*
 * Choix d'une commande à lier
 */
$("#table_cmd").delegate(".listEquipementAction", "click", function () {
    var el = $(this);
    var subtype = $(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').value();
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: subtype}}, function (result) {
	var cmdLiee = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
	cmdLiee.val(result.human);
    });
});

/*
 * Rend la selection du type de commande visible... ou pas
 */
$('#table_cmd').delegate('.cmdAttr[data-l1key=value]', "change", function () {
	var el = $(this);
	var options = el.closest('tr').find('.repeatOptions').first();
	if (el.value() == ""){
		options.hide();
	}else{
		options.show();
	}
});

/*
 *
 */
function prePrintEqLogic(_id) {
	    $('#bt_importEqLogic').removeClass('disabled');
}

/*
* Fonction permettant l'affichage des commandes dans l'équipement
*/
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
	var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
	_cmd.configuration = {};
    }
    if (init(_cmd.logicalId) == 'refresh') {
	return;
    }

    console.log("id: <" + init(_cmd.id) + ">" );
    if (init(_cmd.id) != '') {
	$('#bt_importEqLogic').addClass('disabled');
    }

    if (init(_cmd.type) == 'info') {
	var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
	tr += '<td>';
	tr += '<span class="cmdAttr" data-l1key="id"></span>';
	tr += '</td>';
	tr += '<td>';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width: 140px;" placeholder="{{Nom}}"></td>';
	tr += '</td>';
	tr += '<td>';
	tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
	tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
	tr += '</td>';
	tr += '<td>';
	tr += '<div class="input-group">';
	tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="configuration" data-l2key="cmdLiee" placeholder="{{Commande liée}}"></input>';
	tr += '<span class="input-group-btn">';
	tr += '<a class="btn btn-default btn-sm cursor listEquipementInfo roundedRight" data-input="cmdLiee"><i class="fas fa-list-alt"></i></a>';
	tr += '</span>';
	tr += '</div>';
	tr += '</td>';
	tr += '<td>';
	tr += '</td>';
	tr += '<td>';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;display:inline-block;">';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;display:inline-block;">';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;display:inline-block;margin-right:5px;">';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="listValue" placeholder="{{Liste de valeur|texte séparé par ;}}" title="{{Liste}}">';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label></span> ';
	tr += '</td>';
	tr += '<td>';
	if (is_numeric(_cmd.id)) {
	    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
	    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
	}
	tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
	tr += '</td>';
	tr += '</tr>';
	$('#table_cmd tbody').append(tr);
	$('#table_cmd tbody tr').last().setValues(_cmd, '.cmdAttr');
	if (isset(_cmd.type)) {
	    $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
	}
	jeedom.cmd.changeType($('#table_cmd tbody tr').last(), init(_cmd.subType));
    }

    if (init(_cmd.type) == 'action') {
	var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
	tr += '<td>';
	tr += '<span class="cmdAttr" data-l1key="id"></span>';
	tr += '</td>';
	tr += '<td>';
	tr += '<div class="row">';
	tr += '<div class="col-sm-6">';
	tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> Icône</a>';
	tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
	tr += '</div>';
	tr += '<div class="col-sm-6">';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';
	tr += '</div>';
	tr += '</div>';
	tr += '</div>';
	tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="display: none; margin-top : 5px;margin-right : 10px;" title="{{La valeur de la commande vaut par défaut la commande}}">';
	tr += '<option value="">Aucune</option>';
	tr += '</select>';
	tr += '</td>';
	tr += '<td>';
	tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;" />';
	tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
	tr += '</td>';
	tr += '<td>';
	tr += '<div class="input-group" style="margin-bottom : 5px;">';
	tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="configuration" data-l2key="cmdLiee" placeholder="{{Nom information}}"/>';
	tr += '<span class="input-group-btn">';
	tr += '<a class="btn btn-default btn-sm cursor listEquipementAction roundedRight" data-input="cmdLiee"><i class="fas fa-list-alt "></i></a>';
	tr += '</span>';
	tr += '</div>';
	tr += '</td>';
	tr += '<td>';
	tr += '<div class="input-group repeatOptions" style="display: none">';
	tr += '<div class="form-group" style="display: inline-block; width: 80px; margin-right: 10px">';
	tr += '<label classe="control-label form-control input-sm">{{Commande}}:</label>';
	tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="configuration" data-l2key="targetValue" title="{{type de commande}}">';
	tr += '<option value="1">ON</option>';
	tr += '<option value="0">OFF</option>';
	tr += '</select>';
	tr += '</div>';
	tr += '<div class="form-group" style="display: inline-block; width: 80px; margin-right: 10px">';
	tr += '<label>{{Répétitions}}:</label>';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="repetition" value=5>';
	tr += '</div>';
	tr += '<div class="form-group" style="display: inline-block; width: 80px">';
	tr += '<label>{{Délai}}:</label>';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="delai" value=3>';
	tr += '</div>';
	tr += '</td>';
	tr += '<td>';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;display:inline-block;">';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;display:inline-block;">';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;display:inline-block;margin-right:5px;">';
	tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="listValue" placeholder="{{Liste de valeur|texte séparé par ;}}" title="{{Liste}}">';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label></span> ';
	tr += '</td>';
	tr += '<td>';
	if (is_numeric(_cmd.id)) {
	    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
	    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
	}
	tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
	tr += '</td>';
	tr += '</tr>';
	$('#table_cmd tbody').append(tr);
	$('#table_cmd tbody tr').last().setValues(_cmd, '.cmdAttr');
	var tr = $('#table_cmd tbody tr').last();
	jeedom.eqLogic.builSelectCmd({
	    id:  $('.eqLogicAttr[data-l1key=id]').value(),
	    filter: {type: 'info', subType: 'binary'},
	    error: function (error) {
	      $('#div_alert').showAlert({message: error.message, level: 'danger'});
	    },
	    success: function (result) {
		tr.find('.cmdAttr[data-l1key=value]').append(result);
		tr.find('.cmdAttr[data-l1key=configuration][data-l2key=updateCmdId]').append(result);
		tr.setValues(_cmd, '.cmdAttr');
		jeedom.cmd.changeType(tr, init(_cmd.subType));
	    }
	});
    }
}
