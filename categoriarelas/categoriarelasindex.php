<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       categoriarelas/categoriarelasindex.php
 *	\ingroup    categoriarelas
 *	\brief      Home page of categoriarelas top menu
 */

// Load Dolibarr environment
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/genericobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/product/modules_product.class.php';

if (!empty($conf->propal->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
}
if (!empty($conf->facture->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
}
if (!empty($conf->commande->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
}
if (!empty($conf->accounting->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/core/lib/accounting.lib.php';
}
if (!empty($conf->accounting->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formaccounting.class.php';
}
if (!empty($conf->accounting->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';
}

// Load translation files required by the page
$langs->loadLangs(array('products', 'other'));
if (!empty($conf->stock->enabled)) {
	$langs->load("stocks");
}
if (!empty($conf->facture->enabled)) {
	$langs->load("bills");
}
if (!empty($conf->productbatch->enabled)) {
	$langs->load("productbatch");
}

$langs->load("categoriarelas@categoriarelas");

$mesg = ''; $error = 0; $errors = array();

$refalreadyexists = 0;

$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$type = (GETPOST('type', 'int') !== '') ? GETPOST('type', 'int') : Product::TYPE_PRODUCT;
$action = (GETPOST('action', 'alpha') ? GETPOST('action', 'alpha') : 'view');
$cancel = GETPOST('cancel', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
$socid = GETPOST('socid', 'int');
$duration_value = GETPOST('duration_value', 'int');
$duration_unit = GETPOST('duration_unit', 'alpha');
$lineid   = GETPOST('lineid', 'int');

$accountancy_code_sell = GETPOST('accountancy_code_sell', 'alpha');
$accountancy_code_sell_intra = GETPOST('accountancy_code_sell_intra', 'alpha');
$accountancy_code_sell_export = GETPOST('accountancy_code_sell_export', 'alpha');
$accountancy_code_buy = GETPOST('accountancy_code_buy', 'alpha');
$accountancy_code_buy_intra = GETPOST('accountancy_code_buy_intra', 'alpha');
$accountancy_code_buy_export = GETPOST('accountancy_code_buy_export', 'alpha');

// by default 'alphanohtml' (better security); hidden conf MAIN_SECURITY_ALLOW_UNSECURED_LABELS_WITH_HTML allows basic html
$label_security_check = empty($conf->global->MAIN_SECURITY_ALLOW_UNSECURED_LABELS_WITH_HTML) ? 'alphanohtml' : 'restricthtml';

if (!empty($user->socid)) {
	$socid = $user->socid;
}

$object = new Product($db);
$object->type = $type; // so test later to fill $usercancxxx is correct
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);



$result = $object->fetch($id, $ref);




	// Actions cancel, add, update, delete or clone quality object
	include 'core/class/actions_categoriarelas.class.php';
	if ($action == 'confirm_deleteline')
	{
		header("location:".$_SERVER["PHP_SELF"]);
	}

//echo "<pre>";
//print_r($extrafields);


//	^^
//	||
//	||
//	||
//PRODUCT


// Security check
// if (! $user->rights->categoriarelas->myobject->read) {
// 	accessforbidden();
// }
/*$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}*/

$max = 5;
$now = dol_now();


/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("CategoriaRelasArea"));

print load_fiche_titre($langs->trans("CategoriaRelasArea"), '', 'categoriarelas.png@categoriarelas');


//print dol_get_fiche_head();
//==============>>>>>> Aqui Lines
// Part to edit record
/*if (($id || $ref) && $action == 'editquality')
{
	//$head = muprofPrepareHead($object);

	print load_fiche_titre($langs->trans("quality_of_the_piece"), '', 'multiprodof');


	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	if ($backtopage) print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	if ($backtopageforcancel) print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';

	print dol_get_fiche_head();

	unset($object->fields['ref']);
	unset($object->fields['fk_project']);
	unset($object->fields['fk_warehouse']);
	unset($object->fields['date_start_planned']);
	unset($object->fields['date_end_planned']);
	unset($object->fields['fk_soc']);
	unset($object->fields['date_creation']);
	unset($object->fields['fk_user_creat']);
	unset($object->fields['numermolde']);

	//Only viewa and edit fields for quality
	$object->fields['temper']['visible']=1;
	$object->fields['water_circulates']['visible']=1;
	$object->fields['rack_in_temp']['visible']=1;
	$object->fields['ejector_safety']['visible']=1;
	$object->fields['mold_cleaning']['visible']=1;
	$object->fields['dater']['visible']=1;
	$object->fields['notes']['visible']=1;

	print '<table class="border centpercent tableforfieldedit">'."\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_edit.tpl.php';

	// Other attributes
	//include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_edit.tpl.php';

	print '</table>';

	print dol_get_fiche_end();

	print '<div class="center"><input type="submit" class="button button-save" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button button-cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}*/

// Part to show record
if ((empty($action) || ($action != 'edit')))
{
	//$res = $object->fetch_thirdparty();
	//$res = $object->fetch_optionals();

	//$head = muprofPrepareHead($object);

	//print dol_get_fiche_head($head, 'quality', $langs->trans("ManufacturingOrder"), -1, $object->picto);

	$formconfirm = '';

	// Confirmation to delete
	if ($action == 'delete')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteMuPrOf'), $langs->trans('ConfirmDeleteMuPrOf'), 'confirm_delete', '', 0, 1);
	}
	// Confirmation to delete line
	if ($action == 'deleteline')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?lineid='.$lineid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_deleteline', '', 0, 1);
	}


	// Call Hook formConfirm
	$parameters = array('formConfirm' => $formconfirm, 'lineid' => $lineid);
	$reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	if (empty($reshook)) $formconfirm .= $hookmanager->resPrint;
	elseif ($reshook > 0) $formconfirm = $hookmanager->resPrint;

	// Print form confirm
	print $formconfirm;


	// Object card
	// ------------------------------------------------------------
	$linkback = '<a href="'.dol_buildpath('/multiprodof/muprof_list.php', 1).'?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

	/*$morehtmlref = '<div class="refidno">';


	// Thirdparty
	//$morehtmlref .= $langs->trans('ThirdParty').' : '.(is_object($object->thirdparty) ? //$object->thirdparty->getNomUrl(1) : '');
	// Project
	if (!empty($conf->projet->enabled))
	{
		$langs->load("projects");
		$morehtmlref .= '<br>'.$langs->trans('Project').' ';
		if ($permissiontoadd)
		{
			if ($action != 'classify')
				$morehtmlref .= '<a class="editfielda" href="'.$_SERVER['PHP_SELF'].'?action=classify&amp;id='.$object->id.'">'.img_edit($langs->transnoentitiesnoconv('SetProject')).'</a> : ';
			if ($action == 'classify') {
				//$morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->fk_soc, $object->fk_project, 'projectid', 0, 0, 1, 1);
				$morehtmlref .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
				$morehtmlref .= '<input type="hidden" name="action" value="classin">';
				$morehtmlref .= '<input type="hidden" name="token" value="'.newToken().'">';
				$morehtmlref .= $formproject->select_projects($object->fk_soc, $object->fk_project, 'projectid', 0, 0, 1, 0, 1, 0, 0, '', 1);
				$morehtmlref .= '<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
				$morehtmlref .= '</form>';
			} else {
				$morehtmlref .= $form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, $object->fk_soc, $object->fk_project, 'none', 0, 0, 0, 1);
			}
		} else {
			if (!empty($object->fk_project)) {
				$proj = new Project($db);
				$proj->fetch($object->fk_project);
				$morehtmlref .= $proj->getNomUrl();
			} else {
				$morehtmlref .= '';
			}
		}
	}
	$morehtmlref .= '</div>';

*/
	//dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);


	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent tableforfield">'."\n";

	//Only viewa and edit fields for quality
	$object->fields['fk_warehouse']['visible']=0;

	$object->fields['temper']['visible']=1;
	$object->fields['water_circulates']['visible']=1;
	$object->fields['rack_in_temp']['visible']=1;
	$object->fields['ejector_safety']['visible']=1;
	$object->fields['mold_cleaning']['visible']=1;
	$object->fields['dater']['visible']=1;
	$object->fields['notes']['visible']=1;
	$object->fields['signature']['visible']=1;
	$object->fields['fk_user_quality']['visible']=1;


	//Only viewa and edit fields for quality


	// Common attributes
	$keyforbreak = 'dater';
	unset($object->fields['fk_project']);
	unset($object->fields['fk_warehouse']);
	unset($object->fields['date_start_planned']);
	unset($object->fields['date_end_planned']);
	unset($object->fields['fk_soc']);
	unset($object->fields['numermolde']);
	if($object->fk_user_quality!="")
	{
		$userstatic = new User($db);
		$userstatic->fetch($object->fk_user_quality);
		$object->fk_user_quality = $userstatic->getNomUrl(1, '', 0, 0, 24, 0, 'login');
	}
	//include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_view.tpl.php';

	// Other attributes
	//include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_view.tpl.php';

	print '</table>';
	print '</div>';
	print '</div>';

	print '<div class="clearboth"></div>';

	//print dol_get_fiche_end();



		/*
	 * Lines
	 */

		print '<div id="qualitysheet" style=" width:80vw; overflow-x:auto !important;">';
		print '<style>table.liste th, table.noborder th, table.noborder tr.liste_titre td, table.noborder tr.box_titre td, table.liste td, table.noborder td, div.noborder form div, table.tableforservicepart1 td, table.tableforservicepart2 td {
	padding: 7px 2px 7px 2px !important;
} table.noborder{font-size: 0.80em;}</style>';
		print '	<form  name="addrule" id="addrule" action="'.$_SERVER["PHP_SELF"].(($action != 'editline') ? '#addline' : '').'" method="POST">
		<input type="hidden" name="token" value="' . newToken().'">
		<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addlinerule' : 'updatelinerule').'">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="backtopage" value="'.$backtopage.'">';


		if (!empty($conf->use_javascript_ajax) ) {
			include DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php';
		}

			print '<table  id="tablelines" class="noborder noshadow" >';


			// Form to add new line

				if ($action != 'editline')
				{
					include('tpl/objectline_title.tpl.php');
					// Add products/services form
					include('tpl/objectline_create.tpl.php');

					//$parameters = array();
					//$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
				}

			$sql = 'SELECT * ';
			$sql .= ' FROM '.MAIN_DB_PREFIX.'product_extra_relas as t';

			$resql = $db->query($sql);
			if ($resql) {
				$num = $db->num_rows($resql);

				$i = 0;
				include('tpl/objectline_title.tpl.php');
				while ($i < $num) {
					$obj = $db->fetch_object($resql);
					if ($obj) {
						$coldisplay = 0;
						if($action == 'editline' && $obj->id == $lineid )
						{

							include('tpl/objectline_edit.tpl.php');
						}
						else {
							include('tpl/objectline_view.tpl.php');
						}
					}
					$i++;
				}
			}
			/*include('tpl/objectline_title.tpl.php');
			foreach($Quality->lines as $line)
			{
				$coldisplay = 0;
				if($action == 'editline' && $line->id == $lineid )
				{

					//include(DOL_DOCUMENT_ROOT.'/multiprodof/quality/tpl/objectline_edit.tpl.php');
				}
				else {
					include('tpl/objectline_view.tpl.php');
				}
			}*/




			print '</table>';

		print '</div>';


		print "</form>\n";
		print "</div>";


}

?>
<style>
	.imcheck, .imcan{cursor:pointer}
</style>



<script>
/*$(document).ready(function() {
	"use strict";
	//Init event

	$(".imcheck").on('click', function(){
		$(this).css('opacity', '1');
		$("#ca"+$(this).attr("raw")).css('opacity', '0.2');
		$("#"+$(this).attr("raw")).val("YES");
		//console.log($(this));
	});
	$(".imcan").on('click', function(){
		$(this).css('opacity', '1');
		$("#ck"+$(this).attr("raw")).css('opacity', '0.2');
		$("#"+$(this).attr("raw")).val("NO");
		//console.log($(this));
	});

	$("#addline").on('click', function(){
		CheckLine();
	});

	$("#savelinebutton").on('click', function(){
		CheckLine();
	});

	<?php if($action == 'editline' && $line->id != "" )
	{?>
	setTimeout(function(){
	$("#notes").focus();
	var scrollDiv = document.getElementById("qualitysheet").offsetTop;
	window.scrollTo({ top: scrollDiv-70, behavior: 'smooth'});
}, 300);
	//document.getElementById("qualitysheet").scrollIntoView();
	<?php } ?>

}
);
CheckLine = function()
{
	var fail=0;
	var vales = document.querySelectorAll('.Checkid');
	for(vale of vales)//in es index of es para valor
	{
		if(vale.value=="")
		{
			fail++;
		}

	}
	if(fail>0)
	{
		alert("<?php echo $langs->trans("Por_favor_marque")?>");
	}
	else
	{
		if($("#addline").length > 0)
		{
			$("#addline").attr("type",'submit');
			$("#addline").click();
		}
		if($("#savelinebutton").length > 0)
		{
			$("#savelinebutton").attr("type",'submit');
			$("#savelinebutton").click();
		}

	}
}*/
</script>
<?php

// End of page
llxFooter();
//echo "<pre>";
//print_r($extrafields);
$labes_ex = $extrafields->attribute_label;
$edits_ex = $extrafields->attribute_alwayseditable;


$select2_id=100;
$multisel = '<select id="fields" class="multiselect quatrevingtpercent widthcentpercentminusx select2-hidden-accessible" multiple="" name="fields[]" data-select2-id="fields" tabindex="-1" aria-hidden="true">';
foreach($edits_ex as $key => $val)
{
	if($val==1)
	{
		$multisel.= '<option value="'.$key.'" data-html="'.$labes_ex[$key].'">'.$labes_ex[$key].'</option>';
		$select2_id++;
	}
}

$multisel .= '</select><span  class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="25"><span style="display:none !important" class="selection"><span class="select2-selection select2-selection--multiple" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false"><ul class="select2-selection__rendered" >';

foreach($edits_ex as $key => $val)
{
	if($val==1)
	{
		$multisel.= '<li class="select2-selection__choice" title="Lavorazioni" data-select2-id="'.$key.'"><span class="select2-selection__choice__remove" role="presentation">×</span>'.$labes_ex[$key].'</li>';

		$select2_id++;
	}
}


$multisel .= '</ul></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>';


//print_r($extrafields);
$selex = '<select class="flat minwidth100 maxwidthonsmartphone" tabindex="-1" aria-hidden="true"><option value="" >&nbsp;</option>';
foreach($edits_ex as $key => $val)
{
	if($val==1)
	{
		$selex.= '<option value="'.$key.'" >'.$labes_ex[$key].'</option>';
	}
}
$selex.= '</select>';

?>

<div style="display:none">

	<?php print $object->showOptionals($extrafields, 'create', '');?>
</div>
<script>

<?php $labes_exs = json_encode($labes_ex);
echo "var labes_ex = ". $labes_exs . ";\n";?>
function formatResult(record) {


	return record.text;};
	function formatSelection(record) {
	return record.text;};
	var selex = '<?php echo $selex;?>';
	var melex = '<?php echo $multisel;?>';

$(document).ready(function() {
	"use strict";
	//Init event
	$("#create_td").append(melex);

	$('#fields').select2({
		dir: 'ltr',

	});


	$('#fields + .select2').addClass(' widthcentpercentminusx');

	$("#create_td > select ").attr("onchange","select_camp()");

	$("#addline").on('click', function(){
		CheckLine();
	});

	$("#savelinebutton").on('click', function(){
		CheckLine();
	});


	//==========> view lines <===========
		var Trs = document.querySelectorAll(".listrules");

		//console.log(Trs);

		for(var Tr of Trs){
			var Tds = Tr.querySelectorAll("td");

			var io = 0;
			for(var Td of Tds){


				if(io == 1 || io == 3)
				{
					var input = Td.querySelector("input");
					var camps = input.value.split(',');
					var ia = 0;
					for(var camp of camps)
					{
						if(ia>0)
							$(Td).append(', ');
						$(Td).append(labes_ex[camp]);
						ia++;
					}
				}
				if(io == 5)
				{
					var input = Td.querySelector("input");
					var camps = input.value.split('|');
					//$( "#myselect option:selected" ).text();
					var tx = $( "#options_"+camps[0]+" > option[value='"+camps[1]+"']").text();
					//console.log("#options_"+camps[0]+" > option[value='"+camps[1]+"']");
					$(Td).append(tx);

				}
				/*if(io == 1)
				{
					var input = Td.querySelector("input");
					var camps = input.value.split(',');
					for(camp of camps)
					{
						options_
					}
				}*/
				io++;
				//console.log(input);
			}
		}
		$(".listrules").show();
	//==========> view lines <===========

	//==========> edit line <============
	var Tdes = document.querySelectorAll(".edittlinetr > td");

	io = 0;
	for(var Tde of Tdes)
	{
		if(io == 1)
		{

			var inpute = Tde.querySelector("input");

			var camps = inpute.value.split(',');
			var ia = 0;
			//console.log(camps);
			//for(camp of camps)
			//{
				$("#fields").val(camps);
			//}
		}
		if(io == 3)
		{
			var inpute = Tde.querySelector("input");
			select_camp();
			$("#vale").val(inpute.value);
			var inputer = $("#valore_td > input");

			select_value();
			//console.log($(inputer).val());
			$("#vali").val($(inputer).val());
		}
		io++;
	}
	$('#fields').select2({
		dir: 'ltr',

	});

	<?php if($action != 'editline' ){?>
		select_camp();
	<?php } ?>

	$('#fields + .select2').addClass(' widthcentpercentminusx');

	$("#create_td > select ").attr("onchange","select_camp()");
	//==========> edit line <============


	<?php if($action == 'editline' && $lineid != "" )
	{?>
	/*setTimeout(function(){
	$("#fields").focus();
	var scrollDiv = document.getElementById("qualitysheet").offsetTop;
	window.scrollTo({ top: scrollDiv-70, behavior: 'smooth'});
}, 300);*/
	//document.getElementById("qualitysheet").scrollIntoView();
	<?php } ?>

});

/*value_rel = function(val)
{
	$("input[id='options_'"+val+""]").each(function(i, obj) {
		if($(this).text() === opcion ){
			repetido = 1;
		}
	});
}*/

select_camp = function()
{
	var vals_ = $("#fields").val();
	var acc_ = $("#accione").val();
	if(acc_ != "1")
	{
		//console.log(acc_);
		if(vals_.length){

			$("#padre_td").html(selex);


			for(var val_ of vals_)
			{
				$('#padre_td > select > option[value="'+val_+'"]').remove();
			}
			$("#padre_td > select ").attr("id","vale");
			$("#padre_td > select ").attr("name","vale");
			$("#padre_td > select ").attr("onchange","select_value()");
			//console.log($('#padre_td > select > option[value="'+val_+'"]'));
		}
		else {
			$("#padre_td").html("");
			$("#valore_td").html('');
		}
		$("#condicion").show();
	}
	else {
		$("#padre_td").html("");
		$("#condicion").hide();
		$("#vali").hide();
	}
}

select_value = function()
{
	var val_ = $("#vale").val();
	if(val_!=""){
		$("#valore_td").html('<select class="flat minwidth100 maxwidthonsmartphone" id="vali" name="vali" tabindex="-1" aria-hidden="true">'+$("#options_"+val_).html())+"</select>";


	}
	else {
		$("#valore_td").html('');
	}

}

select_condi = function()
{
	var val_ = $("#condicion").val();
	if(val_==3){
		$("#vali").hide();

	}
}

CheckLine = function()
{
	var fail=0;

	var campo = "";
	var campe = "";
	var campi = "";

	if($("#fields").length >0 )
	{
		campo = $("#fields").val();
	}

	if($("#vale").length >0 )
		campe = $("#vale").val();

	if($("#vali").length >0 )
		campi = $("#vali").val();

//alert("||"+campo+"||");
	var acc_ = $("#accione").val();
	if(campo == "" || ((campe == "" || campi == "") && acc_ != 1))
	{
		alert("<?php echo $langs->trans("llene_todos")?>");
	}
	else
	{
		//alert("exito");
		if($("#addline").length > 0)
		{
			$("#addline").attr("type",'submit');
			$("#addline").attr("id",'addline2');
			$("#addline").click();
		}
		if($("#savelinebutton").length > 0)
		{
			$("#savelinebutton").attr("type",'submit');
			$("#savelinebutton").attr("id",'savelinebutton2');
			$("#savelinebutton").click();
		}

	}
}
</script>
<?php
$db->close();
