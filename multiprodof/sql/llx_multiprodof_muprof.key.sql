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

-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_multiprodof_muprof ADD INDEX idx_multiprodof_muprof_ref (ref);
ALTER TABLE llx_multiprodof_muprof ADD INDEX idx_multiprodof_muprof_entity (entity);
ALTER TABLE llx_multiprodof_muprof ADD INDEX idx_multiprodof_muprof_fk_soc (fk_soc);
ALTER TABLE llx_multiprodof_muprof ADD CONSTRAINT fk_multiprodof_muprof_fk_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user(rowid);
ALTER TABLE llx_multiprodof_muprof ADD INDEX idx_multiprodof_muprof_status (status);
ALTER TABLE llx_multiprodof_muprof ADD INDEX idx_multiprodof_muprof_date_start_planned (date_start_planned);
ALTER TABLE llx_multiprodof_muprof ADD INDEX idx_multiprodof_muprof_date_end_planned (date_end_planned);
ALTER TABLE llx_multiprodof_muprof ADD INDEX idx_multiprodof_muprof_fk_project (fk_project);
ALTER TABLE llx_multiprodof_muprof ADD COLUMN numermolde varchar(25);
-- END MODULEBUILDER INDEXES


-- UPGRADE TO MULTIPLE ORDERS FROM MUPROF 2021-06-12

ALTER TABLE llx_commande_extrafields ADD COLUMN fk_muprof int(10);
ALTER TABLE llx_commande_extrafields ADD COLUMN fk_muprof_ref varchar(128);
ALTER TABLE llx_commandedet_extrafields ADD COLUMN fk_muprof_line int(10);

INSERT INTO llx_extrafields (`name`, `entity`, `elementtype`, `label`, `type`, `size`, `fieldcomputed`, `fielddefault`, `fieldunique`, `fieldrequired`, `perms` , `enabled`, `pos` , `alwayseditable`, `param` , `list` , `totalizable` , `langs` , `fk_user_author` , `fk_user_modif` , `datec` , `tms` , `help` , `printable`
) VALUES ('fk_muprof_line', '1', 'commandedet', 'Línea de OF. Múltiple', 'int', '10', null, null, '0', '0', null, '1', '100', '0', 'a:1:{s:7:\"options\";a:1:{s:0:\"\";N;}}', '5', '0', null, '1', '1', NOW(), NOW(), 'Línea de OF múltiple relacionada', '0'), ('fk_muprof', '1', 'commande', 'Id OF. Múltiple', 'int', '10', null, null, '0', '0', null, '1', '100', '0', 'a:1:{s:7:\"options\";a:1:{s:0:\"\";N;}}', '0', '0', null, '1', '1', NOW(), NOW(), 'OF. múltiple relacionada', '0'), ('fk_muprof_ref', '1', 'commande', 'OF. Múltiple', 'varchar', '128', null, null, '0', '0', null, '1', '100', '0', 'a:1:{s:7:\"options\";a:1:{s:0:\"\";N;}}', '5', '0', null, '1', '1', NOW(), NOW(), 'OF. múltiple relacionada', '0');
-- UPGRADE TO MULTIPLE ORDERS FROM OF 2021-06-12