<?php
/* Copyright (C) 2010-2013	Regis Houssin		<regis.houssin@inodbox.com>
 * Copyright (C) 2010-2011	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012-2013	Christophe Battarel	<christophe.battarel@altairis.fr>
 * Copyright (C) 2012       Cédric Salvador     <csalvador@gpcsolutions.fr>
 * Copyright (C) 2012-2014  Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2013		Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2017		Juanjo Menent		<jmenent@2byte.es>
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
 *
 * Need to have following variables defined:
 * $object (invoice, order, ...)
 * $conf
 * $langs
 * $forceall (0 by default, 1 for supplier invoices/orders)
 * $element     (used to test $user->rights->$element->creer)
 * $permtoedit  (used to replace test $user->rights->$element->creer)
 * $inputalsopricewithtax (0 by default, 1 to also show column with unit price including tax)
 * $object_rights->creer initialized from = $object->getRights()
 * $disableedit, $disablemove, $disableremove
 *
 * $type, $text, $description, $line
 */

// Protection to avoid direct call of template
if (empty($object) || !is_object($object))
{
	print "Error, template page can't be called as URL";
	exit;
}


/*global $forceall, $senderissupplier, $inputalsopricewithtax, $outputalsopricetotalwithtax;

if (empty($dateSelector)) $dateSelector = 0;
if (empty($forceall)) $forceall = 0;
if (empty($senderissupplier)) $senderissupplier = 0;
if (empty($inputalsopricewithtax)) $inputalsopricewithtax = 0;
if (empty($outputalsopricetotalwithtax)) $outputalsopricetotalwithtax = 0;

// add html5 elements
$domData  = ' data-element="'.$line->element.'"';
$domData .= ' data-id="'.$line->id.'"';
$domData .= ' data-qty="'.$line->qty.'"';
$domData .= ' data-product_type="'.$line->product_type.'"';*/



print "<!-- BEGIN PHP TEMPLATE objectline_view.tpl.php -->\n";

/*print '<tr  >';
print '<td >';

if (!empty($permissiontoaddquality)) {
		print '<a class="editfielda reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->id.'">'.img_edit().'</a> ';
		}
if ($permissiontoaddquality) {
			print'<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deleteline&amp;token='.newToken().'&amp;lineid='.$line->id.'">';
			print img_delete();
			print '</a>';
		}
print '</td>';
print '<td  colspan="11" align="right" >';
print dol_print_date($line->date_creation,'dayrfc')." ";
print dol_print_date($line->date_creation,'hour')." ";

$userstatic->fetch($line->fk_user_creat);
$line->fk_user_creat = $userstatic->getNomUrl(1, '', 0, 0, 24, 0, 'login');
print $line->fk_user_creat;
print '</td>';


print '</tr>';*/


print '<tr id="row-'.$obj->id.'" hidden class="drag drop oddeven listrules"  >';
$sel='<table><tr><td @stylec@ ><img width="18" class="imcheck" id="ck@@" title="@@" raw="@@" src="quality/img/check.png"/></td> <td @stylex@><img width="18" class="imcan" title="@@" id="ca@@" raw="@@" @stylex@ src="quality/img/cancel.png"/></td><tr></table>';

$coldisplay++;

print '<td valign="top" align="center" class=" borderbottom">'.($i+1).'. <a class="editfielda editfilelink" href="'.$_SERVER["PHP_SELF"].'?action=editline&amp;lineid='.$obj->id.'" rel="instalarica+impresora+tr+.txt"><span class="fas fa-pencil-alt paddingrightonly" style=" color: #444;" title="Modifica"></span></a><a href="'.$_SERVER["PHP_SELF"].'?action=deleteline&amp;lineid='.$obj->id.'" class="reposition deletefilelink" rel="sdd/instalarica impresora tr .txt"><span class="fas fa-trash pictodelete" style="" title="Elimina"></span></a>';
print '</td>';
$camposjs = json_decode($obj->campos);
print '<td valign="top" align="center" class=" borderbottom">';
print '<input type="hidden" value = "'.implode(",", $camposjs).'"/>';
print '</td>';

$coldisplay++;

print '<td valign="top" align="center" class=" borderbottom">';
print '<input type="hidden" value = "'.$obj->accion.'"/>';
$acct = $obj->accion == 1 ? "Mostra sempre" :  "Mostra se";
print $acct;
print '</td>';

$coldisplay++;

print '<td valign="top" align="center" class=" borderbottom">';
print '<input type="hidden" value = "'.$obj->campo_condicion.'"/>';
print '</td>';

$coldisplay++;

print '<td valign="top" align="center" class=" borderbottom">';
print '<input type="hidden" value = "'.$obj->condicion.'"/>';
$acct = $obj->condicion == 1 ? "Uguale a" : ( $obj->condicion == 2 ? "Diverso da" : ($obj->condicion == 3 ? "Non vuoto" : "")) ;
print $acct;
print '</td>';

$coldisplay++;


print '<td valign="top" align="center" class=" borderbottom">';
print '<input type="hidden" value = "'.$obj->campo_condicion."|".$obj->valor.'"/>';

print '</td>';

$coldisplay++;

print '<td valign="top" align="center" class=" borderbottom">';
print '<input type="hidden" value = "'.$obj->status.'"/>';
$acct = $obj->status == 1 ? "Attivo" : "Inattivo" ;
print $acct;
print '</td>';






		/*print '<td>';
		if (!empty($permissiontoaddquality)) {
		print '<a class="editfielda reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->id.'">'.img_edit().'</a>';
		}
		print '</td>';

		print '<td class="linecoldelete center">';
		$coldisplay++;

			//La suppression n'est autorisée que si il n'y a pas de ligne dans une précédente situation
			print '<td>';
			if ($permissiontoaddquality) {
			print'<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deleteline&amp;token='.newToken().'&amp;lineid='.$line->id.'">';
			print img_delete();
			print '</a>';
			}
		print '</td>';*/

	print '</tr>';

print "<!-- END PHP TEMPLATE objectline_view.tpl.php -->\n";
