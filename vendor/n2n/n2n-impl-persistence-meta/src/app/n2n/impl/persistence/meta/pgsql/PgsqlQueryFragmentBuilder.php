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
namespace n2n\impl\persistence\meta\pgsql;

use n2n\persistence\meta\data\QueryFragmentBuilder;
use n2n\persistence\Pdo;

/**
 * NotCompletedFinishYet
 * @author Thiruban
 *
 */
class PgsqlQueryFragmentBuilder implements QueryFragmentBuilder {
	private $dbh;
	private $sql;

	public function __construct(Pdo $dbh) {
		$this->dbh = $dbh;
	}

	public function addField($fieldName, $fieldAlias = null) {
		$this->sql .= ' ' . (!is_null($fieldAlias) ? $this->dbh->quoteField($fieldAlias) . '.' : '') . $this->dbh->quoteField($fieldName);
	}

	public function addFieldAlias($fieldAlias) {
		$this->sql .= ' AS ' . $this->dbh->quoteField($fieldAlias);
	}

	public function addConstant($value) {
		if (!isset($value)) {
			$this->sql .= ' NULL ';
			return;
		}
		$this->sql .= ' ' . $this->dbh->quote($value);
	}

	public function addPlaceMarker($name = null) {
		if (is_null($name)) {
			$this->sql .= ' ? ';
		} else {
			$this->sql .= ' :' . $this->dbh->quoteField($name);
		}
	}

	public function addOperator($operator) {
		$this->sql .= ' ' . $operator;
	}

	public function openFunction($name) {
		$this->sql .= ' ' . $name . ' (';
	}

	public function closeFunction() {
		$this->sql .= ' )';
	}

	public function openGroup() {
		$this->sql .= ' (';
	}

	public function closeGroup() {
		$this->sql .= ' )';
	}
	
	public function addSeparator() {
		$this->sql .= ', '; 
	}

	public function toSql() {
		return $this->sql;
	}

	public function addTable($tableName) {
		$this->sql .= ' ' . $this->dbh->quote($tableName);
	}

	public function addRawString($sqlString) {
		$this->sql .= ' ' . $this->dbh->quote($sqlString);
	}
}