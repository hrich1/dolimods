<?php
/* Copyright (C) 2021 Massimo Papetti
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
 * \file    categoriarelas/class/actions_categoriarelas.class.php
 * \ingroup categoriarelas
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class ActionsCategoriaRelas
 */
class ActionsCategoriaRelas
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
	public function formConfirm($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs, $db;

		 //print_r($parameters); /*print_r($object); echo "action: " . $action;*/
		if (in_array($parameters['currentcontext'], array('bomcard'))) {	    // do something only for the context

			$objprod = new Product($db);
			$objprod->type = 0; // so test later to fill $usercancxxx is correct
			$extrafieldsp = new ExtraFields($db);
			// fetch optionals attributes and labels
			$extrafieldsp->fetch_name_optionals_label($objprod->table_element);
			$fieldsp =  $objprod->showOptionals($extrafieldsp, 'create', '');

			$fieldsp = str_replace("$(document).ready(function () {", "$(\"#campspro\").on('click', function() {", $fieldsp);

			$fieldsp = str_replace("</select>", "</select><br/><br/>", $fieldsp);


			$sql = 'SELECT  t1.rowid, ifnull(t2.pcs,"") as pcs,  ifnull(t2.xtra_camps,"") as xtra_camps';
			$sql .= ' FROM '.MAIN_DB_PREFIX.'bom_bomline as t1 ';
			$sql .= ' left join '.MAIN_DB_PREFIX.'bom_bomline_extrafields t2 ON t1.rowid = t2.fk_object ';
			$sql .= " where t1.fk_bom = '".$object->id."' ";

			$linesb = array();
			$resql = $db->query($sql);
			if ($resql) {
				$num = $db->num_rows($resql);

				$i = 0;
				while ($i < $num) {
					$obj = $db->fetch_object($resql);
					if ($obj) {
						$linesb[$obj->rowid] = array("0" => $obj->pcs, "1" => $obj->xtra_camps);
					}
					$i++;
				}
			}
			$linesb_x = json_encode($linesb);

			$script = '
				<script>
					var labes_ex ='.$linesb_x . ';
					//console.log(labes_ex);
					$.moveColumn = function (table, from, to, spec = "", comp = "=") {
						var rows = $("tr", table);
						//console.log(rows);
						var cols;
						rows.each(function() {
							var pass = true;
								if(spec!="")
								{
									var cant = 0;
									var classs = ($(this).attr("class")).split(" ");
									for(classse of classs)
									{
										if(comp=="=")
										{
											
											if(classse == spec)
											{
												cant++;
											}
											
											//console.log("|"+classse+"|====|"+spec+"|"+cant);
											
										}
										else
										{
											if(comp=="!=")
											{
												if(classse != spec)
												{
													cant ++;
												}
											}
										}
									}
									if(cant>0)
									{
										pass = true;
									}
									else
									{
										pass =false;
									}
								}
								//console.log("|"+classse+"|====>"+pass);
								if(pass)
								{
									cols = $(this).children("th, td");
									cols.eq(from).detach().insertBefore(cols.eq(to));
								}

							});
					}
					$( document ).ready(function() {
						var tablelin = $("#tablelines");
						var trlin =';
						if($num == 0)
						{
							$script.='$("#tablelines .liste_titre");';
						}
						else
						{
							$script.='$("#tablelines > thead > tr");';
						}
						
						$script.='var trblin = $("#tablelines > tbody > tr");
						var trelin = $("#tablelines > tbody > .tredited");

						//===============PIEZAS=================

							//====Creo el td de titulo

							var td_ = document.createElement("td");
							td_.innerHTML = "Pzs";
							td_.setAttribute("id", "td_pzs");';
							if($num == 0)
							{
								$script.='$(trlin).append(td_);';
							}
							else
							{
								$script.='trlin[0].append(td_);';
							}
							
							$script.='//====Creo el td de titulo

							//Para moverlo veo cuales su index
							var from = ($("#td_pzs").index());


							//===========>Para cada línea ya agregada le coloco suvalor traido de la BD y que está en el array labes_ex
							var ok = 0;
							for(trb of trblin)
							{
								';
							if($num == 0)
							{
								$script.='if(ok>0)
										{
											'; 
							}
								$script.='var idv = $(trb).attr("data-id");
								if(idv != undefined)
								{

									var valb = labes_ex[idv] != undefined ? labes_ex[idv][0] : "0";
									//console.log($(trb).attr("data-id"));
									var td1_ = document.createElement("td");
									td1_.innerHTML = valb;
									trb.append(td1_);

								}
								';
								if($num == 0)
								{
									$script.='}
									';
								}
								$script.='ok++;
							}
							//===========>Para cada línea ya agregada


							//===========>Para el agregar nueva línea y editar
							var td2_ = document.createElement("td");
							td2_.setAttribute("id", "td2_pzs");
							var inp = document.createElement("input");
							inp.setAttribute("class", "flat maxwidth100");
							inp.setAttribute("type","text");
							inp.setAttribute("name", "inp_pcs");
							inp.setAttribute("id", "inp_pcs");
							var valb = 0;';
							
							
							
							
							if($action=="editline")
							{
								$script.=' 	var linestr  = trelin.length;
											var idv = $("input[name=\'lineid\']").val();
											valb = labes_ex[idv] != undefined ? labes_ex[idv][0] : "0"
											inp.setAttribute("value", valb);
											td2_.append(inp);
											trelin[linestr-1].append(td2_);';
							}
							else
							{
								$script.='	var linestr  = trblin.length; trblin[linestr-1].append(td2_);
											inp.setAttribute("value", valb);
											td2_.append(inp);
											';
							}
							
							$clad = "liste_titre_create";
							$claf = "from-7";
							$clag = '$("#tablelines > thead > tr")';
							$clat = 'trlin[0]';
							$clin = "trclin[0]";
							$clan = "from-7";
							if($num == 0)
							{
								$clad = "nohoverpair";
								$claf = 'from-4, "liste_titre", "="';
								$clag = '$("#tablelines .liste_titre")';
								$clat = '$(trlin)';
								$clin = "trclin";
								$clan = "from-3";
							}

							$script .=(
							($action=="editline")
								? 'var from2 = ($(".tredited > linecolqty").index());$.moveColumn(tablelin, from2, from2-5, "tredited", "=");'

								: 'var from2 = ($("#td2_pzs").index()); $.moveColumn(tablelin, from2, from2-5, "'.$clad.'", "=")'
							);
							$script.='
							//===========>Para el agregar nueva línea y editar

							//Muevo el tdde título
							$.moveColumn(tablelin, from, '.$claf.');

						//===============PIEZAS=================


						var tablelin = $("#tablelines");
						var trlin = '.$clag.';


						var trclin = $("#tablelines > tbody > .'.(($action=="editline") ? "tredited"  : "$clad").'");

						//===============CAMPOS DE PRODUCTOS=================
							//====Creo el td de titulo y lo muevo

								var td_ = document.createElement("td");
								td_.innerHTML = "Materie";
								td_.setAttribute("align", "center");
								td_.setAttribute("id", "td_mat");
								'.$clat.'.append(td_);

								//Para moverlo veo cuales su index
								var from = ($("#td_mat").index());

								$.moveColumn(tablelin, from, '.$claf.');


							//====Creo el td de titulo y lo muevo

							//===========>Para cada línea ya agregada le coloco suvalor traido de la BD y que está en el array labes_ex
							var ok = 0;
							for(trb of trblin)
							{
								';
							if($num == 0)
							{
								$script.='if(ok>0)
										{
											'; 
							}
								$script.='var idv = $(trb).attr("data-id");
								if(idv != undefined)
								{

									var valb = "-";
									if(labes_ex[idv] != undefined)
									{
										if(labes_ex[idv][1] != undefined )
										{
											if(labes_ex[idv][1] != "")
											{
												var docs = JSON.parse(labes_ex[idv][1]);
												{
													valb = "<div><ul width=\"300px\">";
													for(doc of docs)
													{
														valb += "<li>"+doc.label+": "+doc.labelsel+"</li>"
													}
													valb += "</ul></div>";

												}
											}
										}
									}
									
									';
									if($num == 0)
									{
										$script.='}
										';
									}
									$script.='ok++;
									//var valb = labes_ex[idv] != undefined ? labes_ex[idv][1] : "-";
									//console.log($(trb).attr("data-id"));
									var td1_ = document.createElement("td");
									if(valb == "-")
										td1_.setAttribute("align", "center");
									td1_.innerHTML = valb;
									trb.append(td1_);

								}
							}

							$.moveColumn(tablelin, from, '.$clan.', "oddeven", "=");
							//===========>Para cada línea ya agregada



							//=======Para la línea a crear
								
								var td2_ = document.createElement("td");
								td2_.setAttribute("id", "td_materie");
								td2_.setAttribute("hidden", "");
								td2_.innerHTML = $("#campspro").html();

								$("#campspro").click();
								$("#campspro").html("");
								'.$clin.'.append(td2_);
								console.log("#td_materie===>"+"'.$clad.'");
								var from = ($("#td_materie").index());
								$.moveColumn(tablelin, from, from-5, "'.(($action=="editline") ? "tredited"  : $clad ).'", "="); 
								
							//=======Para la línea a crear

						//===============CAMPOS DE PRODUCTOS=================

						';
						$script.='//=====RELLENO VALORES SI ES EDITAR=======';
						if($action=="editline")
						{
							$script.='
							var ide_ = $("input[name=\'lineid\']").val();
							//console.log(labes_ex);
							if(labes_ex[ide_] != undefined)
							{
								var valxs = JSON.parse(labes_ex[ide_][1]);
								for(var valx of valxs)
								{

									if($("#options_"+valx.field).length > 0)
									{

										$("#options_"+valx.field).val(valx.value);
									}
								}
							}
							';
						}
						$script.='//=====RELLENO VALORES SI ES EDITAR=======


					});
				</script>
			';

			echo '<div hidden id="campspro" ><table width="300px">';
				print $fieldsp;
			echo '</table></div>';


			echo $script;

			$this->formObjectOptions($parameters, $object, $action, "Y");
		}

	}


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
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			foreach ($parameters['toselect'] as $objectid) {
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
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			$this->resprints = '<option value="0"'.($disabled ? ' disabled="disabled"' : '').'>'.$langs->trans("CategoriaRelasMassAction").'</option>';
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
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
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

		$langs->load("categoriarelas@categoriarelas");

		$this->results = array();

		$head = array();
		$h = 0;

		if ($parameters['tabfamily'] == 'categoriarelas') {
			$head[$h][0] = dol_buildpath('/module/index.php', 1);
			$head[$h][1] = $langs->trans("Home");
			$head[$h][2] = 'home';
			$h++;

			$this->results['title'] = $langs->trans("CategoriaRelas");
			$this->results['picto'] = 'categoriarelas@categoriarelas';
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
			if ($user->rights->categoriarelas->myobject->read) {
				$this->results['result'] = 1;
				return 1;
			} else {
				$this->results['result'] = 0;
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Execute action completeTabsHead
	 *
	 * @param   array           $parameters     Array of parameters
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         'add', 'update', 'view'
	 * @param   Hookmanager     $hookmanager    hookmanager
	 * @return  int                             <0 if KO,
	 *                                          =0 if OK but we want to process standard actions too,
	 *                                          >0 if OK and we want to replace standard actions.
	 */
	public function completeTabsHead(&$parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $conf, $user;

		if (!isset($parameters['object']->element)) {
			return 0;
		}
		if ($parameters['mode'] == 'remove') {
			// utilisé si on veut faire disparaitre des onglets.
			return 0;
		} elseif ($parameters['mode'] == 'add') {
			$langs->load('categoriarelas@categoriarelas');
			// utilisé si on veut ajouter des onglets.
			$counter = count($parameters['head']);
			$element = $parameters['object']->element;
			$id = $parameters['object']->id;
			// verifier le type d'onglet comme member_stats où ça ne doit pas apparaitre
			// if (in_array($element, ['societe', 'member', 'contrat', 'fichinter', 'project', 'propal', 'commande', 'facture', 'order_supplier', 'invoice_supplier'])) {
			if (in_array($element, ['context1', 'context2'])) {
				$datacount = 0;

				$parameters['head'][$counter][0] = dol_buildpath('/categoriarelas/categoriarelas_tab.php', 1) . '?id=' . $id . '&amp;module='.$element;
				$parameters['head'][$counter][1] = $langs->trans('CategoriaRelasTab');
				if ($datacount > 0) {
					$parameters['head'][$counter][1] .= '<span class="badge marginleftonlyshort">' . $datacount . '</span>';
				}
				$parameters['head'][$counter][2] = 'categoriarelasemails';
				$counter++;
			}
			if ($counter > 0 && (int) DOL_VERSION < 14) {
				$this->results = $parameters['head'];
				// return 1 to replace standard code
				return 1;
			} else {
				// en V14 et + $parameters['head'] est modifiable par référence
				return 0;
			}
		}
	}

	/* Add here any other hooked methods... */
	public function formObjectOptions(&$parameters, &$object, &$action, $fromhere = "N")
	{
		global $db, $id;

		if(date("Y-m-d") >= "2021-11-13")
		{
			return;
		}

		if(in_array($parameters['currentcontext'], array('productcard')) || $fromhere=="Y")
		{
			$it = "";
			$contx =  in_array($parameters['currentcontext'], array('productcard'));
			//Para el editar de items
			if($id!="" && $contx)
			{
				$it = "_".$id;
			}
			$script = '';
			$script .= '

					<script>

						HiddenAll = function()
						{
							var extras = document.querySelectorAll(".trextrafields_collapse'.$it.'");
							for(var xtra of extras)
							{

								if($(xtra).length>0)
								{

									var fld = xtra.querySelector(" td > select ");
									var flx = xtra.querySelector(" td > input ");

									$(xtra).hide();

									if(typeof fld !="undefined")
									{
										$(fld).val("0");

									}

									if(typeof flx !="undefined")
									{
										$(flx).val("0");
									}
								}
							}
						}

						RefreshExtras = function()
						{
							var extras = document.querySelectorAll(".trextrafields_collapse'.$it.'");
							//console.log(extras);
							for(var xtra of extras)
							{

								if($(xtra).length>0)
								{

									var fld = xtra.querySelector(" td > select ");
									var flx = xtra.querySelector(" td > input ");

									if(typeof fld !="undefined")
									{
										if($(fld).val() != "" || $(fld).val() != 0) //si no tiene valor setiado lo escondo
										{
											var vald = $(fld).val();
											$(fld).trigger("change");
										}
									}

									if(typeof flx !="undefined")
									{
										if($(flx).val() != "" || $(flx).val() != 0) //si no tiene valor setiado lo
										{
											var vald = $(flx).val();
											$(flx).trigger("change");
										}
									}
								}
							}
						}

						ProccessExtras = function()
						{
							var extras = document.querySelectorAll(".trextrafields_collapse'.$it.'");
							for(var xtra of extras)
							{
								if($(xtra).length>0)
								{

									var fld = xtra.querySelector(" td > select ");
									var flx = xtra.querySelector(" td > input ");

									if(typeof fld !="undefined")
									{
										if($(fld).val() == "" || $(fld).val() == 0) //si no tiene valor setiado lo escondo
										{
											$(xtra).hide();
											$(fld).attr("disabled", "disabled");

										}
									}

									if(typeof flx !="undefined")
									{
										if($(flx).val() == "" || $(flx).val() == 0) //si no tiene valor setiado lo escondo
										{
											$(xtra).hide();
											$(flx).attr("disabled", "disabled");
										}
									}
								}

							}


			';
			$sql = 'SELECT * ';
			$sql .= ' FROM '.MAIN_DB_PREFIX.'product_extra_relas as t where status = 1';

			$resql = $db->query($sql);
			if ($resql) {
				$num = $db->num_rows($resql);

				$i = 0;
				while ($i < $num) {
					$obj = $db->fetch_object($resql);
					if ($obj) {

						$campos = json_decode($obj->campos);
						//accion (1 => Mostra sempre, 2 => Nascondi se, 3 => Mostra se)
						foreach($campos as $campo)
						{
							if($obj->accion == 1)
							{
								$script .= ' $(".product_extras_'.$campo.'").show();
								$("#options_'.$campo.'").removeAttr("disabled");';
							}
							elseif($obj->accion == 2)
							{
								$script .= '
								if($("#options_'.$obj->campo_condicion.'").length>0)
								{
									$("#options_'.$obj->campo_condicion.'").on("change", function(){
										if($("#options_'.$obj->campo_condicion.'").length>0)
										{
											if($("#options_'.$obj->campo_condicion.'").val()=="'.$obj->valor.'" && "'.$obj->valor.'" != "0" && "'.$obj->condicion.'" == "1") //igual
											{
												if($(".product_extras_'.$campo.'").length>0)
												{
													$(".product_extras_'.$campo.'").show();
													$("#options_'.$campo.'").removeAttr("disabled");
												}
											}
											else if("'.$obj->condicion.'" == "2") //diferente
											{
												if($("#options_'.$obj->campo_condicion.'").val()!="'.$obj->valor.'" && "'.$obj->valor.'" != "0" && $("#options_'.$obj->campo_condicion.'").val()!="0")
												{
													if($(".product_extras_'.$campo.'").length>0)
													{
														$(".product_extras_'.$campo.'").show();
														$("#options_'.$campo.'").removeAttr("disabled");
													}
												}
												else
												{
													if($(".product_extras_'.$campo.'").length>0)
													{
														$(".product_extras_'.$campo.'").hide();
														$("#options_'.$campo.'").val("0");';

														if(!$contx)
															$script .='$("#options_'.$campo.'").attr("disabled", "disabled");';

														$script .= '$("#options_'.$campo.'").trigger("change");
													}
												}
											}
											else
											{
												if("'.$obj->condicion.'" == "3")// no vacio
												{
													//alert($("#options_'.$obj->campo_condicion.'").val());
													if($("#options_'.$obj->campo_condicion.'").val() != "" && $("#options_'.$obj->campo_condicion.'").val() != "0")
													{
														if($(".product_extras_'.$campo.'").length>0)
														{
															$(".product_extras_'.$campo.'").show();
															$("#options_'.$campo.'").removeAttr("disabled");
														}
													}
													else
													{

														if($(".product_extras_'.$campo.'").length>0)
														{
															$(".product_extras_'.$campo.'").hide();
															$("#options_'.$campo.'").val("0");';

															if(!$contx)
																$script .='$("#options_'.$campo.'").attr("disabled", "disabled");';

															$script .= '$("#options_'.$campo.'").trigger("change");
														}
													}
												}
												else
												{
													if($(".product_extras_'.$campo.'").length>0)
													{
														$(".product_extras_'.$campo.'").hide();';

														if(!$contx)
															$script .='$("#options_'.$campo.'").attr("disabled", "disabled");';

														$script .= '$("#options_'.$campo.'").val("0");
														//console.log($("#options_'.$campo.'").val());
														$("#options_'.$campo.'").trigger("change");
													}
												}
											}
										}
									});
								}

								';
							}

						}
					}
					$i++;
				}
			}


			$script .= '
						RefreshExtras();
						}//function proccess


						$(document).ready(function() {';
						if($fromhere=="Y")
							$script .= 'setTimeout(function(){ ProccessExtras(); $("#td_materie").show();}, 500);';
						else
						{
							$script .= '

							ProccessExtras();
							//console.log($("select[name=\'finished\']"));
							$("select[name=\'finished\']").on("change", function(){
								//console.log($("select[name=\'finished\']").val());
								if($("select[name=\'finished\']").val() != 0 )
									HiddenAll();
								else
									ProccessExtras();
							});
							$("select[name=\'finished\']").trigger("change");


							function swap_position(first_index, second_index){

								  if(second_index > first_index){
									$("tr").eq(first_index).insertBefore($("tr").eq(second_index));
									//$("tr").eq(second_index).insertBefore($("tr").eq(first_index));
								  }

								  else if(first_index > second_index){
									//$("tr").eq(second_index).insertBefore($("tr").eq(first_index));
									$("tr").eq(first_index).insertBefore($("tr").eq(second_index));
								  }

								}

							var rowt = $("input[name=\'weight\']").parent().parent();
							var rowm = $("input[name=\'size\']").parent().parent();
							//console.log($(rowt).index());
							//$(rowt).eq($(rowt).index()).insertBefore($(rowt).eq($(rowt).index()+2));
							';
							if($contx && $action =="create")
							{
								$script .= 'var rowp = $("#note_private").parent().parent().index();

								swap_position($(rowt).index()+1, rowp-2)
								swap_position($(rowm).index()+1, rowp-2)
								';
							}
							else
							{
								$script .= 'var rowp = $("#categories").parent().parent().index();

								swap_position($(rowt).index(), rowp-2)
								swap_position($(rowm).index(), rowp-2)

								';

							}

						}
						$script .='});

					</script>
			';
			echo $script."";
		}

	}

}
