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

use n2n\persistence\meta\structure\BinaryColumn;

class PgsqlBinaryColumn extends PgsqlColumn implements BinaryColumn, PgsqlManagedColumn {
	const COLUMN_TYPE = 'BYTEA';

	private $size;

	/**
	 * @param string $name
	 * @param int $size
	 */
	public function __construct($name, $size) {
		parent::__construct($name);
		$this->setSize($size);
	}

	/**
	 * @param int $size
	 */
	public function setSize($size) {
		$maxSize = pow(2, 31) - 1;
		if ($size <= $maxSize && !is_null($size)) {
			$this->size = $size;
		} else {
			$this->size = $maxSize;
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see n2n\persistence\meta.BinaryColumn::getSize()
	 */
	public function getSize() {
		return pow(2, 31) - 1;
	}

	/**
	 * (non-PHPdoc)
	 * @see n2n\impl\persistence\meta\pgsql.PgsqlManagedColumn::getTypeForCurrentState()
	 * @return String
	 */
	public function getTypeForCurrentState() {
		return self::COLUMN_TYPE;
	}

	/**
	 * (non-PHPdoc)
	 * @see n2n\persistence\meta.Column::copy()
	 */
	public function copy($newColumnName = null) {
		if (is_null($newColumnName)) $newColumnName = $this->getName();
		$binaryColumn = new PgsqlBinaryColumn($newColumnName, $this->getSize());
		$binaryColumn->setAttrs($this->getAttrs());
		$binaryColumn->setDefaultValue($this->getDefaultValue());
		$binaryColumn->setNullAllowed($this->isNullAllowed());
		$binaryColumn->setValueGenerated($this->isValueGenerated());

		return $binaryColumn;
	}
}
