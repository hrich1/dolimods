<?php
/* Copyright (C) 2005-2014 Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2014 Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2014      Marcos García		<marcosgdf@gmail.com>
 * Copyright (C) 2015      Bahfir Abbes        <bafbes@gmail.com>
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
 *  \file       htdocs/core/triggers/interface_90_all_Demo.class.php
 *  \ingroup    core
 *  \brief      Fichier de demo de personalisation des actions du workflow
 *  \remarks    Son propre fichier d'actions peut etre cree par recopie de celui-ci:
 *              - Le nom du fichier doit etre: interface_99_modMymodule_Mytrigger.class.php
 *				                           ou: interface_99_all_Mytrigger.class.php
 *              - Le fichier doit rester stocke dans core/triggers
 *              - Le nom de la classe doit etre InterfaceMytrigger
 *              - Le nom de la propriete name doit etre Mytrigger
 */
require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for demo module
 */
class InterfaceDueDate extends DolibarrTriggers
{

	public $family = 'others';
	public $picto = 'technic';
	public $description = "Triggers of this module are empty functions. They have no effect. They are provided for tutorial purpose only.";
	public $version = self::VERSION_DOLIBARR;

	/**
     * Function called when a Dolibarrr business event is done.
	 * All functions "runTrigger" are triggered if file is inside directory htdocs/core/triggers or htdocs/module/code/triggers (and declared)
     *
     * @param string		$action		Event action code
     * @param Object		$object     Object concerned. Some context information may also be provided into array property object->context.
     * @param User		    $user       Object user
     * @param Translate 	$langs      Object langs
     * @param conf		    $conf       Object conf
     * @return int         				<0 if KO, 0 if no triggered ran, >0 if OK
     */
    public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
    {
		// Put here code you want to execute when a Dolibarr business events occurs.
        // Data and type of action are stored into $object and $action
	    /*echo "<pre>";
				print_r($object);
				exit;*/
	    switch ($action) {
			
			// Bills
		    case 'BILL_CREATE':
				$this->updatedue($action, $object, $user, $langs, $conf);
		    break;
		    case 'BILL_MODIFY':
				$this->updatedue($action, $object, $user, $langs, $conf);
				/*echo "<pre>";
				print_r($object);
				exit;*/
		    break;
			    
	    }

        return 0;
	}
	
	function updatedue($action, $object, User $user, Translate $langs, Conf $conf)
	{
				if($object->thirdparty)
				{
					$socio = $object->thirdparty;
				}
				else
				{
					$socio = new Societe($object->db);
					$socio->fetch($object->socid);
					
				}
				
				//diavencimiento
				if(isset($socio->array_options['options_divenc']) && $socio->array_options['options_divenc']!="")
				{
					$DueDay = $socio->array_options['options_divenc'];
					
			    	$DateFact =  date('Y-m-d', $object->date);
			    	$DateP =  explode('-', $DateFact);
	
			    	$DateD = date('Y-m-d', strtotime($DateP[0]."-".$DateP[1]."-".$DueDay));
			    	
			    	$Fact = new Facture($object->db);
			    	$Fact->fetch($object->id);
			    	
			    	if($DateD >= $DateFact)
			    	{
				    	$DateD = dol_mktime(12, 0, 0, $DateP[1], $DueDay, $DateP[0]);
				    	//$Fact->array_options['options_fvence'] = $DateD;
				    	$Fact->date_lim_reglement = $DateD;
				    	
			    	}
			    	else
			    	{
				    	//echo  "de==>".$this->add_date($DateP[0]."-".$DateP[1]."-01",0,1,0);
				    	$NewDueP =  explode('-', $this->add_date($DateP[0]."-".$DateP[1]."-01",0,1,0));
				    	//$NewDue = date('Y-m-d', strtotime($NewDueP[0]."-".$NewDueP[1]."-".$DueDay));
				    	$NewDue = dol_mktime(12, 0, 0, $NewDueP[1], $DueDay, $NewDueP[0]);
				    	//$Fact->array_options['options_fvence'] = $NewDue;
				    	$Fact->date_lim_reglement = $NewDue;
				    	//echo $NewDue;
			    	}
			    	
			    	//$result = $Fact->insertExtraFields(); for update extrafields
			    	$result = $Fact->update($user);
			    	if ($result <= 0)
					{
						$this->error = $Fact->error;
						$this->errors[] =$Fact->error;
						dol_syslog("Trigger '".$this->name."' in action '".$action."' ERROR ".$error, LOG_ERR);
						return -1;
							
					}
					else
					{
						dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
	
						return 1;
					}
				
				}
				else
				{
					return 1;
				}
	}
	
	function add_date($givendate,$day=0,$mth=0,$yr=0) {
      $cd = strtotime($givendate);
      $newdate = date('Y-m-d h:i:s', mktime(date('h',$cd),
    date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
    date('d',$cd)+$day, date('Y',$cd)+$yr));
      return $newdate;
              }

}
