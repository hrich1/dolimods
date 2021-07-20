<?php
/* Copyright (C) 2021 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    sinprecio/class/actions_sinprecio.class.php
 * \ingroup sinprecio
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class ActionsSinPrecio
 */
class ActionsSinPrecio
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Execute action
	 *
	 * @param	array			$parameters		Array of parameters
	 * @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string			$action      	'add', 'update', 'view'
	 * @return	int         					<0 if KO,
	 *                           				=0 if OK but we want to process standard actions too,
	 *                            				>0 if OK and we want to replace standard actions.
	 */
	public function getNomUrl($parameters, &$object, &$action)
	{
		global $db, $langs, $conf, $user;
		$this->resprints = '';
		return 0;
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	/*public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/ * print_r($parameters); print_r($object); echo "action: " . $action; * /
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2')))	    // do something only for the context 'somecontext1' or 'somecontext2'
		{
			// Do what you want here...
			// You can for example call global vars like $fieldstosearchall to overwrite them, or update database depending on $action and $_POST values.
		}

		if (!$error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}*/


	/**
	 * Overloading the doMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2')))		// do something only for the context 'somecontext1' or 'somecontext2'
		{
			foreach ($parameters['toselect'] as $objectid)
			{
				// Do action on each object id
			}
		}

		if (!$error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	/**
	 * Overloading the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2')))		// do something only for the context 'somecontext1' or 'somecontext2'
		{
			$this->resprints = '<option value="0"'.($disabled ? ' disabled="disabled"' : '').'>'.$langs->trans("SinPrecioMassAction").'</option>';
		}

		if (!$error) {
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}



	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$object		   	Object output on PDF
	 * @param   string	$action     	'add', 'update', 'view'
	 * @return  int 		        	<0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
	 */
	public function beforePDFCreation($parameters, &$object, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0; $deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2')))		// do something only for the context 'somecontext1' or 'somecontext2'
		{
		}

		return $ret;
	}

	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$pdfhandler     PDF builder handler
	 * @param   string	$action         'add', 'update', 'view'
	 * @return  int 		            <0 if KO,
	 *                                  =0 if OK but we want to process standard actions too,
	 *                                  >0 if OK and we want to replace standard actions.
	 */
	public function afterPDFCreation($parameters, &$pdfhandler, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0; $deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {
			// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}



	/**
	 * Overloading the loadDataForCustomReports function : returns data to complete the customreport tool
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function loadDataForCustomReports($parameters, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$langs->load("sinprecio@sinprecio");

		$this->results = array();

		$head = array();
		$h = 0;

		if ($parameters['tabfamily'] == 'sinprecio') {
			$head[$h][0] = dol_buildpath('/module/index.php', 1);
			$head[$h][1] = $langs->trans("Home");
			$head[$h][2] = 'home';
			$h++;

			$this->results['title'] = $langs->trans("SinPrecio");
			$this->results['picto'] = 'sinprecio@sinprecio';
		}

		$head[$h][0] = 'customreports.php?objecttype='.$parameters['objecttype'].(empty($parameters['tabfamily']) ? '' : '&tabfamily='.$parameters['tabfamily']);
		$head[$h][1] = $langs->trans("CustomReports");
		$head[$h][2] = 'customreports';

		$this->results['head'] = $head;

		return 1;
	}



	/**
	 * Overloading the restrictedArea function : check permission on an object
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int 		      			  	<0 if KO,
	 *                          				=0 if OK but we want to process standard actions too,
	 *  	                            		>0 if OK and we want to replace standard actions.
	 */
	public function restrictedArea($parameters, &$action, $hookmanager)
	{
		global $user;

		if ($parameters['features'] == 'myobject') {
			if ($user->rights->sinprecio->myobject->read) {
				$this->results['result'] = 1;
				return 1;
			} else {
				$this->results['result'] = 0;
				return 1;
			}
		}

		return 0;
	}

	/* Add here any other hooked methods... */
		/**
	 * Overloading the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;
		$script = "";
		/*echo "<pre>";
		$currurl = $_SERVER['PHP_SELF'];
		//echo $currurl;
		 print_r($parameters); print_r($object); echo "action: " . $action;		
		 exit;*/
		
		//thirdpartycard = societe/card.php, thirdpartycontact = societe/contact.php, projectthirdparty = societe/project.php, consumptionthirdparty = societe/consumption.php, thirdpartyticket = ticket/list.php, thirdpartynotification = societe/notify/card.php, thirdpartynote = societe/note.php,agendathirdparty = societe/agenda.php
		if(in_array('thirdpartycard', explode(':', $parameters['context'])) || in_array('thirdpartycontact', explode(':', $parameters['context'])) || in_array('projectthirdparty', explode(':', $parameters['context'])) || in_array('consumptionthirdparty', explode(':', $parameters['context'])) || in_array('thirdpartyticket', explode(':', $parameters['context'])) || in_array('thirdpartynotification', explode(':', $parameters['context'])) || in_array('thirdpartynote', explode(':', $parameters['context'])) || in_array('agendathirdparty', explode(':', $parameters['context'])))// do something only for the context 'somecontext1' or 'somecontext2'
		{
			if($user->rights->sinprecio->myobject->price == 1)
			{
				
				$script='<style> #rib {display:none}; </style>';
				echo $script;
				$error = 1;
			}
			else
			{
				$error = 1;
			}

		}

		else
		{
			$error = 1;
		}

		if (!$error) {
			return 1; // or return 1 to replace standard code
		} 
	}

	public function printFieldPreListTitle($parameters, &$object, &$action, $hookmanager) //commande/list.php
	{
		global $conf, $user, $langs, $arrayfields;

		$error = 0; // Error counter
		$disabled = 1;
		/*echo "<pre>";
		 print_r($parameters); //print_r($object); echo "action: " . $action;
		exit;*/
		if(in_array('productservicelist', explode(':', $parameters['context'])))//products list in products/list.php
		{
			if($user->rights->sinprecio->myobject->price == 1)
				unset($arrayfields['p.sellprice']);

		}
		elseif(in_array('movementlist', explode(':', $parameters['context'])))// stock product/stock/list.php
		{
			if($user->rights->sinprecio->myobject->price == 1)
			{
				$arrayfields['m.value']['checked']=0;
				$arrayfields['m.price']['checked']=0;
			}

		}
		elseif(in_array('stocklist', explode(':', $parameters['context'])))// stock product/stock/list.php
		{
			if($user->rights->sinprecio->myobject->price == 1)
			{
				$arrayfields['estimatedvalue']['checked']=0;
				$arrayfields['estimatedstockvaluesell']['checked']=0;
			}

		}
		elseif(in_array('orderlist', explode(':', $parameters['context'])))// orders table details commande/list.php
		{
			 if($user->rights->sinprecio->myobject->price == 1)
			 {
				/*$arrayfields['c.total_ht']['checked'] = 0;
				$arrayfields['c.total_vat']['checked'] = 0;
				$arrayfields['c.total_ttc']['checked'] = 0;*/
				unset($arrayfields['c.total_ht']);
				unset($arrayfields['c.total_vat']);
				unset($arrayfields['c.total_ttc']);
				 $script='<style>.classfortooltip{display:none}</style><script>window.addEventListener("load", function(event) {if($(".classfortooltip")){$(".classfortooltip").attr( "title", "");  if($(".classfortooltip")){$(".classfortooltip").show();
				 
					/*document.getElementById("checkboxc.total_ht").style.display = "none";
					var parr = document.getElementById("checkboxc.total_ht").parentElement;
					parr.style.display="none";
					document.getElementById("checkboxc.total_vat").style.display = "none";
					var parr = document.getElementById("checkboxc.total_vat").parentElement;
					parr.style.display="none";
					document.getElementById("checkboxc.total_ttc").style.display = "none";
					var parr = document.getElementById("checkboxc.total_ttc").parentElement;
					parr.style.display="none";*/
				}}})</script>';
				 
				 echo $script;
			 }
 
		}
		else
		{
			$error = 1;
		}

		

		if (!$error) {
			return 1; // or return 1 to replace standard code
		}
	}
	/*public function printFieldListTitle($parameters, &$object, &$action, $hookmanager) //commande/list.php
	{
		global $conf, $user, $langs, $arrayfields;

		$error = 0; // Error counter
		$disabled = 1;

		if(in_array('orderlist', explode(':', $parameters['context'])))// orders table details commande/list.php
		{
			 //echo "hollla";
			// exit;
			 if($user->rights->sinprecio->myobject->price == 1)
			 {

				unset($arrayfields['c.total_ht']);
				unset($arrayfields['c.total_vat']);
				unset($arrayfields['c.total_ttc']);
				 $script='<style>.classfortooltip{display:none}</style><script>window.addEventListener("load", function(event) {if($(".classfortooltip")){$(".classfortooltip").attr( "title", "");  if($(".classfortooltip")){$(".classfortooltip").show();
					document.getElementById("checkboxc.total_ht").style.display = "none";
					var parr = document.getElementById("checkboxc.total_ht").parentElement;
					parr.style.display="none";
					document.getElementById("checkboxc.total_vat").style.display = "none";
					var parr = document.getElementById("checkboxc.total_vat").parentElement;
					parr.style.display="none";
					document.getElementById("checkboxc.total_ttc").style.display = "none";
					var parr = document.getElementById("checkboxc.total_ttc").parentElement;
					parr.style.display="none";
				}}})</script>';
				 
				 echo $script;
			 }
 
		}
		else
		{
			$error = 1;
		}

		

		if (!$error) {
			return 1; // or return 1 to replace standard code
		}
	}*/
	public function printFieldListWhere($parameters, &$object, &$action, $hookmanager) //products/index.php
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;
		//echo "<pre>";
		 //print_r($parameters); print_r($object); echo "action: " . $action;
		 if(in_array('productindex', explode(':', $parameters['context'])))
		{

			if($user->rights->sinprecio->myobject->price == 1)
			{
				if(empty($conf->global->PRODUIT_MULTIPRICES))
						$conf->global->PRODUIT_MULTIPRICES = 1;
			}

		}
		else
		{
			$error = 1;
		}

		

		if (!$error) {
			return 1; // or return 1 to replace standard code
		}
	}
	public function printTabsHead($parameters, &$object, &$action, $hookmanager) //tabs head en products card.php htdocs/core/lib/functions.lib.php
	{
		global $conf, $user, $langs, $form;

		$error = 0; // Error counter
		$disabled = 1;
		$script ="";
		//echo "<pre>".
		//print_r($parameters); print_r($object); echo "action: " . $action;
		//exit;
		$context = explode(':', $parameters['context']);
		$currurl = $_SERVER['PHP_SELF'];
		if((in_array('productcard',  $context)|| in_array('productstatsinvoice',  $context)|| in_array('productdocuments',  $context) || in_array('pricesuppliercard',  $context) || in_array('agendathirdparty',  $context) || in_array('stockproductcard',  $context)|| strpos($currurl,"product/stats/card.php")!= false || strpos($currurl,"product/note.php")!= false ) && $user->rights->sinprecio->myobject->price == 1)
		//if (in_array($parameters['currentcontext'], array('productcard', 'productstatsinvoice', 'main')))		// do something only for the context 'somecontext1' or 'somecontext2'
		{

			
				$script="<script>if($('#pricve')){ $('#price').hide() }</script>";
				$this->resprints= $parameters['out'].$script;
			
			if(in_array('stockproductcard',  $context))//product/stock/product.php
			{
				$titllV = array();
				$titllV[] = $langs->trans("SellingPrice");
				$titllV[] = $langs->trans("MinPrice");
				$titllH = array();
				$titllH[] = $langs->trans("AverageUnitPricePMPShort");
				$titllH[] = $langs->trans("EstimatedStockValueShort");
				$titllH[] = $langs->trans("SellPriceMin");
				$titllH[] = $langs->trans("EstimatedStockValueSellShort");

				$js_array = json_encode($titllV);
				$script =  "<script>var titllV = ". $js_array . "; //console.log(titllV);</script>";

				$js_array = json_encode($titllH);
				$script .=  "<script>var titllH = ". $js_array . "; //console.log(titllH);</script>";

				$script.='
				<style>.fichehalfleft, .div-table-responsive{display:none}</style>
				<script>
				window.addEventListener("load", function(event) {var hidespan = -1; 
					if($(".fichehalfleft")){
						var Boxpr = document.querySelector(".fichehalfleft");var Boxxtd = Boxpr.querySelectorAll("tr"); for(Box_ in Boxxtd){ 
					
					for(var tit in titllV)
					{
						var Title = $("<textarea />").html(titllV[tit]).text();
						if((Boxxtd[Box_]).innerHTML)
						{
							if((Boxxtd[Box_]).innerHTML.indexOf(Title)>0)
							{
								(Boxxtd[Box_]).style.display="none";
								//(Boxxtd[Box_]);
							}
						}
					}
					
				
				}}
				if($(".div-table-responsive")){
					var Boxpr = document.querySelector(".div-table-responsive>table:first-child");var Boxxtd = Boxpr.querySelectorAll("tr:first-child > td");
					var Boxxtr = Boxpr.querySelectorAll("tr"); 
					//console.log(Boxxtr);
					for(Box_ in Boxxtd){
						for(var tit in titllH)
						{
							var Title = $("<textarea />").html(titllH[tit]).text();
							if((Boxxtd[Box_]).innerHTML)
							{
								//console.log((Boxxtd[Box_]).innerHTML);
								if((Boxxtd[Box_]).innerHTML.indexOf(Title)>=0)
								{
									//(Boxxtd[Box_]).style.display="none";
									//console.log((Boxxtd[Box_]).innerHTML.indexOf(Title)+"===>"+(Boxxtd[Box_]).innerHTML+"=====>"+Title);
									//(Boxxtd[Box_]).style.display="none";

									for(var bbax of Boxxtr)
									{
										var chitd = bbax.querySelectorAll("td");
										if(chitd[Box_])
											chitd[Box_].style.display="none";
									}

								}
							}
						}
				}}
				
				
				$(".fichehalfleft").show();$(".div-table-responsive").show();}); </script>';

				echo $script;
			
			}
		}
		else
		{
			$error = 1;
		}

		

		if (!$error) {
			return 1; // or return 1 to replace standard code
		}
	}

	public function dashboardCommercials($parameters, &$object, &$action, $hookmanager) //Dasboards in comm/index.php
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;
		$script ="";
		//echo "<pre>".
		//print_r($parameters); //print_r($object); echo "action: " . $action;
		//exit;
		$context = explode(':', $parameters['context']);
		if(in_array($parameters['currentcontext'], array('commercialindex')))
		//if (in_array($parameters['currentcontext'], array('productcard', 'productstatsinvoice', 'main')))		// do something only for the context 'somecontext1' or 'somecontext2'
		{

			if($user->rights->sinprecio->myobject->price == 1)
			{
				$script='<script>if($(".tdamount")){$(".tdamount").hide();} if($(".liste_total")){$(".liste_total").hide();} if($(".classfortooltip")){$(".classfortooltip").attr( "title", "");}</script>';
			}
			echo $script;
		}
		else
		{
			$error = 1;
		}

		

		if (!$error) {
			return 1; // or return 1 to replace standard code
		}
	}
	
	public function formObjectOptions($parameters, &$object, &$action, $hookmanager)//wiew order commande/card.php
	{
		global $conf, $user, $langs;
		if(in_array('ordercard', explode(':', $parameters['context'])))// order view in commande/card.php
		{
			if($user->rights->sinprecio->myobject->price == 1)
			{
				$script='<style> .ficheaddleft table, .linecolht, .linecoluht, #price_ht {display:none}; </style>';
				echo $script;
				$error = 0;
			}

		}
	}

	public function formAddObjectLine($parameters, &$object, &$action, $hookmanager)//wiew order commande/card.php
	{
		global $conf, $user, $langs, $arrayfields, $form, $buyer;

		$error = 0; // Error counter
		$disabled = 1;
		$script = "";
//echo "<pre>";
		// print_r($parameters); print_r($object); echo "action: " . $action;		
		if(in_array('ordercard', explode(':', $parameters['context'])))// do something only for the context 'somecontext1' or 'somecontext2'
		{
			if($user->rights->sinprecio->myobject->price == 1)
			{

				$statustoshow = 1;
					
				$form->select_produits(GETPOST('idprod2'), 'idprod2', $filtertype, $conf->product->limit_size, $buyer->price_level, 1, 2, '', 1, array(), $buyer->id, '1', 0, 'maxwidth500', 1, '', GETPOST('combinations', 'array'));
			

				$script="<script>
				
				window.addEventListener('load', function(event) { document.getElementById('idprod2').style.display = 'none';document.getElementById('idprod').innerHTML= document.getElementById('idprod2').innerHTML; }); 

				</script>";
				echo $script;
				$error = 1;
			}

		}
		else
		{
			$error = 1;
		}

		if (!$error) {
			return 1; // or return 1 to replace standard code
		} 
	}

	public function printTopRightMenu($parameters, &$object, &$action, $hookmanager)//$hookmanager->executeHooks('printTopRightMenu' in main.inc.php
	{
		global $conf, $user, $langs, $file;

		$error = 0; // Error counter
		$disabled = 1;
		$script = "";
		$currurl = $_SERVER['PHP_SELF'];

		if (strpos($currurl,"commande/stats/index.php")!= false)
		{
			
			
			if($user->rights->sinprecio->myobject->price == 1)
			{
				
				$script='<style> canvas, .dolgraphtitle, .div-table-responsive-no-min > table > tbody > tr > td:nth-child(4), .div-table-responsive-no-min > table > tbody > tr > td:nth-child(6) {display:none !important}; </style>';
				echo $script;
				$error = 1;
			}

		}
		elseif (strpos($currurl,"societe/note.php")!= false || strpos($currurl,"societe/document.php")!= false)//continue of doActions hide tab #rib
		{
			
			
			if($user->rights->sinprecio->myobject->price == 1)
			{
				
				$script='<style> #rib {display:none}; </style>';
				echo $script;
				$error = 1;
			}

		}
		else
		{
			$error = 1;
		}

		if (!$error) {
			return 1; // or return 1 to replace standard code
		} 
	}
	public function addMoreBoxStatsCustomer($parameters, &$object, &$action, $hookmanager)//view un order tab Cliente potencial, comm/card.php
	{
		global $conf, $user, $langs, $file;

		$error = 0; // Error counter
		$disabled = 1;
		$script = "";
		$currurl = $_SERVER['PHP_SELF'];
		//echo "<pre>";
		// print_r($parameters); //print_r($object); echo "action: " . $action;
		 //exit;
		if(in_array('thirdpartycomm', explode(':', $parameters['context'])))
		{
			
			if($user->rights->sinprecio->myobject->price == 1)//=========comm/card.php
			{
				
				$script='<script>var hidespan = -1; if($(".boxtable")){var Boxx = document.querySelector(".boxtable");var Boxxa = Boxx.querySelectorAll("a"); for(Box_ in Boxxa){if(Boxxa[Box_].href){if((Boxxa[Box_].href).indexOf("/commande/list.php?socid=")>0){hidespan = Box_;} }} var Boxxs = Boxx.querySelectorAll(".boxstats"); Boxxs[hidespan].style.display = "none";window.addEventListener("load", function(event) {if($(".classfortooltip")){$(".classfortooltip").attr( "title", ""); $(".classfortooltip").show()}})}</script><style>.lastrecordtable > tbody  tr > td:nth-child(3) {display:none !important} .classfortooltip, #rib{display:none }</style>';
				$this->resprints= $script;
				$error = 1;
			}

		}
		else
		{
			$error = 1;
		}

		if (!$error) {
			return 1; // or return 1 to replace standard code
		} 
	}

	public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)//view un order tab Cliente potencial, comm/card.php
	{
		global $conf, $user, $langs, $file;

		$error = 0; // Error counter
		$disabled = 1;
		$script = "";
		$currurl = $_SERVER['PHP_SELF'];
		//echo "<pre>";
		// print_r($parameters); //print_r($object); echo "action: " . $action;
		 //exit;
		if(in_array('thirdpartycomm', explode(':', $parameters['context'])) || in_array('suppliercard', explode(':', $parameters['context'])))
		{
			
			if($user->rights->sinprecio->myobject->price == 1)//=========comm/card.php
			{
				
				$script='<script>var hidespan = []; if($(".boxtable")){var Boxx = document.querySelector(".boxtable");var Boxxa = Boxx.querySelectorAll("a"); console.log(Boxxa);for(Box_ in Boxxa){if(Boxxa[Box_].href){
				if(
				(Boxxa[Box_].href).indexOf("/fourn/commande/list.php?socid=")>0
				|| (Boxxa[Box_].href).indexOf("/supplier_proposal/list.php?socid=")>0
				|| (Boxxa[Box_].href).indexOf("/fourn/facture/list.php?socid=")>0
				|| (Boxxa[Box_].href).indexOf("/fourn/recap-fourn.php?socid=")>0
				)
				
				{hidespan.push(Box_);} }} var Boxxs = Boxx.querySelectorAll(".boxstats"); for(hidd_ in hidespan){Boxxs[hidd_].style.display = "none";}}</script><style>.lastrecordtable > tbody  tr > td:nth-child(3) {display:none !important} .classfortooltip, #rib{display:none }</style>';
				echo $script;
				$error = 1;
			}

		}
		else
		{
			$error = 1;
		}

		if (!$error) {
			return 1; // or return 1 to replace standard code
		} 
	}

	
}
