-- Copyright (C) ---Put here your own copyright and developer email---
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


CREATE TABLE llx_multiprodof_muprof(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	ref varchar(128) DEFAULT '(PROV)' NOT NULL, 
	entity integer DEFAULT 1 NOT NULL, 
	fk_warehouse integer,
	fk_soc integer,
	temper varchar(50) ,
	water_circulates varchar(50),
	rack_in_temp varchar(50),
	ejector_safety varchar(50),
	mold_cleaning varchar(50),
	dater varchar(50) ,
	numermolde varchar(25) ,
	notes text,
	signature datetime,
	fk_user_quality integer,
	note_public text, 
	note_private text, 
	date_creation datetime NOT NULL, 
	date_valid datetime NULL,
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer,
	fk_user_valid integer,
	import_key varchar(14),
	model_pdf varchar(255),
	status integer NOT NULL, 
	date_start_planned datetime, 
	date_end_planned datetime, 
	fk_project integer
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;