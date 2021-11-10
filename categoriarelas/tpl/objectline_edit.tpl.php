<?php
/* Copyright (C) 2010-2012	Regis Houssin		<regis.houssin@inodbox.com>
 * Copyright (C) 2010-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012		Christophe Battarel	<christophe.battarel@altairis.fr>
 * Copyright (C) 2012       Cédric Salvador     <csalvador@gpcsolutions.fr>
 * Copyright (C) 2012-2014  Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2013		Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2018       Frédéric France         <frederic.france@netlogic.fr>
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
 * $seller, $buyer
 * $dateSelector
 * $forceall (0 by default, 1 for supplier invoices/orders)
 * $senderissupplier (0 by default, 1 for supplier invoices/orders)
 * $inputalsopricewithtax (0 by default, 1 to also show column with unit price including tax)
 */

print '<tr class="liste_titre nodrag nodrop edittlinetr">';

$coldisplay++;
$sal = str_replace("@@", "appearance", $sel);
print '<td valign="middle" align="center" >'.($i+1).'. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td>';
print '<input type="hidden" name="lineid" value = "'.$obj->id.'"/>';

$coldisplay++;
$sal = str_replace("@@", "fill", $sel);
print '<td width="400" id="create_td" align="center" class="bordertop nobottom">';
$camposjs = json_decode($obj->campos);
print '<input type="hidden" value = "'.implode(",", $camposjs).'"/>';
print '</td>';

$coldisplay++;
print '<td align="center" class="bordertop nobottom">';
print '<select class="flat minwidth100 maxwidthonsmartphone" id="accione" name="accione" onchange="select_camp()" tabindex="-1" aria-hidden="true"><option value="1" '.($obj->accion==1 ? "selected" : "" ).' >Mostra sempre</option><option value="2" '.($obj->accion==2 ? "selected" : "" ).'>Mostra se</option></select>';
print '</td>';

$coldisplay++;
print '<td align="center" id="padre_td" class="bordertop nobottom">';
print '<input type="hidden" value = "'.$obj->campo_condicion.'"/>';
print '</td>';

$coldisplay++;
print '<td align="center" class="bordertop nobottom">';
print '<select class="flat minwidth100 maxwidthonsmartphone " id = "condicion" name = "condicion" tabindex="-1" onchange = "select_condi()" aria-hidden="true"><option value="0" ></option><option value="1" '.($obj->condicion==1 ? "selected" : "" ).'>Uguale a</option><option value="2" '.($obj->condicion==2 ? "selected" : "" ).'>Diverso da</option><option value="3" '.($obj->condicion==3 ? "selected" : "" ).'>Non vuoto</option></select>';
print '</td>';

$coldisplay++;
print '<td align="center" id="valore_td" class="bordertop nobottom">';
print '<input type="hidden" value = "'.$obj->valor.'"/>';
print '</td>';

$coldisplay++;
print '<td align="center" class="bordertop nobottom">';
print '<select class="flat minwidth100 maxwidthonsmartphone " name = "status" tabindex="-1" aria-hidden="true"><option value="1" '.($obj->status==1 ? "selected" : "" ).' >Attivo</option><option value="2" '.($obj->status==2 ? "selected" : "" ).'>Inattivo</option></select>';
print '</td>';

/*$coldisplay += $colspan;
print '<td class="bordertop nobottom linecoledit center valignmiddle" colspan="5">';
print '<input type="submit" class="button" value="'.$langs->trans('Add').'" name="addline" id="addline">';
print '</td>';*/
print '</tr>';


print '<tr>';
$coldisplay++;
print '<td class="bordertop nobottom center" colspan="17">';
print '<input type="button" class="button buttongen marginbottomonly button-save" id="savelinebutton" name="save" value="'.$langs->trans("Save").'">';

print '<input type="submit" class="button buttongen marginbottomonly button-cancel" id="cancellinebutton" name="cancel" value="'.$langs->trans("Cancel").'">';
print '</td>';
print '</tr>';

print "<!-- END PHP TEMPLATE objectline_edit.tpl.php -->\n";
