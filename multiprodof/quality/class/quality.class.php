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

/**
 * \file        multprodof/quality/class/quality.class.php
 * \ingroup     multiprodof
 * \brief       This file is a CRUD class file for Quality (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

/**
 * Class for Quality
 */
class Quality extends CommonObject
{
	public $ismultientitymanaged = 0;

	/**
	 * @var int  Does quality support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 0;
	
	public $picto = 'quality';
	
	
	public $table_element_line = 'multiprodof_quality';

	/**
	 * @var string    Field with ID of parent key if this field has a parent
	 */
	public $element = 'muprof';

	/**
	 * @var string    Name of subtable class that manage subtable lines
	 */
	public $class_element_line = 'QualityLine';

	/**
	 * @var array	List of child tables. To test if we can delete object.
	 */
	protected $childtables = array();

	/**
	 * @var array	List of child tables. To know object to delete on cascade.
	 */
	protected $childtablesoncascade = array('multiprodof_quality');

	/**
	 * @var QualityLine[]     Array of subtable lines
	 */
	public $lines = array();
	
	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf, $langs;

		$this->db = $db;
	}
	
	public function fetchLines()
	{
		$this->lines = array();
		$result = $this->fetchLinesCommon();
		return $result;
	}
	
	/**
	 *  Delete a line of object in database
	 *
	 *	@param  User	$user       User that delete
	 *  @param	int		$idline		Id of line to delete
	 *  @param 	bool 	$notrigger  false=launch triggers after, true=disable triggers
	 *  @return int         		>0 if OK, <0 if KO
	 */
	public function deleteLine(User $user, $idline, $notrigger = false)
	{
		global $object;
		if ($object->status == 9)
		{
			$this->error = 'ErrorDeleteLineNotAllowedByObjectStatus';
			return -2;
		}

		return $this->deleteLineCommon($user, $idline, $notrigger);
	}
}

/**
 * Class for QualityLines
 */
class QualityLine extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'multiprodof_quality';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'multiprodof_quality';

	/**
	 * @var int  Does quality support multicompany module ? 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	 */
	public $ismultientitymanaged = 0;


	/**
	 * @var int  Does moline support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 0;



	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields = array(
		'rowid' => array('type'=>'integer', 'label'=>'TechnicalID', 'enabled'=>1, 'visible'=>-1, 'position'=>1, 'notnull'=>1, 'index'=>1, 'comment'=>"Id",),
		'entity' => array('type'=>'integer', 'label'=>'Entity', 'enabled'=>1, 'visible'=>0, 'position'=>5, 'notnull'=>1, 'default'=>'1', 'index'=>1),
		'ref' => array('type'=>'varchar(128)', 'label'=>'Ref', 'enabled'=>1, 'visible'=>4, 'position'=>10, 'notnull'=>1, 'default'=>'(PROV)', 'index'=>1, 'searchall'=>1, 'comment'=>"Reference of object", 'showoncombobox'=>'1', 'noteditable'=>1),
		'fk_muprof' =>array('type'=>'integer', 'label'=>'MuPrOf', 'enabled'=>1, 'visible'=>-1, 'notnull'=>1, 'position'=>15),
		
		'appearance' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Appearance', 'enabled'=>1, 'visible'=>1, 'position'=>20),
		'fill' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Fill', 'enabled'=>1, 'visible'=>1, 'position'=>25),
		'shovels' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Shovels', 'enabled'=>1, 'visible'=>1, 'position'=>30),
		'accommodation' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Accommodation', 'enabled'=>1, 'visible'=>1, 'position'=>35),
		'curvature' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Curvature', 'enabled'=>1, 'visible'=>1, 'position'=>40),
		'size' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Size', 'enabled'=>1, 'visible'=>1, 'position'=>45),
		'rack' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Rack', 'enabled'=>1, 'visible'=>1, 'position'=>50),
		'temper' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Temper', 'enabled'=>1, 'visible'=>1, 'position'=>55),
		'material' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Material', 'enabled'=>1, 'visible'=>1, 'position'=>60),
		'parameters' => array('type'=>'enum(\'YES\',\'NO\')', 'label'=>'Parameters', 'enabled'=>1, 'visible'=>1, 'position'=>65),
		'box' => array('type'=>'integer', 'label'=>'Box', 'enabled'=>1, 'visible'=>1, 'position'=>67),
		'notes' => array('type'=>'html', 'label'=>'Notes', 'enabled'=>1, 'visible'=>1, 'position'=>70, 'notnull'=>-1),
		
		'date_creation' => array('type'=>'datetime', 'label'=>'Signature', 'enabled'=>1, 'visible'=>0, 'position'=>100, 'notnull'=>1,),
		'tms' => array('type'=>'timestamp', 'label'=>'DateModification', 'enabled'=>1, 'visible'=>-2, 'position'=>110, 'notnull'=>1,),
		'fk_user_creat' => array('type'=>'integer', 'label'=>'Operator', 'enabled'=>1, 'visible'=>0, 'position'=>120, 'notnull'=>1, 'foreignkey'=>'user.rowid',),
		'fk_user_modif' => array('type'=>'integer', 'label'=>'UserModif', 'enabled'=>1, 'visible'=>-2, 'position'=>130, 'notnull'=>-1,),
		'import_key' => array('type'=>'varchar(14)', 'label'=>'ImportId', 'enabled'=>1, 'visible'=>-2, 'position'=>1000, 'notnull'=>-1,),
	);
	public $rowid;
	public $ref;
	public $entity;
	public $fk_muprof;
	
	public $appearance;
	public $fill;
	public $shovels;
	public $accommodation;
	public $curvature;
	public $size;
	public $rack;
	public $temper;
	public $material;
	public $parameters;
	public $notes;
	
	public $date_creation;
	public $tms;
	public $fk_user_creat;
	public $fk_user_modif;
	public $import_key;
	





	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf, $langs;

		$this->db = $db;

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) $this->fields['rowid']['visible'] = 0;
		if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) $this->fields['entity']['enabled'] = 0;

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val)
		{
			if (isset($val['enabled']) && empty($val['enabled']))
			{
				unset($this->fields[$key]);
			}
		}

		// Translate some data of arrayofkeyval
		foreach ($this->fields as $key => $val)
		{
			if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval']))
			{
				foreach ($val['arrayofkeyval'] as $key2 => $val2)
				{
					$this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
				}
			}
		}
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <=0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		global $conf;

		$error = 0;
		$idcreated = 0;

		$this->db->begin();


		// Check that product is not a kit/virtual product
		if (!$error) {
			$idcreated = $this->createCommon($user, $notrigger);
			if ($idcreated <= 0) {
				$error++;
			}
		}


		if (!$error) {
			$this->db->commit();
		} else {
			$this->db->rollback();
		}

		return $idcreated;
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id   Id object
	 * @param string $ref  Ref
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		$this->lines = array();
		$result = $this->fetchCommon($id);
		return $result;
		
	}
		/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		global $langs;

		$error = 0;

		$this->db->begin();

		$result = $this->updateCommon($user, $notrigger);
		if ($result <= 0) {
			$error++;
		}

		if (!$error) {
			setEventMessages($langs->trans("RecordModifiedSuccessfully"), null, 'mesgs');
			$this->db->commit();
			return 1;
		} else {
			setEventMessages($this->error, $this->errors, 'errors');
			$this->db->rollback();
			return -1;
		}
	}
}