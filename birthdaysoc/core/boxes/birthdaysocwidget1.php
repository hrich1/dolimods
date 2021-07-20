<?php
/* Copyright (C) 2004-2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2020  Frédéric France     <frederic.france@netlogic.fr>
 * Copyright (C) 2021 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    birthdaysoc/core/boxes/birthdaysocwidget1.php
 * \ingroup birthdaysoc
 * \brief   Widget provided by BirthdaySoc
 *
 * Put detailed description here.
 */

/** Includes */
include_once DOL_DOCUMENT_ROOT."/core/boxes/modules_boxes.php";

/**
 * Class to manage the box
 *
 * Warning: for the box to be detected correctly by dolibarr,
 * the filename should be the lowercase classname
 */
class birthdaysocwidget1 extends ModeleBoxes
{
	/**
	 * @var string Alphanumeric ID. Populated by the constructor.
	 */

	/**
	 * @var string Box icon (in configuration page)
	 * Automatically calls the icon named with the corresponding "object_" prefix
	 */
	public $boximg = "object_company";

	/**
	 * @var string Box label (in configuration page)
	 */
	public $boxlabel = "BoxTitleUserBirthdaysOfSoc";

	/**
	 * @var string[] Module dependencies
	 */
	public $depends = array('societe');
	

	/**
	 * @var DoliDb Database handler
	 */
	public $db;

	/**
	 * @var mixed More parameters
	 */
	public $param;

	/**
	 * @var array Header informations. Usually created at runtime by loadBox().
	 */
	public $info_box_head = array();

	/**
	 * @var array Contents informations. Usually created at runtime by loadBox().
	 */
	public $info_box_contents = array();

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 * @param string $param More parameters
	 */
	public function __construct(DoliDB $db, $param = '')
	{
		global $user, $conf, $langs;
		$langs->load("boxes");
		$langs->load('birthdaysoc@birthdaysoc');

		parent::__construct($db, $param);

		$this->boxlabel = $langs->transnoentitiesnoconv("BoxTitleBirthdaysSoc");

		$this->param = $param;

		//$this->enabled = $conf->global->FEATURES_LEVEL > 0;         // Condition when module is enabled or not
		$this->hidden = ! ($user->rights->birthdaysoc->myobject->read);   // Condition when module is visible by user (test on permission)
	}

	/**
	 * Load data into info_box_contents array to show array later. Called by Dolibarr before displaying the box.
	 *
	 * @param int $max Maximum number of records to load
	 * @return void
	 */
	public function loadBox($max = 5)
	{
		global $user, $langs;
		$langs->load("boxes");

		$this->max = 100;//customer required all registers

		include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
		include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
        $userstatic = new Societe($this->db);

        $this->info_box_head = array('text' => $langs->trans("BoxTitleBirthdaysSoc"));

		if ($user->rights->birthdaysoc->myobject->read)
		{
			$tmparray = dol_getdate(dol_now(), true);

			$sql = "SELECT u.rowid, u.nom, o.nacimiento, u.email";
			$sql .= " FROM ".MAIN_DB_PREFIX."societe as u";
			$sql .= " JOIN ".MAIN_DB_PREFIX."societe_extrafields as o on u.rowid = o.fk_object";
			$sql .= " WHERE u.entity IN (".getEntity('user').")";
			$sql .= " AND u.status = 1";
			$sql .= " AND EXTRACT(MONTH FROM o.nacimiento ) = EXTRACT(MONTH FROM NOW() )";
			$sql .= " AND EXTRACT(DAY FROM o.nacimiento ) = EXTRACT(DAY FROM NOW() )";
			$sql .= " ORDER BY o.nacimiento ASC";
			$sql .= $this->db->plimit($max, 0);
			dol_syslog(get_class($this)."::loadBox", LOG_DEBUG);
			$result = $this->db->query($sql);
			if ($result)
			{
				$num = $this->db->num_rows($result);

				$line = 0;
				while ($line < $num)
				{
					$objp = $this->db->fetch_object($result);
                    $userstatic->id = $objp->rowid;
                    $userstatic->nom = $objp->nom;
                    $userstatic->email = $objp->email;
                    $dateb = $this->db->jdate($objp->nacimiento);
                    $age = date('Y', dol_now()) - date('Y', $dateb);

                    $this->info_box_contents[$line][] = array(
                        'td' => '',
                        'text' => $userstatic->getNomUrl(1),
                        'asis' => 1,
                    );

                    $this->info_box_contents[$line][] = array(
                        'td' => 'class="right"',
                        'text' => dol_print_date($dateb, "day").' - '.$age.' '.$langs->trans('DurationYears')
                    );

                    /*$this->info_box_contents[$line][] = array(
                        'td' => 'class="right" width="18"',
                        'text' => $userstatic->LibStatut($objp->status, 3)
                    );*/

					$line++;
				}

				if ($num == 0) $this->info_box_contents[$line][0] = array('td' => 'class="center opacitymedium"', 'text'=>$langs->trans("None"));

				$this->db->free($result);
			}
			else {
				$this->info_box_contents[0][0] = array(
                    'td' => '',
                    'maxlength'=>500,
                    'text' => ($this->db->error().' sql='.$sql)
                );
			}
		}
		else {
			$this->info_box_contents[0][0] = array(
			    'td' => 'class="nohover opacitymedium left"',
                'text' => $langs->trans("ReadPermissionNotAllowed")
			);
		}
	}
	

	/**
	 * Method to show box. Called by Dolibarr eatch time it wants to display the box.
	 *
	 * @param array $head       Array with properties of box title
	 * @param array $contents   Array with properties of box lines
	 * @param int   $nooutput   No print, only return string
	 * @return void
	 */
	public function showBox($head = null, $contents = null, $nooutput = 0)
	{
		// You may make your own code here…
		// … or use the parent's class function using the provided head and contents templates
		parent::showBox($this->info_box_head, $this->info_box_contents);
	}
}
