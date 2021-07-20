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

 if($action=="addlinequality"  && !empty($permissiontoaddquality))
{
	// Action to add record
	$QualityL = new QualityLine($db);
	
	foreach ($QualityL->fields as $key => $val)
	{
		if ($QualityL->fields[$key]['type'] == 'duration') {
			if (GETPOST($key.'hour') == '' && GETPOST($key.'min') == '') continue; // The field was not submited to be edited
		} else {
			if (!GETPOSTISSET($key)) continue; // The field was not submited to be edited
		}
		// Ignore special fields
		if (in_array($key, array('rowid', 'entity', 'import_key'))) continue;
		if (in_array($key, array('date_creation', 'tms', 'fk_user_creat', 'fk_user_modif'))) {
			if (!in_array(abs($val['visible']), array(1, 3))) continue; // Only 1 and 3 that are case to create
		}

		// Set value to insert
		if (in_array($QualityL->fields[$key]['type'], array('text', 'html'))) {
			$value = GETPOST($key, 'restricthtml');
		} elseif ($QualityL->fields[$key]['type'] == 'date') {
			$value = dol_mktime(12, 0, 0, GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int'));	// for date without hour, we use gmt
		} elseif ($QualityL->fields[$key]['type'] == 'datetime') {
			$value = dol_mktime(GETPOST($key.'hour', 'int'), GETPOST($key.'min', 'int'), GETPOST($key.'sec', 'int'), GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int'), 'tzuserrel');
		} elseif ($QualityL->fields[$key]['type'] == 'duration') {
			$value = 60 * 60 * GETPOST($key.'hour', 'int') + 60 * GETPOST($key.'min', 'int');
		} elseif (preg_match('/^(integer|price|real|double)/', $QualityL->fields[$key]['type'])) {
			$value = price2num(GETPOST($key, 'alphanohtml')); // To fix decimal separator according to lang setup
		} elseif ($QualityL->fields[$key]['type'] == 'boolean') {
			$value = ((GETPOST($key) == '1' || GETPOST($key) == 'on') ? 1 : 0);
		} elseif ($QualityL->fields[$key]['type'] == 'reference') {
			$tmparraykey = array_keys($QualityL->param_list);
			$value = $tmparraykey[GETPOST($key)].','.GETPOST($key.'2');
		} else {
			$value = GETPOST($key, 'alphanohtml');
		}
		if (preg_match('/^integer:/i', $QualityL->fields[$key]['type']) && $value == '-1') $value = ''; // This is an implicit foreign key field
		if (!empty($QualityL->fields[$key]['foreignkey']) && $value == '-1') $value = ''; // This is an explicit foreign key field

		//var_dump($key.' '.$value.' '.$object->fields[$key]['type']);
		$QualityL->$key = $value;
		if ($val['notnull'] > 0 && $QualityL->$key == '' && !is_null($val['default']) && $val['default'] == '(PROV)')
		{
			$QualityL->$key = '(PROV)';
		}
		if ($val['notnull'] > 0 && $QualityL->$key == '' && is_null($val['default']))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv($val['label'])), null, 'errors');
		}
	}

	// Fill array 'array_options' with data from add form
	if (!$error) {
		$ret = $extrafields->setOptionalsFromPost(null, $QualityL);
		if ($ret < 0) $error++;
	}
	
	
	if (!$error)
	{
		$result = $QualityL->create($user);
		if ($result > 0)
		{
			// Creation OK
			$urltogo = $backtopage ? str_replace('__ID__', $result, $backtopage) : $backurlforlist;
			$urltogo = preg_replace('/--IDFORBACKTOPAGE--/', $QualityL->id, $urltogo); // New method to autoselect project after a New on another form object creation
			echo $urltogo;
			header("Location: ".$urltogo);
			exit;
		} else {
			// Creation KO
			if (!empty($QualityL->errors)) setEventMessages(null, $QualityL->errors, 'errors');
			else setEventMessages($QualityL->error, null, 'errors');
			$action = 'create';
		}
	} else {
		$action = 'create';
	}

}

// Action to update record
if ($action == 'updatequality' && !empty($permissiontoaddquality))
{
	$QualityL = new QualityLine($db);
	$QualityL->fetch($lineid);
	foreach ($QualityL->fields as $key => $val)
	{
		// Check if field was submited to be edited
		if ($QualityL->fields[$key]['type'] == 'duration') {
			if (!GETPOSTISSET($key.'hour') || !GETPOSTISSET($key.'min')) continue; // The field was not submited to be edited
		} elseif ($QualityL->fields[$key]['type'] == 'boolean') {
			if (!GETPOSTISSET($key)) {
				$QualityL->$key = 0; // use 0 instead null if the field is defined as not null
				continue;
			}
		} else {
			if (!GETPOSTISSET($key)) continue; // The field was not submited to be edited
		}
		// Ignore special fields

		if (in_array($key, array('rowid', 'entity', 'import_key', 'date_creation', 'fk_user_creat', 'tms'))) continue;
		if (in_array($key, array('date_creation', 'tms', 'fk_user_creat', 'fk_user_modif'))) {
			if (!in_array(abs($val['visible']), array(1, 3, 4))) continue; // Only 1 and 3 and 4 that are case to update
		}

		// Set value to update
		if (preg_match('/^(text|html)/', $QualityL->fields[$key]['type'])) {
			$tmparray = explode(':', $QualityL->fields[$key]['type']);
			if (!empty($tmparray[1])) {
				$value = GETPOST($key, $tmparray[1]);
			} else {
				$value = GETPOST($key, 'restricthtml');
			}
		} elseif ($QualityL->fields[$key]['type'] == 'date') {
			$value = dol_mktime(12, 0, 0, GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int'));	// for date without hour, we use gmt
		} elseif ($QualityL->fields[$key]['type'] == 'datetime') {
			$value = dol_mktime(GETPOST($key.'hour', 'int'), GETPOST($key.'min', 'int'), GETPOST($key.'sec', 'int'), GETPOST($key.'month', 'int'), GETPOST($key.'day', 'int'), GETPOST($key.'year', 'int'), 'tzuserrel');
		} elseif ($QualityL->fields[$key]['type'] == 'duration') {
			if (GETPOST($key.'hour', 'int') != '' || GETPOST($key.'min', 'int') != '') {
				$value = 60 * 60 * GETPOST($key.'hour', 'int') + 60 * GETPOST($key.'min', 'int');
			} else {
				$value = '';
			}
		} elseif (preg_match('/^(integer|price|real|double)/', $QualityL->fields[$key]['type'])) {
			$value = price2num(GETPOST($key, 'alphanohtml')); // To fix decimal separator according to lang setup
		} elseif ($QualityL->fields[$key]['type'] == 'boolean') {
			$value = ((GETPOST($key, 'aZ09') == 'on' || GETPOST($key, 'aZ09') == '1') ? 1 : 0);
		} elseif ($QualityL->fields[$key]['type'] == 'reference') {
			$value = array_keys($QualityL->param_list)[GETPOST($key)].','.GETPOST($key.'2');
		} else {
			$value = GETPOST($key, 'alpha');
		}
		if (preg_match('/^integer:/i', $QualityL->fields[$key]['type']) && $value == '-1') $value = ''; // This is an implicit foreign key field
		if (!empty($QualityL->fields[$key]['foreignkey']) && $value == '-1') $value = ''; // This is an explicit foreign key field

		$QualityL->$key = $value;
		if ($val['notnull'] > 0 && $QualityL->$key == '' && is_null($val['default']))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv($val['label'])), null, 'errors');
		}
	}

	// Fill array 'array_options' with data from add form
	if (!$error) {
		$ret = $extrafields->setOptionalsFromPost(null, $QualityL, '@GETPOSTISSET');
		if ($ret < 0) $error++;
	}

	if (!$error)
	{

		$result = $QualityL->update($user);

		if ($result > 0)
		{
			$action = 'view';
		} else {
			// Creation KO
			setEventMessages($QualityL->error, $QualityL->errors, 'errors');
			$action = 'edit';
		}
	} else {
		$action = 'edit';
	}
}

// Remove a line
if ($action == 'confirm_deleteline' && $confirm == 'yes' && !empty($permissiontoaddquality))
{
	$Quality = new Quality($db);
	$Quality->id = $id;
	if (method_exists($Quality, 'deleteline')) {
		$result = $Quality->deleteline($user, $lineid); // For backward compatibility
	} else {
		$result = $Quality->deleteLine($user, $lineid);
	}
	if ($result > 0)
	{
		// Define output language
		$outputlangs = $langs;

		setEventMessages($langs->trans('RecordDeleted'), null, 'mesgs');
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$Quality->id);
		exit;
	} else {
		echo "holl";
		setEventMessages($Quality->error, $Quality->errors, 'errors');
	}
}
