<?php
/*
 * Copyright (c) 2012-2016, Hofmänner New Media.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This file is part of the N2N FRAMEWORK.
 *
 * The N2N FRAMEWORK is free software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * N2N is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details: http://www.gnu.org/licenses/
 *
 * The following people participated in this project:
 *
 * Andreas von Burg.....: Architect, Lead Developer
 * Bert Hofmänner.......: Idea, Community Leader, Marketing
 * Thomas Günther.......: Developer, Hangar
 */
namespace n2n\impl\persistence\meta\oracle;

use n2n\persistence\meta\structure\common\CommonView;

use n2n\persistence\meta\structure\MetaEntityFactory;

class OracleMetaEntityFactory implements MetaEntityFactory {
	
	/**
	 * @var n2n\impl\persistence\meta\oracle\OracleDatabase;
	 */
	private $database;
	
	public function __construct(OracleDatabase $database) {
		$this->database = $database;
	}
	
	public function getDatabase() {
		return $this->database;
	}
	

	public function createTable($name) {
		$metaEntity = new OracleTable($name);
		$this->database->addMetaEntity($metaEntity);
		return $metaEntity;
	}
	
	public function createView($name, $query) {
		$metaEntity = new CommonView($name, $query);
		$this->database->addMetaEntity($metaEntity);
		return $metaEntity;
	}
}
