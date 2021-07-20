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

// Protection to avoid direct call of template
if (empty($Quality) || !is_object($Quality)) {
	print "Error: this template page cannot be called directly as an URL";
	exit;
}

print '<tr class="liste_titre nodrag nodrop">';
if (!empty($conf->global->MAIN_VIEW_LINE_NUMBER)) {
		print '<td class="linecolnum center"></td>';
}

$sel='<table><tr><td><img width="18" class="imcheck" id="ck@@" raw="@@" style="opacity:0.2;" src="quality/img/check.png"/></td> <td><img width="18" class="imcan" id="ca@@" raw="@@" style="opacity:0.2;" src="quality/img/cancel.png"/></td><tr></table>';

$coldisplay++;
$sal = str_replace("@@", "appearance", $sel);
print '<td valign="middle" align="center" ><input type="hidden" name="appearance" id="appearance" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "fill", $sel);
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="fill" id="fill" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "shovels", $sel);
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="shovels" id="shovels" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "accommodation", $sel);
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="accommodation" id="accommodation" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "curvature", $sel);
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="curvature" id="curvature" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "size", $sel);
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="size" id="size" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "rack", $sel);
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="rack" id="rack" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "temper", $sel);
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="temper" id="temper" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "material", $sel);
print '<td align="center" class="bordertop nobottom"><input type="hidden" name="material" id="material" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
$sal = str_replace("@@", "parameters", $sel);
print '<td align="center" class="bordertop nobottom"><input type="hidden"  name="parameters" id="parameters" class="Checkid flat right" size="10" value="">'.$sal;
print '</td>';

$coldisplay++;
print '<td align="center" class="bordertop nobottom"><input type="number"  name="box" id="box" class="Checkid flat right" size="5" value="">';
print '</td>';

$coldisplay++;
print '<td width="20%" class="bordertop nobottom "><textarea style="font-size: 10pt;" name="notes" id="notes" class="Checkid flat " rows="4" cols="20"> </textarea>';
print '</td>';

print '<td class="bordertop nobottom ">';
print '</td>';



/*$coldisplay += $colspan;
print '<td class="bordertop nobottom linecoledit center valignmiddle" colspan="5">';
print '<input type="submit" class="button" value="'.$langs->trans('Add').'" name="addline" id="addline">';
print '</td>';*/
print '</tr>';

print '<tr>';
//$coldisplay += $colspan;
print '<td class="bordertop nobottom linecoledit center valignmiddle" colspan="17">';
print '<input type="" class="button" value="'.$langs->trans('Add').'" name="addline" id="addline"></br></br>';

print '</td>';
print '</tr>';


if (is_object($objectline)) {
	print $objectline->showOptionals($extrafields, 'edit', array('style'=>$bcnd[$var], 'colspan'=>$coldisplay), '', '', 1);
}
?>

<script>

/* JQuery for product free or predefined select */
jQuery(document).ready(function() {
	/* When changing predefined product, we reload list of supplier prices required for margin combo */
	$("#idprod").change(function()
	{
		console.log("#idprod change triggered");

  		/* To set focus */
  		if (jQuery('#idprod').val() > 0)
  	  	{
			/* focus work on a standard textarea but not if field was replaced with CKEDITOR */
			jQuery('#dp_desc').focus();
			/* focus if CKEDITOR */
			if (typeof CKEDITOR == "object" && typeof CKEDITOR.instances != "undefined")
			{
				var editor = CKEDITOR.instances['dp_desc'];
   				if (editor) { editor.focus(); }
			}
  	  	}
	});
	
	
	
});

</script>

<!-- END PHP TEMPLATE objectline_create.tpl.php -->
