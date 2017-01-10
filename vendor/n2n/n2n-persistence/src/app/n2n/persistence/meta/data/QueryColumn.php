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
namespace n2n\persistence\meta\data;

class QueryColumn implements QueryItem {
	private $tableAlias;
	private $columnName;
	
	public function __construct($columnName, $tableAlias = null) {
		$this->tableAlias = $tableAlias;
		$this->columnName = $columnName;
	}
	
	public function getTableAlias() {
		return $this->tableAlias;
	}
	
	public function getColumnName() {
		return $this->columnName;
	}
	
	public function buildItem(QueryFragmentBuilder $itemBuilder) {
		$itemBuilder->addField($this->getColumnName(), $this->getTableAlias());
	}
	
	public function equals($obj) {
		return $obj instanceof QueryColumn && $this->tableAlias === $obj->getTableAlias()
				&& $this->columnName === $obj->getColumnName();
	}
}
