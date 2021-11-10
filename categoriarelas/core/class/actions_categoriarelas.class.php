<?php
/* Copyright (C) 2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2020  Lenin Rivas		   <lenin@leninrivas.com>
 * Copyright (C) ---Put here your own copyright and developer email---
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

 if($action=="addlinerule")
{
	$db->begin();

	$fields = json_encode(GETPOST('fields', 'array'));
	$accione = GETPOST('accione', 'alpha');
	$campo_condi = 0;
	$condicion = 0;
	$valor = 0;
	if($accione==2)
	{
		$campo_condi= GETPOST('vale', 'alpha');
		$condicion = GETPOST('condicion', 'alpha');
		$valor = GETPOST('vali', 'alpha');
	}
	if($condicion==3)
	{
		$valor = 0;
	}
	$status = GETPOST('status', 'int');

	// Insertion dans base de la ligne
	$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'product_extra_relas';
	$sql .= ' (campos, accion, campo_condicion, condicion, valor, status';
	$sql .= ')';
	$sql .= " VALUES ('".$db->escape($fields)."',";
	$sql .= " '".$accione."',";
	$sql .= " '".$campo_condi."',";
	$sql .= " '".$condicion."',";
	$sql .= " '".$valor."',";
	$sql .= " '".$status."'";
	$sql .= ')';

	//echo $sql;
	//exit;

	dol_syslog(get_class($object)."::insert", LOG_DEBUG);
	$resql = $db->query($sql);

	if ($resql) {
		//setEventMessages($langs->trans("RecordModifiedSuccessfully"), null, 'mesgs');
		setEventMessages($langs->trans("RecorAddedSuccessfully"), null, 'mesgs');
		$idi = $db->last_insert_id(MAIN_DB_PREFIX.'product_extra_relas');
		$db->commit();
		return 1;
	} else {
		setEventMessages($object->error, $object->errors, 'errors');
		$db->rollback();
		return -1;
	}

}

// Action to update record
if ($action == 'updatelinerule')
{
	$db->begin();

	$fields = json_encode(GETPOST('fields', 'array'));
	$accione = GETPOST('accione', 'alpha');
	$campo_condi = 0;
	$condicion = 0;
	$valor = 0;
	if($accione==2)
	{
		$campo_condi= GETPOST('vale', 'alpha');
		$condicion = GETPOST('condicion', 'alpha');
		$valor = GETPOST('vali', 'alpha');
	}
	if($condicion==3)
	{
		$valor = 0;
	}
	$status = GETPOST('status', 'int');


	// Insertion dans base de la ligne
	$sql = "UPDATE ".MAIN_DB_PREFIX."product_extra_relas";
	$sql .= " set campos = '".$db->escape($fields)."', accion = '".$accione."', campo_condicion = '".$campo_condi."', condicion = '".$condicion."', valor = '".$valor."', status = '".$status."' where id = '".$lineid."'";

	//echo $sql;
	//exit;

	dol_syslog(get_class($object)."::update", LOG_DEBUG);
	$resql = $db->query($sql);

	if ($resql) {
		setEventMessages($langs->trans("RecordModifiedSuccessfully"), null, 'mesgs');

		$db->commit();
		return 1;
	} else {
		setEventMessages($object->error, $object->errors, 'errors');
		$db->rollback();
		return -1;
	}
}

// Remove a line
if ($action == 'confirm_deleteline' )
{
	$db->begin();

	$fields = json_encode(GETPOST('fields', 'array'));
	$accione = GETPOST('accione', 'alpha');
	$campo_condi= GETPOST('vale', 'alpha');
	$condicion= GETPOST('condicion', 'alpha');
	$valor = GETPOST('vali', 'alpha');
	$status = GETPOST('status', 'int');


	// Insertion dans base de la ligne
	$sql = " DELETE FROM ".MAIN_DB_PREFIX."product_extra_relas ";
	$sql .= " where id = '".$lineid."' ";

	//echo $sql;
	//exit;

	dol_syslog(get_class($object)."::delete", LOG_DEBUG);
	$resql = $db->query($sql);

	if ($resql) {
		setEventMessages($langs->trans("RecordDeletedSuccessfully"), null, 'mesgs');

		$db->commit();
		return 1;
	} else {
		setEventMessages($object->error, $object->errors, 'errors');
		$db->rollback();
		return -1;
	}
}
