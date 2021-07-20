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

// Protection to avoid direct call of template
if (empty($object) || !is_object($object))
{
	print "Error, template page can't be called as URL";
	exit;
}




print "<!-- BEGIN PHP TEMPLATE objectline_edit.tpl.php -->\n";

print '<tr class="oddeven tredited">';
// Adds a line numbering column
if (!empty($conf->global->MAIN_VIEW_LINE_NUMBER)) {
	print '<td class="linecolnum center">'.($i + 1).'</td>';
	$coldisplay++;
}

$sel='<table><tr><td><img width="18" class="imcheck" id="ck@@" raw="@@" @stylec@ src="quality/img/check.png"/></td> <td><img width="18" class="imcan" id="ca@@" raw="@@" @stylex@ src="quality/img/cancel.png"/></td><tr></table>';

$coldisplay++;

$sal = str_replace("@@", "appearance", $sel);
if($line->appearance=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}
	

print '<td align="center" class="bordertop nobottom"><input type="hidden" name="appearance" id="appearance" class="Checkid flat right" size="10"  value="'.$line->appearance.'">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "fill", $sel);
if($line->fill=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}

print '<td align="center" class="bordertop nobottom"><input type="hidden" name="fill" id="fill" class="Checkid flat right" size="10" value="'.$line->fill.'">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "shovels", $sel);
if($line->shovels=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="shovels" id="shovels" class="Checkid flat right" size="10"  value="'.$line->shovels.'">'.$sal;
print '</td>';

$coldisplay++;

$sal = str_replace("@@", "accommodation", $sel);
if($line->accommodation=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="accommodation" id="accommodation" class="Checkid flat right" size="10"  value="'.$line->accommodation.'">'.$sal;
print '</td>';

$coldisplay++;

$sal = str_replace("@@", "curvature", $sel);
if($line->curvature=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="curvature" id="curvature" class="Checkid flat right" size="10"  value="'.$line->curvature.'">'.$sal;
print '</td>';

$coldisplay++;

$sal = str_replace("@@", "size", $sel);
if($line->size=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="size" id="size" class="Checkid flat right" size="10"  value="'.$line->size.'">'.$sal;
print '</td>';

$coldisplay++;

$sal = str_replace("@@", "rack", $sel);
if($line->rack=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="rack" id="rack" class="Checkid flat right" size="10" value="'.$line->rack.'">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "temper", $sel);
if($line->temper=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="temper" id="temper" class="Checkid flat right" size="10"  value="'.$line->temper.'">'.$sal;
print '</td>';

$coldisplay++;

$sal = str_replace("@@", "material", $sel);
if($line->material=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}
print '<td align="center"class="bordertop nobottom"><input type="hidden" name="material" id="material" class="Checkid flat right" size="10"  value="'.$line->material.'">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "parameters", $sel);
if($line->parameters=="YES")
{
	$sal = str_replace("@stylec@", "", $sal);
	$sal = str_replace("@stylex@", 'style="opacity:0.2;"', $sal);
}
else
{
	$sal = str_replace("@stylec@", 'style="opacity:0.2;"', $sal);
	$sal = str_replace("@stylex@", "", $sal);
}
print '<td align="center" class="bordertop nobottom"><input type="hidden"  name="parameters" id="parameters" class="Checkid flat right" size="10"  value="'.$line->parameters.'">'.$sal;
print '</td>';

$coldisplay++;
print '<td align="center" class="bordertop nobottom"><input type="number"  name="box" id="box" class="Checkid flat right" size="5" value="'.$line->box.'">';
print '</td>';

$coldisplay++;
print '<td class="bordertop nobottom"><textarea style="font-size: 10pt;" name="notes" id="notes" class="Checkid flat " rows="4" cols="20">'.$line->notes.'</textarea>';
print '<input type="hidden" name="lineid" value="'.$line->id.'">';
print '</td>';

$coldisplay++;
print '<td class="bordertop nobottom">';
print '</td>';

print '</tr>';


print '<tr>';
$coldisplay++;
print '<td class="bordertop nobottom center" colspan="17">';
print '<input type="button" class="button buttongen marginbottomonly button-save" id="savelinebutton" name="save" value="'.$langs->trans("Save").'">';

print '<input type="submit" class="button buttongen marginbottomonly button-cancel" id="cancellinebutton" name="cancel" value="'.$langs->trans("Cancel").'">';
print '</td>';
print '</tr>';

if (is_object($objectline)) {
	print $objectline->showOptionals($extrafields, 'edit', array('style'=>$bcnd[$var], 'colspan'=>$coldisplay), '', '', 1);
}

print "<!-- END PHP TEMPLATE objectline_edit.tpl.php -->\n";
