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
		print '<div class="fichecenter">';


		print '<table width="100%">';


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
				
				if ($line->role == 'toproduce' ) {
					$i = 1;
					
					$father = $line->id;

					$nblinetoproducecursor++;

					$tmpproduct = new Product($db);
					$tmpproduct->fetch($line->fk_product);

					$arrayoflines = $object->fetchLinesLinked('produced', $line->id);
					$alreadyproduced = 0;
					foreach ($arrayoflines as $line2) {
						$alreadyproduced += $line2['qty'];
					}
					
					
					print'<!--Start fiche for toproduce: '.$line->id.'-->';
					print '<tr>';
					print '<td valign="top">';
					print '<div class="clearboth"></div>';
					
					$newlinetext = '';
					
					if ($lns == 0 ) {
						
						$newlinetext = '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=addproductionline">'.$langs->trans("AddNewProductionLines").'</a>';
					if($object->status == $object::STATUS_PRODUCED || $object->status == $object::STATUS_CANCELED)
						$newlinetext = "";
					print load_fiche_titre($langs->trans('Production'), '', '', 0, '', '', $newlinetext);
					}
					print '<div class="div-table-responsive-no-min">';
					print '<table width="100%" class="noborder noshadow nobottom centpercent">';
			
					print '<tr class="liste_titre">';
					print '<td>#</td>';
					print '<td>'.$langs->trans("Product").'</td>';
					print '<td class="right">'.$langs->trans("Qty").'</td>';
					print '<td class="right">'.$langs->trans("QtyAlreadyProduced").'</td>';
					print '<td>';
					if ($collapse || in_array($action, array('consumeorproduce', 'consumeandproduceall'))) print $langs->trans("Warehouse");
					print '</td>';
					if ($conf->productbatch->enabled) {
						print '<td>';
						if ($collapse || in_array($action, array('consumeorproduce', 'consumeandproduceall'))) print $langs->trans("Batch");
						print '</td>';
						print '<td></td>';
					}
					print '</tr>';
					print'<!--Moved-->';
					
					
					

					$suffix = '_'.$line->id;
					print '<!-- Line to dispatch '.$suffix.' -->'."\n";
					// hidden fields for js function
					print '<input id="qty_ordered'.$suffix.'" type="hidden" value="'.$line->qty.'">';
					print '<input id="qty_dispatched'.$suffix.'" type="hidden" value="'.$alreadyproduced.'">';

					print '<tr>';
					if ($action != 'addproductionline') {
					
					$che = "";
						
					if($line->id == GETPOST('BomSelect','int'))
					{
						$che = " checked ";
					}
					print '<td><input class="selec_produt" id="selec_produt'.$suffix.'" name = "selec_produt" '.$che.' type="radio" value="'.$line->id.'"></td>';
					}
					else {
						print '<td></td>';
					}
					print '<td>'.$tmpproduct->getNomUrl(1);
					print '<br><span class="opacitymedium small">'.$tmpproduct->label.'</span>';
					print '</td>';
					print '<td class="right">'.$line->qty.'</td>';
					print '<td class="right nowraponall">';
					if ($alreadyproduced) {
						print '<script>';
						print 'jQuery(document).ready(function() {
							jQuery("#expandtoproduce'.$line->id.'").click(function() {
								console.log("Expand multiprodof_production line '.$line->id.'");
								jQuery(".expanddetailtoproduce'.$line->id.'").toggle();';
						if ($nblinetoproduce == $nblinetoproducecursor) {
							print 'if (jQuery("#tablelinestoproduce").hasClass("nobottom")) { jQuery("#tablelinestoproduce").removeClass("nobottom"); } else { jQuery("#tablelinestoproduce").addClass("nobottom"); }';
						}
						print '
							});
						});';
						print '</script>';
						if (empty($conf->use_javascript_ajax)) print '<a href="'.$_SERVER["PHP_SELF"].'?collapse='.$collapse.','.$line->id.'">';
						print img_picto($langs->trans("ShowDetails"), "chevron-down", 'id="expandtoproduce'.$line->id.'"');
						if (empty($conf->use_javascript_ajax)) print '</a>';
					}
					print ' '.$alreadyproduced;
					print '</td>';
					print '<td>'; // Warehouse
					print '</td>';
					if ($conf->productbatch->enabled) {
						print '<td></td>'; // Lot
						print '<td></td>';
					}
					print '</tr>';

					// Show detailed of already consumed with js code to collapse
					foreach ($arrayoflines as $line2) {
						print '<tr class="expanddetailtoproduce'.$line->id.' hideobject opacitylow">';
						print '<td>';
						print dol_print_date($line2['date'], 'dayhour');
						print '</td>';
						print '<td></td>';
						print '<td class="right">'.$line2['qty'].'</td>';
						print '<td class="tdoverflowmax150">';
						if ($line2['fk_warehouse'] > 0) {
							$result = $tmpwarehouse->fetch($line2['fk_warehouse']);
							if ($result > 0) print $tmpwarehouse->getNomUrl(1);
						}
						print '</td>';
						if ($conf->productbatch->enabled) {
							print '<td>';
							if ($line2['batch'] != '') {
								$tmpbatch->fetch(0, $line2['fk_product'], $line2['batch']);
								print $tmpbatch->getNomUrl(1);
							}
							print '</td>';
							print '<td></td>';
						}
						print '</tr>';
					}
					
					
					print '</table>';

					print '</td>';//end of line table toproduce
					
					print '<td valign="top">';//start of lines table toconsume
					print '<div class="clearboth"></div>';

					$newlinetext = '';
					if ($lns == 0) {
						$newlinetext = '<a href="javascript:AddNewConsumeLines()">'.$langs->trans("AddNewConsumeLines").'</a>';
					print '<script>function AddNewConsumeLines(){ var selec_produt = $(\'input[name="selec_produt"]:checked\').val();';
					print 'if(selec_produt){window.location.href ="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=addconsumeline&BomSelect="+selec_produt;}else{ alert("'.$langs->trans("PlisSelectProdutToAddConsume").'") }}</script>';
					if($object->status == $object::STATUS_PRODUCED || $object->status == $object::STATUS_CANCELED)
						$newlinetext = "";
					print load_fiche_titre($langs->trans('Consumption'), '', '', 0, '', '', $newlinetext);
					}
					print '<div class="div-table-responsive-no-min">';
					print '<table class="noborder noshadow centpercent nobottom">';
			
					print '<tr class="liste_titre">';
					print '<td>'.$langs->trans("Product").'</td>';
					print '<td class="right">'.$langs->trans("Qty").'</td>';
					print '<td class="right">'.$langs->trans("QtyAlreadyConsumed").'</td>';
					print '<td>';
					if ($collapse || in_array($action, array('consumeorproduce', 'consumeandproduceall'))) print $langs->trans("Warehouse");
					print '</td>';
					if ($conf->productbatch->enabled) {
						print '<td>';
						if ($collapse || in_array($action, array('consumeorproduce', 'consumeandproduceall'))) print $langs->trans("Batch");
						print '</td>';
					}
					print '</tr>';
			

			
					// Lines to consume
			
					if (!empty($object->lines))
					{
						$nblinetoconsume = 0;
						foreach ($object->lines as $line) {
							if ($line->role == 'toconsume' && $father == $line->fk_father) {
								$nblinetoconsume++;
							}
						}
			
						$nblinetoconsumecursor = 0;
						foreach ($object->lines as $line) {
							if ($line->role == 'toconsume'  && $father == $line->fk_father) {
								$nblinetoconsumecursor++;
			
								$tmpproduct = new Product($db);
								$tmpproduct->fetch($line->fk_product);
			
								$arrayoflines = $object->fetchLinesLinked('consumed', $line->id);
								$alreadyconsumed = 0;
								foreach ($arrayoflines as $line2) {
									$alreadyconsumed += $line2['qty'];
								}
			
								print '<tr>';
								print '<td>'.$tmpproduct->getNomUrl(1);
								print '<br><span class="opacitymedium small">'.$tmpproduct->label.'</span>';
								print '</td>';
								print '<td class="right nowraponall">';
								$help = '';
								if ($line->qty_frozen) $help .= ($help ? '<br>' : '').'<strong>'.$langs->trans("QuantityFrozen").'</strong>: '.yn(1).' ('.$langs->trans("QuantityConsumedInvariable").')';
								if ($line->disable_stock_change) $help .= ($help ? '<br>' : '').'<strong>'.$langs->trans("DisableStockChange").'</strong>: '.yn(1).' ('.(($tmpproduct->type == Product::TYPE_SERVICE && empty($conf->global->STOCK_SUPPORTS_SERVICES)) ? $langs->trans("NoStockChangeOnServices") : $langs->trans("DisableStockChangeHelp")).')';
								if ($help) {
									print $form->textwithpicto($line->qty, $help, -1);
								} else {
									print $line->qty;
								}
								print '</td>';
								print '<td class="right">';
								if ($alreadyconsumed) {
									print '<script>';
									print 'jQuery(document).ready(function() {
											jQuery("#expandtoproduce'.$line->id.'").click(function() {
												console.log("Expand multiprodof_production line '.$line->id.'");
												jQuery(".expanddetail'.$line->id.'").toggle();';
									if ($nblinetoconsume == $nblinetoconsumecursor) {	// If it is the last line
										print 'if (jQuery("#tablelines").hasClass("nobottom")) { jQuery("#tablelines").removeClass("nobottom"); } else { jQuery("#tablelines").addClass("nobottom"); }';
									}
									print '
											});
										});';
									print '</script>';
									if (empty($conf->use_javascript_ajax)) print '<a href="'.$_SERVER["PHP_SELF"].'?collapse='.$collapse.','.$line->id.'">';
									print img_picto($langs->trans("ShowDetails"), "chevron-down", 'id="expandtoproduce'.$line->id.'"');
									if (empty($conf->use_javascript_ajax)) print '</a>';
								} else {
									if ($nblinetoconsume == $nblinetoconsumecursor) {	// If it is the last line
										print '<script>jQuery("#tablelines").removeClass("nobottom");</script>';
									}
								}
								print ' '.$alreadyconsumed;
								print '</td>';
								print '<td>'; // Warehouse
								print '</td>';
								if ($conf->productbatch->enabled) {
									print '<td></td>'; // Lot
								}
								print '</tr>';
			
								// Show detailed of already consumed with js code to collapse
								foreach ($arrayoflines as $line2) {
									print '<tr class="expanddetail'.$line->id.' hideobject opacitylow">';
									print '<td>';
									print dol_print_date($line2['date'], 'dayhour');
									print '</td>';
									print '<td></td>';
									print '<td class="right">'.$line2['qty'].'</td>';
									print '<td class="tdoverflowmax150">';
									if ($line2['fk_warehouse'] > 0) {
										$result = $tmpwarehouse->fetch($line2['fk_warehouse']);
										if ($result > 0) print $tmpwarehouse->getNomUrl(1);
									}
									print '</td>';
									// Lot Batch
									print '<td>';
									if ($line2['batch'] != '') {
										$tmpbatch->fetch(0, $line2['fk_product'], $line2['batch']);
										print $tmpbatch->getNomUrl(1);
									}
									print '</td>';
									print '</tr>';
								}
			
								if (in_array($action, array('consumeorproduce', 'consumeandproduceall'))) {
									$i = 1;
									print '<!-- Enter line to consume -->'."\n";
									print '<tr>';
									print '<td><span class="opacitymedium">'.$langs->trans("ToConsume").'</span></td>';
									$preselected = (GETPOSTISSET('qty-'.$line->id.'-'.$i) ? GETPOST('qty-'.$line->id.'-'.$i) : max(0, $line->qty - $alreadyconsumed));
									if ($action == 'consumeorproduce' && !GETPOSTISSET('qty-'.$line->id.'-'.$i)) $preselected = 0;
									print '<td class="right"><input type="text" class="width50 right" name="qty-'.$line->id.'-'.$i.'" value="'.$preselected.'"></td>';
									print '<td></td>';
									print '<td>';
									if ($tmpproduct->type == Product::TYPE_PRODUCT || !empty($conf->global->STOCK_SUPPORTS_SERVICES)) {
										if (empty($line->disable_stock_change)) {
											$preselected = (GETPOSTISSET('idwarehouse-'.$line->id.'-'.$i) ? GETPOST('idwarehouse-'.$line->id.'-'.$i) : ($tmpproduct->fk_default_warehouse > 0 ? $tmpproduct->fk_default_warehouse : 'ifone'));
											print $formproduct->selectWarehouses($preselected, 'idwarehouse-'.$line->id.'-'.$i, '', 1, 0, $line->fk_product, '', 1, 0, null, 'maxwidth300');
										} else {
											print '<span class="opacitymedium">'.$langs->trans("DisableStockChange").'</span>';
										}
									} else {
										print '<span class="opacitymedium">'.$langs->trans("NoStockChangeOnServices").'</span>';
									}
									// Lot / Batch
									print '</td>';
									if ($conf->productbatch->enabled) {
										print '<td>';
										if ($tmpproduct->status_batch) {
											$preselected = (GETPOSTISSET('batch-'.$line->id.'-'.$i) ? GETPOST('batch-'.$line->id.'-'.$i) : '');
											print '<input type="text" class="width50" name="batch-'.$line->id.'-'.$i.'" value="'.$preselected.'">';
										}
										print '</td>';
									}
									print '</tr>';
								}
							}
						}
					}
			
			   		print '</table>';
					print '</td>'; //end of lines table toconsume
					print '</tr>'; // end or line complete to produce an consume 
					$lns++;
				}
			
			}
		}
else {

						
						$newlinetext = '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=addproductionline">'.$langs->trans("AddNewProductionLines").'</a>';
					if($object->status == $object::STATUS_PRODUCED || $object->status == $object::STATUS_CANCELED)
						$newlinetext = "";
					print load_fiche_titre($langs->trans('Production'), '', '', 0, '', '', $newlinetext);
				
					print '<div class="div-table-responsive-no-min">';
					print '<table width="100%" class="noborder noshadow nobottom centpercent">';
			
					print '<tr class="liste_titre">';
					print '<td>#</td>';
					print '<td>'.$langs->trans("Product").'</td>';
					print '<td class="right">'.$langs->trans("Qty").'</td>';
					print '<td class="right">'.$langs->trans("QtyAlreadyProduced").'</td>';
					print '<td>';
					if ($collapse || in_array($action, array('consumeorproduce', 'consumeandproduceall'))) print $langs->trans("Warehouse");
					print '</td>';
					if ($conf->productbatch->enabled) {
						print '<td>';
						if ($collapse || in_array($action, array('consumeorproduce', 'consumeandproduceall'))) print $langs->trans("Batch");
						print '</td>';
						print '<td></td>';
					}
					print '</tr>';
					print'<!--Moved-->';
}

		
		print '<table>';
		print '</div>';
		
		
		



		// Lines to produce

		print '</div>';

		print '</div>';