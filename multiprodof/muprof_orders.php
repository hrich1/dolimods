<?php
/* Copyright (C) 2019-2020 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       mo_production.php
 *		\ingroup    multiprodof
 *		\brief      Page to make production on a MO
 */

// Load Dolibarr environment
require '../main.inc.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/productlot.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';
require_once DOL_DOCUMENT_ROOT.'/bom/class/bom.class.php';
dol_include_once('/multiprodof/class/muprof.class.php');
dol_include_once('/multiprodof/lib/multiprodof_muprof.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("multiprodof", "stocks", "other", "productbatch"));

// Get parameters
$id = GETPOST('id', 'int');
$ref        = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm    = GETPOST('confirm', 'alpha');
$cancel     = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ?GETPOST('contextpage', 'aZ') : 'mocard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
//$lineid   = GETPOST('lineid', 'int');

$collapse = GETPOST('collapse', 'aZ09comma');

// Initialize technical objects
$object = new MuPrOf($db);

$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->multiprodof->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('mocard', 'globalcard')); // Note that conf->hooks_muprofdules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');

// Initialize array of search criterias
$search_all = GETPOST("search_all", 'alpha');
$search = array();
foreach ($object->fields as $key => $val)
{
	if (GETPOST('search_'.$key, 'alpha')) $search[$key] = GETPOST('search_'.$key, 'alpha');
}

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
$permissiontodelete = $user->rights->multiprodof->muprof->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
$upload_dir = $conf->multiprodof->multidir_output[isset($object->entity) ? $object->entity : 1];

$permissiontoproduce = $permissiontoadd;


/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	$error = 0;

	$backurlforlist = dol_buildpath('/multiprodof/muprof_list.php', 1);

	if (empty($backtopage) || ($cancel && empty($id))) {
		//var_dump($backurlforlist);exit;
		if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) $backtopage = $backurlforlist;
		else $backtopage = DOL_URL_ROOT.'/multiprodof/muprof_orders.php?id='.($id > 0 ? $id : '__ID__');
	}
	
	//$triggermodname = 'MULTIPRODOF_MUPROF_MODIFY'; // Name of trigger action code to execute when we modify record
	
	
	if ($action == 'confirm_neworder' && $confirm == 'yes' && !empty($permissiontoadd))
	{
		$selec_produt = GETPOST('selec_produt', 'array');
		$selec_produtall = GETPOST('selec_produtall', 'array');
		$fororder = GETPOST('fororder', 'array');
		$resor = $object->CreateOrder($user, $selec_produt, $selec_produtall, $fororder, false);
	}
	
	/*if ($action == 'confirm_addproductionline' && GETPOST('addproductionlinebutton')) {
		
		
		$moline = new MuPrOfLine($db);
		$moline->setproductionslines($user, false);
		header("Location: ".$_SERVER["PHP_SELF"].'?id='.$object->id);
		exit;
	}*/
	
	

}



/*
 * View
 */


$formproduct = new FormProduct($db);
$tmpwarehouse = new Entrepot($db);
$tmpbatch = new Productlot($db);

$help_url = 'EN:Module_Manufacturing_Orders|FR:Module_Ordres_de_Fabrication';
llxHeader('', $langs->trans('MuPrOf'), $help_url, '', 0, 0, array('/multiprodof/js/lib_dispatch.js.php'));

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{

	$head = muprofPrepareHead($object);

	print dol_get_fiche_head($head, 'orders', $langs->trans("orders"), -1, $object->picto);
	

	$formconfirm = '';


	print '<div class="fichecenter">';
		
	print '<form method="POST" name="form1" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="confirm_neworder">';
	print '<input type="hidden" name="confirm" value="yes">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$id.'">';
		
		
	print'<!--Start fiche for toproduce: '.$line->id.'-->';
	print '<div class="clearboth"></div>';
	
	print '<table width="100%">';
	print '<tr>';
	print '<td>';	;			
	print load_fiche_titre($langs->trans('GenerateOrdesProduction'), '', '', 0, '', '', $newlinetext);
	print '</td>';
	print '<td class="right">';
	
	if ($permissiontoadd)
	{
		//if ($object->status == $object::STATUS_PRODUCED)
		//{
			if(trim($object->fk_soc)!="")
			{
				print '<a class="butAction" href="javascript:GenerateOrder()">'.$langs->trans("CreateOrder").'</a>'."\n";
			}
			else {
				print '<a class="butAction" href="javascript:alert(\''.$langs->trans("Please_add_a_client").'\')">'.$langs->trans("CreateOrder").'</a>'."\n";
			}
		//}
	}
	
	print '</td></tr></table>';
	print '<div class="div-table-responsive-no-min">';
	print '<table width="100%" class="noborder noshadow nobottom centpercent">';
			
	print '<tr class="liste_titre">';
	
	print '<td><input class="selec_produt" id="CheckAll"  type="checkbox" onclick="CheckedAll()" ></td>';
	print '<td>'.$langs->trans("Product").'</td>';
	print '<td class="right">'.$langs->trans("QtyToProduce").'</td>';
	print '<td class="right">'.$langs->trans("QtyAlreadyProduced").'</td>';
	print '<td class="right">'.$langs->trans("QtyInOrder").'</td>';
	print '<td class="right">'.$langs->trans("QtyPending").'</td>';

	print '</tr>';




		if (!empty($object->lines))
		{
			$nblinetoproduce = 0;
			foreach ($object->lines as $line) {
				if ($line->role == 'toproduce') {
					$nblinetoproduce++;
				}
			}

			$nblinetoproducecursor = 0;
			$lns = 0;
			foreach ($object->lines as $line) {
				
				if ($line->role == 'toproduce')
				{
					
					//Verify if line is already produced
					$arrayoflines = $object->fetchLinesLinked('produced', $line->id);
					$alreadyproduced = 0;
					foreach ($arrayoflines as $line2) {
						$alreadyproduced += $line2['qty'];
					}
					//Verify if line is already produced
					
					if($line2['qty']>0)
					{
						$i = 1;
						
						$father = $line->id;
	
						$nblinetoproducecursor++;
	
						$tmpproduct = new Product($db);
						$tmpproduct->fetch($line->fk_product);
	
	
						$suffix = '_'.$line->id;
						print '<!-- Line to dispatch '.$suffix.' -->'."\n";

						print '<tr id="tr'.$lns.'">';
	
						print '<td><input class="selec_produt" id="selec_produt'.$lns.'" name = "selec_produt[]" '.$che.' type="checkbox" value="'.$line->id.'">
						<input name = "selec_produtall[]" type="hidden" value="'.$line->id.'">
						<input id = "line'.$line->id.'" type="hidden" value="'.$lns.'">
						</td>';
	
						print '<td>'.$tmpproduct->getNomUrl(1);
						print '<br><span class="opacitymedium small">'.$tmpproduct->label.'</span>';
						print '</td>';
						print '<td class="right">'.$line->qty.'</td>';
						print '<td class="right nowraponall">';
						print $alreadyproduced;
						print '<input type="hidden" value="'.$alreadyproduced.'" id="produced'.$lns.'" />';
						print '</td>';
						$InOrd = $object->CountLinesInOrder(array($line->id));
						$InR = $InOrd[$line->id] == "Error" ? 0 : $InOrd[$line->id]; 
						print '<td class="right">'.$InR;
						print '<input type="hidden" value="'.$InR.'" id="inorder'.$lns.'" />';
						print '</td>';
						$Ped = $alreadyproduced - $InR;
						print '<td class="right"><input type="text" class="flat maxwidth75imp" name="fororder[]" id="fororder'.$lns.'" value="'.$Ped.'"></td>';
						
						print '</tr>';

	
						$lns++;
					}
				}
			
			}
		}

		print '</table>';
		print '</div>';
		print '<input type="hidden" value="'.$lns.'" id="rows" />';
		
		print "</form>\n";

		print '</div>';


?>
<script>
GenerateOrder = function()
{
	var rows = Number($("#rows").val());
	var vl = 0;
	var pedf = 0;
	var peds = [];
	for(var i=0; i<=rows; i++)
	{
		if($("#selec_produt"+i).length > 0)
		{
			var pending = Number($('#produced'+i).val())-Number($('#inorder'+i).val());
			var ck = $('#selec_produt'+i).is(':checked');
			if(ck && Number($('#fororder'+i).val()) > 0)
			{
				if(Number($('#fororder'+i).val()) > pending)//Las unidades a pedir rebasan lo pendiente
				{
					$("#tr"+i+" td").delay(3000).css("background-color","#ff7f7f");
					pedf++;
				}
				peds.push($('#selec_produt'+i).val())
				vl++;
			}
			else{
				if(ck)
				{
					$("#tr"+i+" td").delay(3000).css("background-color","#ff7f7f");
					pedf++;
				}
			}
		}
	}
	
	if(vl==0)
	{

		alert("<?php echo $langs->trans("Plis_select_lines_to_order");?>");
	}
	else if(pedf>0)
	{
		alert("<?php echo $langs->trans("Marked_lines_exceed");?>");
	}
	else
	{
		RevalidateMuprof(peds);
		//form1.submit();
		//window.location.href = "<? echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=confirm_neworder&confirm=yes'?>";
		
		
	}
	//
}

RevalidateMuprof = function (peds)
{
		$.ajax({
		// http method
		type: 'POST',
		url: 'ajax/muprof_ajax.php',
	    data : {
	    	action: "RevalidateMuprof",
	    	id : "<?php echo $id;?>",
	    	peds : peds
	    },
	    success: function (data, status, xhr) {
	    	var doc = JSON.parse(data);
	    	console.log(doc);
	    	if(doc.Res=="Success")
	    	{
	    		if(typeof doc.V != "undefined")
       			{
	       			var fails = 0;
	       			var pas = 0;
       				//console.log("valido");
       				for(lin in doc.V)
       				{
	       				var ln = Number($("#line"+lin).val());
	       				var prod = Number($("#produced"+ln).val());
	       				var forord = Number($("#fororder"+ln).val());
	       				var inord = Number(doc.V[lin]);
	       				
	       				var totord = forord+inord;
	       				
	       				if(totord>prod)
	       				{
		       				//$("#tr"+ln).css("background-color", "#FFBDBD").show(1500);
		       				$("#tr"+ln+" td").delay(3000).css("background-color","#ff7f7f");
		       				fails++;
	       				}
	       				pas++;
       				}
       				if(fails>0)
       				{
	       				alert("<?php echo $langs->trans("Marked_lines_exceed_reload");?>");
       				}
       				else
       				{
	       				if(pas>0)
	       				{
		       				form1.submit();
	       				}
       				}
       			}
       			else
	   			{
	       		alert("<?php echo $langs->trans("Error_contact_support");?>");
       			}
       		}
       		else
       		{
	       		alert("<?php echo $langs->trans("Error_try_again");?>");
       		}
	    	
	        
	    },
	    error: function (jqXhr, textStatus, errorMessage) {
	           console.log('Error' + errorMessage);
	    }
		});
}

CheckedAll = function()
{
	var ck = $('#CheckAll').is(':checked');
	var rows = Number($("#rows").val());
	for(var i=0; i<=rows; i++)
	{
		if($("#selec_produt"+i).length > 0)
		{
			$('#selec_produt'+i).attr( "checked", ck);
		}
	}
}
	//'.
</script>
<?php
}?>

<?php
// End of page
llxFooter();
$db->close();
