<?php
/* Copyright (C) 2010-2012	Regis Houssin		<regis.houssin@inodbox.com>
 * Copyright (C) 2010-2014	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012-2013	Christophe Battarel	<christophe.battarel@altairis.fr>
 * Copyright (C) 2012       Cédric Salvador     <csalvador@gpcsolutions.fr>
 * Copyright (C) 2014		Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2014       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2015-2016	Marcos García		<marcosgdf@gmail.com>
 * Copyright (C) 2018-2019  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2018		Ferran Marcet		<fmarcet@2byte.es>
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
 */


print '<tr class="liste_titre nodrag nodrop">';
if (!empty($conf->global->MAIN_VIEW_LINE_NUMBER)) {
		print '<td class="linecolnum center"></td>';
}

$sel='<table><tr><td><img width="18" class="imcheck" id="ck@@" raw="@@" style="opacity:0.2;" src="quality/img/check.png"/></td> <td><img width="18" class="imcan" id="ca@@" raw="@@" style="opacity:0.2;" src="quality/img/cancel.png"/></td><tr></table>';

$coldisplay++;
$sal = str_replace("@@", "appearance", $sel);
print '<td valign="middle" align="center" > - ';
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "fill", $sel);
print '<td width="400" id="create_td" align="center" class="bordertop nobottom">';

print '</td>';

$coldisplay++;
print '<td align="center" class="bordertop nobottom">';
print '<select class="flat minwidth100 maxwidthonsmartphone" id="accione" name="accione" onchange="select_camp()" tabindex="-1" aria-hidden="true"><option value="1" >Mostra sempre</option><option value="2" >Mostra se</option></select>';
print '</td>';

$coldisplay++;
print '<td align="center" id="padre_td" class="bordertop nobottom">';
print '</td>';

$coldisplay++;
print '<td align="center" class="bordertop nobottom">';
print '<select class="flat minwidth100 maxwidthonsmartphone " hidden id = "condicion" name = "condicion" onchange = "select_condi()" tabindex="-1" aria-hidden="true"><option value="0" ></option><option value="1" >Uguale a</option><option value="2" >Diverso da</option><option value="3" >Non vuoto</option></select>';
print '</td>';

$coldisplay++;
print '<td align="center" id="valore_td" class="bordertop nobottom">';
print '</td>';

$coldisplay++;
print '<td align="center" class="bordertop nobottom">';
print '<select class="flat minwidth100 maxwidthonsmartphone " name = "status" tabindex="-1" aria-hidden="true"><option value="1" >Attivo</option><option value="2" >Inattivo</option></select>';
print '</td>';

/*$coldisplay += $colspan;
print '<td class="bordertop nobottom linecoledit center valignmiddle" colspan="5">';
print '<input type="submit" class="button" value="'.$langs->trans('Add').'" name="addline" id="addline">';
print '</td>';*/
print '</tr>';

print '<tr>';
//$coldisplay += $colspan;
print '<td class="bordertop nobottom linecoledit center valignmiddle" colspan="17">';
print '<input type="button" class="button" value="'.$langs->trans('Add').'" name="addline" id="addline"></br></br>';

print '</td>';
print '</tr>';


if (is_object($objectline)) {
	print $objectline->showOptionals($extrafields, 'edit', array('style'=>$bcnd[$var], 'colspan'=>$coldisplay), '', '', 1);
}
?>

<!-- END PHP TEMPLATE objectline_create.tpl.php -->
