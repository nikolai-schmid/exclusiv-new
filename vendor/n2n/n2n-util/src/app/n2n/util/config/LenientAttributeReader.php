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
namespace n2n\util\config;

use n2n\reflection\property\TypeConstraint;

class LenientAttributeReader {
	private $attributes;
	
	public function __construct(Attributes $attributes) {
		$this->attributes = $attributes;
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function isEmpty(): bool {
		return $this->attributes->isEmpty();
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function contains($name): bool {
		return $this->attributes->contains($name);
	}
	
	public function getNames() {
		return $this->attributes->getNames();
	}
	
	public function hasKey($name, $key) {
		return $this->attributes->hasKey($name, $key);
	}
	
	/**
	 *
	 * @param unknown_type $name
	 * @return mixed
	 */
	public function get($name, $fallbackValue = null, TypeConstraint $typeConstraint = null) {
		try {
			return $this->attributes->get($name, false, $fallbackValue, $typeConstraint);
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	public function getScalar($name, $fallbackValue = null, bool $nullAllowed = false) {
		try {
			return $this->attributes->getScalar($name, false, $fallbackValue, $nullAllowed);	
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	public function getString($name, $fallbackValue = null, $nullAllowed = false) {
		try {
			return $this->attributes->getString($name, false, $fallbackValue, $nullAllowed);
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	public function getBool($name, $fallbackValue = null, $nullAllowed = false, $lenient = true) {
		try {
			return $this->attributes->getBool($name, false, $fallbackValue, $nullAllowed);
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	public function getNumeric($name, $fallbackValue = null, bool $nullAllowed = false) {
		try {
			return $this->attributes->getBool($name, false, $fallbackValue, $nullAllowed);
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	public function getInt($name, $fallbackValue = null, $nullAllowed = false, $lenient = true) {
		try {
			return $this->attributes->getBool($name, false, $fallbackValue, $nullAllowed);
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	public function getFloat($name, $fallbackValue = null, $nullAllowed = false, $lenient = true): float {
		try {
			return $this->attributes->getFloat($name, false, $fallbackValue, $nullAllowed);
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	public function getEnum($name, array $allowedValues, $fallbackValue = null) {
		try {
			return $this->attributes->getEnum($name, $allowedValues, false, $fallbackValue);
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	public function getArray(string $name, $fallbackValue = array(),
			TypeConstraint $arrayFieldTypeConstraints = null, bool $nullAllowed = false) {
		try {
			return $this->attributes->getArray($name, false, $fallbackValue, $arrayFieldTypeConstraints, $nullAllowed);
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	public function getScalarArray($name, $fallbackValue = array(), $nullAllowed = false) {
		try {
			return $this->attributes->getScalarArray($name, false, $fallbackValue, $nullAllowed);
		} catch (AttributesException $e) {
			return $fallbackValue;
		}
	}
	
	/**
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->attributes->toArray();
	}
}
