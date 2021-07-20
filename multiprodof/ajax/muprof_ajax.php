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
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/multiprodof/class/muprof.class.php';

// Get parameters
$action = GETPOST('action', 'aZ09');
$id = GETPOST('id', 'int');
$order = GETPOST('order', 'int');
$peds = GETPOST('peds', 'array');

if($action == "RevalidateMuprof")
{
	$Resp = array();
	if($id>0 && count($peds) > 0)
	{
		$MuPrOf = new MuPrOf($db);
		$MuPrOf->fetch($id);
		if($order > 0)
			$valid = $MuPrOf->CountLinesInOrder($peds, $order);
		else
			$valid = $MuPrOf->CountLinesInOrder($peds);
			
		$Resp["Res"] = "Success";
		$Resp["V"] = $valid;
	}
	else
	{
		$Resp["Res"] = "Error";
	}
		
	echo json_encode($Resp);
	exit;
}