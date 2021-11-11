<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
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
 */



// Load Dolibarr environment
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
// Get parameters
$action = GETPOST('action', 'aZ09');
$id = GETPOST('id', 'int');

if($action == "Product_extra_vals")
{
	$Resp = array();
	$objprod = new Product($db);
	$objprod->type = 0; // so test later to fill $usercancxxx is correct
	$extrafieldsp = new ExtraFields($db);
	// fetch optionals attributes and labels
	
	
	$extrafieldsp->fetch_name_optionals_label($objprod->table_element);
	
	
	$backup = array();
	foreach($extrafieldsp->attribute_label as $key => $val)
	{
		$backup["options_".$key] = "0";
	}

	if ($id > 0 )
	{
	
		$objprod->fetch($id);
		
		foreach($objprod->array_options as $key => $opt)
		{
			$objprod->array_options[$key] = $opt == null ? "0" : $opt;
		}
		$Resp["Res"] = "Success";
		$Resp["V"] = count($objprod->array_options) == 0 ? $backup : $objprod->array_options;
	
	}
	else
	{
		$Resp["Res"] = "Success";
		$Resp["V"] = $backup;
	}
		
	echo json_encode($Resp);
	exit;
}