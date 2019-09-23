
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


$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
/*
 * Fonction pour l'ajout d'options (historiser/ affichage par exemple) dans l'onglet commandes de l'équipement, appellé automatiquement par plugin.template
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td>';
	//tr += '<span><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" /> {{Historiser}}<br/></span>'; // checkbox pour le bouton Historiser par exemple de l'onglet commande
    //tr += '<span><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" /> {{Affichage}}<br/></span>'; // checkbox pour Rendre visible ou non le bouton Affichage de l'onglet commande
	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    tr += '</td>';		
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}

// function typefieldChange(){
	// if ($('#typefield').value() == 'vm') {
    	// $('#loginPassword').hide();
		// $('#passwordESXi').hide();
		// $('#ESXiIpAddress').hide();
		// $('#ESXIHostLabel').show();
		// $('#esxiHostfield').show();
		// $('#nbSnapLabel').show();
		// $('#ipAddressLabelRightPartOfPage').show();		
		// $('#ipAddressfield').show();		
	// }else { 
		// $('#loginPassword').show();
		// $('#passwordESXi').show();
		// $('#ESXiIpAddress').show();
		// $('#ESXIHostLabel').hide();
		// $('#esxiHostfield').hide();
		// $('#nbSnapLabel').hide();
		// $('#ipAddressLabelRightPartOfPage').hide();		
		// $('#ipAddressfield').hide();		
	// }
// }
  
// $( "#typefield" ).change(function(){
  // setTimeout(typefieldChange,100);
// });

// Affichage de la page health 
// $('#bt_healthdahua').on('click', function () {
	// console.log("On appelle la modal Health");
	// $('#md_modal').dialog({title: "{{Santé dahua}}"});
	// $('#md_modal').load('index.php?v=d&plugin=dahua&modal=health').dialog('open');
// });

// Appel à la fonction refresh de l'ESXi (bouton synchroniser à gauche de chaque ESXi)
// $('.synchronisation').on('click', function () {
	// console.log("On appelle la fonction Synchroniser de l'ESXI nommé : ");
	// var id = $(this).attr('data-id');
	// console.log(id);
	// $.ajax({
        // type: "POST",
        // url: "plugins/dahua/core/ajax/dahua.ajax.php",
        // data: {
            // action: "synchronisation",
            // id: id,
        // },
        // dataType: 'json',
        // error: function (request, status, error) {
            // handleAjaxError(request, status, error);
        // },
        // success: function (data) {
            // if (data.state != 'ok') {
                // $('#div_alert').showAlert({message: data.result, level: 'danger'});
                // return;
            // }
			//////////////window.location.reload(); // Recharge la page, mais si on sort d'une sauvegarde d'un équipement on se retrouve à retourner dans l'équipement après avoir cliqué sur le bouton synchroniser
			//////////////console.log("On affichage la valeur de window.location : "); // Affiche l'url entière
			//////////////console.log(window.location);
			//////////////console.log("On affichage la valeur de window.origin : "); // affiche tout ce qui est avant le premier / que l'on trouve après le nom du site
			//////////////console.log(window.location.origin);
			// window.location.assign(window.location.origin+'/index.php?v=d&m=dahua&p=dahua'); 
        // }
    // });
// });