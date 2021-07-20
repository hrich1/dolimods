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
ALTER TABLE llx_multiprodof_quality ADD CONSTRAINT fk_multiprodof_quality_fk_muprof FOREIGN KEY (fk_muprof) REFERENCES llx_multiprodof_muprof(rowid);
ALTER TABLE llx_multiprodof_quality ADD CONSTRAINT fk_multiprodof_quality_fk_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user(rowid);
ALTER TABLE llx_multiprodof_quality ADD INDEX idx_multiprodof_quality_ref (ref);
ALTER TABLE llx_multiprodof_quality ADD INDEX idx_multiprodof_quality_entity (entity);
ALTER TABLE llx_multiprodof_quality ADD INDEX idx_multiprodof_quality_fk_muprof (fk_muprof);
-- END MODULEBUILDER INDEXES