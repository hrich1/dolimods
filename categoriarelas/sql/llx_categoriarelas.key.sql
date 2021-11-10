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


CREATE TABLE `llx_product_extra_relas` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `campos` text DEFAULT NULL,
	  `accion` int(2) DEFAULT NULL,
	  `campo_condicion` varchar(100) DEFAULT NULL,
	  `condicion` int(2) DEFAULT NULL,
	  `valor` varchar(100) DEFAULT NULL,
	  `status` int(2) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4;

ALTER TABLE llx_bom_bomline_extrafields ADD COLUMN pcs int(10) NULL;
ALTER TABLE llx_bom_bomline_extrafields ADD COLUMN xtra_camps text NULL;
