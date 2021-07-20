-- Copyright (C) 2019      Laurent Destailleur  <eldy@users.sourceforge.net>
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
-- along with this program.  If not, see http://www.gnu.org/licenses/.

ALTER TABLE llx_multiprodof_production ADD CONSTRAINT fk_multiprodof_production_muprof FOREIGN KEY (fk_muprof) REFERENCES llx_multiprodof_muprof (rowid);
ALTER TABLE llx_multiprodof_production ADD CONSTRAINT fk_multiprodof_production_product FOREIGN KEY (fk_product) REFERENCES llx_product (rowid);
ALTER TABLE llx_multiprodof_production ADD CONSTRAINT fk_multiprodof_production_stock_movement FOREIGN KEY (fk_stock_movement) REFERENCES llx_stock_mouvement (rowid);

ALTER TABLE llx_multiprodof_production ADD INDEX idx_multiprodof_production_fk_muprof (fk_muprof);

