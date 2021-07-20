<?php
/* Copyright (C) 2019 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   	\file       muprof_quality.php
 *		\ingroup    multiprodof
 *		\brief      Page to show quality_of_the_piece of a MUPROF
 */

// Load Dolibarr environment
require '../main.inc.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
dol_include_once('/multiprodof/quality/class/quality.class.php');
dol_include_once('/multiprodof/class/muprof.class.php');
dol_include_once('/multiprodof/lib/multiprodof_muprof.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("multiprodof", "stocks", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref        = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm    = GETPOST('confirm', 'alpha');
$cancel     = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ?GETPOST('contextpage', 'aZ') : 'muprofquality'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$lineid   = GETPOST('lineid', 'int');



// Initialize technical objects
$object = new MuPrOf($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->multiprodof->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('muprofquality', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);



if (empty($action) && empty($id) && empty($ref)) $action = 'view';

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

// Security check - Protection if external user
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
$isdraft = (($object->status == $object::STATUS_DRAFT) ? 1 : 0);
$result = restrictedArea($user, 'multiprodof', $object->id, 'multiprodof_muprof', '', 'fk_soc', 'rowid', $isdraft);


$permissionnote = $user->rights->multiprodof->muprof->write; // Used by the include of actions_setnotes.inc.php
$permissiondellink = $user->rights->multiprodof->muprof->write; // Used by the include of actions_dellink.inc.php
$permissiontoadd = $user->rights->multiprodof->muprof->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
$permissiontoaddquality = $user->rights->multiprodof->muprof->quality;
$permissiontodelete = $user->rights->multiprodof->muprof->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
$upload_dir = $conf->multiprodof->muprof->multidir_output[isset($object->entity) ? $object->entity : 1];

$permissiontoproduce = $permissiontoadd;


/*
 * Actions
 */
 
	

if (empty($reshook))
{
	$error = 0;

	$backurlforlist = dol_buildpath('/multiprodof/muprof_list.php', 1);

	if (empty($backtopage) || ($cancel && empty($id))) {
		//var_dump($backurlforlist);exit;
		if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) $backtopage = $backurlforlist;
		else $backtopage = DOL_URL_ROOT.'/multiprodof/muprof_quality.php?id='.($id > 0 ? $id : '__ID__');
	}
	$triggermodname = 'MULTIPRODOF_MUPROF_MODIFY'; // Name of trigger action code to execute when we modify record
	
	if($action == 'update')
	{
		//Only viewa and edit fields for quality
		$object->fields['temper']['visible']=1;
		$object->fields['water_circulates']['visible']=1;
		$object->fields['rack_in_temp']['visible']=1;
		$object->fields['ejector_safety']['visible']=1;
		$object->fields['mold_cleaning']['visible']=1;
		$object->fields['dater']['visible']=1;
		$object->fields['notes']['visible']=1;
		
		
		if($object->fk_user_quality=="")
		{
			$object->fields['fk_user_quality']['visible']=1;
			$object->fk_user_quality = $user->id;
		}
		if($object->signature=="")
		{
			$object->fields['signature']['visible']=1;
			$now = dol_now();
			$object->signature = $object->db->idate($now);
		}
		// Actions cancel, add, update, update_extras, confirm_validate, confirm_delete, confirm_deleteline, confirm_clone, confirm_close, confirm_setdraft, confirm_reopen
		include DOL_DOCUMENT_ROOT.'/core/actions_addupdatedelete.inc.php';
	}
	else {
		// Actions cancel, add, update, delete or clone quality object
		include DOL_DOCUMENT_ROOT.'/multiprodof/quality/class/actions_quality.class.php';
	}
	// Actions when linking object each other
	include DOL_DOCUMENT_ROOT.'/core/actions_dellink.inc.php';

	// Actions when printing a doc from card
	include DOL_DOCUMENT_ROOT.'/core/actions_printing.inc.php';

	// Actions to send emails
	$triggersendname = 'MUPROF_SENTBYMAIL';
	$autocopy = 'MAIN_MAIL_AUTOCOPY_MUPROF_TO';
	$trackid = 'muprof'.$object->id;
	include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';

	// Action to move up and down lines of object
	//include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';	// Must be include, not include_once

	if ($action == 'set_thirdparty' && $permissiontoadd)
	{
		$object->setValueFrom('fk_soc', GETPOST('fk_soc', 'int'), '', '', 'date', '', $user, 'MUPROF_MODIFY');
	}
	if ($action == 'classin' && $permissiontoadd)
	{
		$object->setProject(GETPOST('projectid', 'int'));
	}

	if ($action == 'confirm_reopen') {
		$result = $object->setStatut($object::STATUS_INPROGRESS, 0, '', 'MULTIPRODOF_REOPEN');
	}
}



/*
 * View
 */

$form = new Form($db);
$formproject = new FormProjets($db);
$warehousestatic = new Entrepot($db);
$userstatic = new User($db);

llxHeader('', $langs->trans('MuPrOf'), '');

// Part to edit record
if (($id || $ref) && $action == 'editquality')
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
}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'editquality')))
{
	$res = $object->fetch_thirdparty();
	$res = $object->fetch_optionals();

	$head = muprofPrepareHead($object);

	print dol_get_fiche_head($head, 'quality', $langs->trans("ManufacturingOrder"), -1, $object->picto);

	$formconfirm = '';

	// Confirmation to delete
	if ($action == 'delete')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteMuPrOf'), $langs->trans('ConfirmDeleteMuPrOf'), 'confirm_delete', '', 0, 1);
	}
	// Confirmation to delete line
	if ($action == 'deleteline')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_deleteline', '', 0, 1);
	}
	// Clone confirmation
	if ($action == 'clone') {
		// Create an array for form
		$formquestion = array();
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneMuPrOf', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
	}

	// Confirmation of action xxxx
	if ($action == 'xxx')
	{
		$formquestion = array();
		/*
		$forcecombo=0;
		if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
	    $formquestion = array(
	        // 'text' => $langs->trans("ConfirmClone"),
	        // array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value' => 1),
	        // array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"), 'value' => 1),
	        // array('type' => 'other',    'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockDecrease"), 'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1, 0, 0, '', 0, $forcecombo))
        );
	    */
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('XXX'), $text, 'confirm_xxx', $formquestion, 0, 1, 220);
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

	$morehtmlref = '<div class="refidno">';
	/*
	// Ref bis
	$morehtmlref.=$form->editfieldkey("RefBis", 'ref_client', $object->ref_client, $object, $user->rights->multiprodof->creer, 'string', '', 0, 1);
	$morehtmlref.=$form->editfieldval("RefBis", 'ref_client', $object->ref_client, $object, $user->rights->multiprodof->creer, 'string', '', null, null, '', 1);*/
	// Thirdparty
	$morehtmlref .= $langs->trans('ThirdParty').' : '.(is_object($object->thirdparty) ? $object->thirdparty->getNomUrl(1) : '');
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


	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);


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
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_view.tpl.php';

	// Other attributes
	//include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_view.tpl.php';

	print '</table>';
	print '</div>';
	print '</div>';
	
	print '<div class="clearboth"></div>';
	
	print dol_get_fiche_end();
	
	// Modify
			if ($object->status != $object::STATUS_CANCELED) {
				if ($permissiontoaddquality)
				{
					print '<div class="tabsAction">';
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editquality">'.$langs->trans("Modify").'</a>';
					print '</div>';
				}
			}
	
		/*
	 * Lines
	 */
$Quality = new Quality($db);
$Quality->id = $object->id;
$Quality->fetchLines();
/*echo "<pre>";
print_r($Qualities);*/
if (!empty($Quality->table_element_line))
{
		print '<div id="qualitysheet" style=" width:80vw; overflow-x:auto !important;">';
		print '<style>table.liste th, table.noborder th, table.noborder tr.liste_titre td, table.noborder tr.box_titre td, table.liste td, table.noborder td, div.noborder form div, table.tableforservicepart1 td, table.tableforservicepart2 td {
    padding: 7px 2px 7px 2px !important;
} table.noborder{font-size: 0.80em;}</style>';
		print '	<form  name="addquality" id="addquality" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.(($action != 'editline') ? '#addline' : '').'" method="POST">
    	<input type="hidden" name="token" value="' . newToken().'">
    	<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addlinequality' : 'updatequality').'">
    	<input type="hidden" name="mode" value="">
    	<input type="hidden" name="id" value="' . $Quality->id.'">
		<input type="hidden" name="fk_muprof" value="' . $Quality->id.'">
		<input type="hidden" name="backtopage" value="'.$backtopage.'">';
		

		if (!empty($conf->use_javascript_ajax) &&  $object->status != MuPrOf::STATUS_CANCELED) {
			include DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php';
		}

			print '<table  id="tablelines" class="noborder noshadow" >';

			include(DOL_DOCUMENT_ROOT.'/multiprodof/quality/tpl/objectline_title.tpl.php');
			// Form to add new line
			if ($object->status != MuPrOf::STATUS_CANCELED && $permissiontoaddquality && $action != 'selectlines')
			{
				if ($action != 'editline')
				{
					// Add products/services form
					include(DOL_DOCUMENT_ROOT.'/multiprodof/quality/tpl/objectline_create.tpl.php');
	
					$parameters = array();
					$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
				}
			}
			
			foreach($Quality->lines as $line)
			{
				$coldisplay = 0;
				if($action == 'editline' && $line->id == $lineid )
				{
					
					include(DOL_DOCUMENT_ROOT.'/multiprodof/quality/tpl/objectline_edit.tpl.php');
				}
				else {
					//include(DOL_DOCUMENT_ROOT.'/multiprodof/quality/tpl/objectline_view.tpl.php');
				}
			}
			
			include(DOL_DOCUMENT_ROOT.'/multiprodof/quality/tpl/objectline_title.tpl.php');
			foreach($Quality->lines as $line)
			{
				$coldisplay = 0;
				if($action == 'editline' && $line->id == $lineid )
				{
					
					//include(DOL_DOCUMENT_ROOT.'/multiprodof/quality/tpl/objectline_edit.tpl.php');
				}
				else {
					include(DOL_DOCUMENT_ROOT.'/multiprodof/quality/tpl/objectline_view.tpl.php');
				}
			}

		

		if (!empty($Quality->lines) || ($object->status != MuPrOf::STATUS_CANCELED && $permissiontoaddquality && $action != 'selectlines' && $action != 'editline'))
		{
			print '</table>';
		}
		print '</div>';
		

		print "</form>\n";
		print "</div>";
	}


}?>
<style>
	.imcheck, .imcan{cursor:pointer}
</style>

<script>
$(document).ready(function() {
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
}
</script>
<?php

// End of page
llxFooter();
$db->close();
