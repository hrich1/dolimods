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
 * $element     (used to test $user->rights->$element->creer)
 * $permtoedit  (used to replace test $user->rights->$element->creer)
 * $inputalsopricewithtax (0 by default, 1 to also show column with unit price including tax)
 * $outputalsopricetotalwithtax
 * $usemargins (0 to disable all margins columns, 1 to show according to margin setup)
 *
 * $type, $text, $description, $line
 */

// Protection to avoid direct call of template
if (empty($object) || !is_object($object))
{
	print "Error, template page can't be called as URL";
	exit;
}
print "<!-- BEGIN PHP TEMPLATE objectline_title.tpl.php -->\n";
// Title line
print "<thead>\n";

print '<tr  class="liste_titre nodrag nodrop">';

// Adds a line numbering column
if (!empty($conf->global->MAIN_VIEW_LINE_NUMBER)) print '<td class="linecolnum center">&nbsp;</td>';

print '<td align="center" class="linecoldescription">'.$langs->trans('appearance').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('fill').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('shovels').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('accommodation').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('curvature').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('size').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('rack').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('temper').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('material').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('parameters').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('box').'</td>';
print '<td class="linecoldescription">'.$langs->trans('notes').'</td>';
print '<td align="center" class="linecoldescription">'.$langs->trans('Signature').'</td>';
/*print '<td class="linecoldescription">'.$langs->trans('date_q').'</td>';
print '<td class="linecoldescription">'.$langs->trans('hour_q').'</td>';
print '<td class="linecoldescription">'.$langs->trans('Signature').'</td>';*/




/*if ($action == 'selectlines')
{
	print '<td class="linecolcheckall center">';
	print '<input type="checkbox" class="linecheckboxtoggle" />';
	print '<script>$(document).ready(function() {$(".linecheckboxtoggle").click(function() {var checkBoxes = $(".linecheckbox");checkBoxes.prop("checked", this.checked);})});</script>';
	print '</td>';
}
*/
print "</tr>\n";
print "</thead>\n";

print "<!-- END PHP TEMPLATE objectline_title.tpl.php -->\n";
