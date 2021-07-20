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

-- DROP TABLE llx_multiprodof_quality;

CREATE TABLE llx_multiprodof_quality (
-- BEGIN MODULEBUILDER FIELDS
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY NOT NULL,
  ref varchar(128) NOT NULL DEFAULT '(PROV)',
  entity integer NOT NULL DEFAULT 1,
  fk_muprof int(11) DEFAULT NULL,
  appearance enum('YES','NO') DEFAULT 'NO',
  fill enum('YES','NO') DEFAULT 'NO',
  shovels enum('YES','NO') DEFAULT 'NO',
  accommodation enum('YES','NO') DEFAULT 'NO',
  curvature enum('YES','NO') DEFAULT 'NO',
  size enum('YES','NO') DEFAULT 'NO',
  rack enum('YES','NO') DEFAULT 'NO',
  temper enum('YES','NO') DEFAULT 'NO',
  material enum('YES','NO') DEFAULT 'NO',
  parameters enum('YES','NO') DEFAULT 'NO',
  box integer DEFAULT NULL,
  notes text DEFAULT NULL,
  date_creation datetime NOT NULL,
  tms timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  fk_user_creat integer NOT NULL,
  fk_user_modif integer DEFAULT NULL,
  import_key varchar(14) DEFAULT 'NULL'
  -- END MODULEBUILDER FIELDS
  ) ENGINE=innodb;
