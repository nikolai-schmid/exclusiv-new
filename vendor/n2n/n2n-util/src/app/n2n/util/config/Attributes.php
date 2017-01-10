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

use n2n\reflection\ReflectionUtils;
use n2n\reflection\property\TypeConstraint;
use n2n\reflection\property\ValueIncompatibleWithConstraintsException;
use n2n\util\col\ArrayUtils;

class Attributes {
	private $attrs;
	/**
	 * 
	 * @param array $attrs
	 */
	public function __construct(array $attrs = null) {
		$this->attrs = (array) $attrs;
	}
	/**
	 * 
	 * @return boolean
	 */
	public function isEmpty() {
		return empty($this->attrs);
	}
	/**
	 *
	 * @return boolean
	 */
	public function contains($name) {
		return array_key_exists($name, $this->attrs);
	}
	
	public function getNames() {
		return array_keys($this->attrs);
	}
	
	
	public function hasKey($name, $key) {
		return array_key_exists($name, $this->attrs) 
				&& is_array($this->attrs[$name])
				&& array_key_exists($key, $this->attrs[$name]);
	}
	/**
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	public function set($name, $value) {
		$this->attrs[$name] = $value;
	}
	/**
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function add($name, $key, $value) {
		if(!isset($this->attrs[$name]) || !is_array($this->attrs[$name])) {
			$this->attrs[$name] = array();
		}
	
		$this->attrs[$name][$key] = $value;
	}
	/**
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	public function push($name, $value) {
		if(!isset($this->attrs[$name]) || !is_array($this->attrs[$name])) {
			$this->attrs[$name] = array();
		}
	
		$this->attrs[$name][] = $value;
	}

	private function findR($attrs, array $nextNames, array $prevNames, $mandatory, &$found) {
		if (empty($nextNames)) {
			$found = true;
			return $attrs;
		}
		 
		$nextName = array_shift($nextNames);
		 
		if (!is_array($attrs)) {
			throw new InvalidAttributeException('Property \'' . new AttributePath($prevNames)
				. '\' must be an array. ' . ReflectionUtils::getTypeInfo($attrs) . ' given.');
		}
		 
		$prevNames[] = $nextName;
		 
		if (!array_key_exists($nextName, $attrs)) {
			if (!$mandatory) {
				$found = false;
				return null;
			}
			throw new MissingAttributeFieldException('Missing property: '  . new AttributePath($prevNames));
		}
		 
		return $this->findR($attrs[$nextName], $nextNames, $prevNames, $mandatory, $found);
		 
	}
	
	private function find(AttributePath $attributePath, $mandatory, &$found) {		
		return $this->findR($this->attrs, $attributePath->toArray(), array(), $mandatory, $found);
	}
	/**
	 *
	 * @param unknown_type $name
	 * @return mixed
	 */
	public function get($name, $mandatory = true, $defaultValue = null, TypeConstraint $typeConstraint = null) {
		$attributePath = AttributePath::create($name);
		
		$found = null;
		$value = $this->find($attributePath, $mandatory, $found);
		if (!$found) return $defaultValue;
	
		if ($typeConstraint === null) {
			return $value;
		}
		 
		try {
			$typeConstraint->validate($value);
		} catch (ValueIncompatibleWithConstraintsException $e) {
			throw new InvalidAttributeException('Property contains invalid value: ' . $attributePath, 0, $e);
		}
	
		return $value;
	}
	
	public function getScalar($name, $mandatory = true, $defaultValue = null, $nullAllowed = false) {
		return $this->get($name, $mandatory, $defaultValue, TypeConstraint::createSimple('scalar', $nullAllowed));
	}
	
	public function getString($name, $mandatory = true, $defaultValue = null, $nullAllowed = false, $lenient = true) {
		if (!$lenient) {
			return $this->get($name, $mandatory, $defaultValue, TypeConstraint::createSimple('string', $nullAllowed));
		}
		
		if (null !== ($value = $this->getScalar($name, $mandatory, $defaultValue, $nullAllowed))) {
			return (string) $value;
		}
		
		return null;
	}
	
	public function getBool($name, $mandatory = true, $defaultValue = null, $nullAllowed = false, $lenient = true) {
		if (!$lenient) {
			return $this->get($name, $mandatory, $defaultValue, TypeConstraint::createSimple('boolean', $nullAllowed));
		}
		 
		if (null !== ($value = $this->getScalar($name, $mandatory, $defaultValue, $nullAllowed))) {
			return (string) $value;
		}
		 
		return null;
	}	
	
	public function getNumeric($name, $mandatory = true, $defaultValue = null, $nullAllowed = false) {
		return $this->get($name, $mandatory, $defaultValue, TypeConstraint::createSimple('scalar', $nullAllowed));
	}
	
	public function getInt($name, $mandatory = true, $defaultValue = null, $nullAllowed = false, $lenient = true) {
		if (!$lenient) {
			return $this->get($name, $mandatory, $defaultValue, TypeConstraint::createSimple('int', $nullAllowed));
		}
		 
		if (null !== ($value = $this->getNumeric($name, $mandatory, $defaultValue, $nullAllowed))) {
			return (int) $value;
		}
		 
		return null;
	}
	
	public function getFloat($name, $mandatory = true, $defaultValue = null, $nullAllowed = false, $lenient = true): float {
		if (!$lenient) {
			return $this->get($name, $mandatory, $defaultValue, TypeConstraint::createSimple('float', $nullAllowed));
		}
		 
		if (null !== ($value = $this->getNumeric($name, $mandatory, $defaultValue, $nullAllowed))) {
			return (float) $value;
		}
		 
		return null;
	}
	
	public function getEnum($name, array $allowedValues, $mandatory = true, $defaultValue = null, $nullAllowed = false) {
		$attributePath = AttributePath::create($name);
		
		$found = null;
		$value = $this->find($attributePath, $mandatory, $found);
		if (!$found) return $defaultValue;
	
		if ($nullAllowed && $value === null) {
			return $value;
		}
		
		if (!ArrayUtils::inArrayLike($value, $allowedValues)) {
			throw new InvalidAttributeException('Property \'' . $attributePath 
				. '\' must contain one of following values: ' . implode(', ', $allowedValues) 
				. '. Given: ' . ReflectionUtils::buildScalar($value));
		}
	
		return $this->attrs[$name];
	}
	
	public function getArray($name, $mandatory = true, $defaultValue = array(), 
			TypeConstraint $arrayFieldTypeConstraints = null, bool $nullAllowed = false) {
		return $this->get($name, $mandatory, $defaultValue, 
				TypeConstraint::createArrayLike('array', $nullAllowed, $arrayFieldTypeConstraints));
	}
	
	
	public function getScalarArray($name, $mandatory = true, $defaultValue = array(), $nullAllowed = false) {
		return $this->getArray($name, $mandatory, $defaultValue, TypeConstraint::createSimple('scalar'));
	}
	
	/**
	 * 
	 * @param unknown_type $name
	 */
	public function remove($name) {
		unset($this->attrs[$name]);
	}
	
	public function removeKey($name, $key) {
		if ($this->hasKey($name, $key)) {
			unset($this->attrs[$name][$key]);
		}
	}
	/**
	 * 
	 * @param array $attrs
	 */
	public function setAll(array $attrs) {
		$this->attrs = $attrs;
	}
	/**
	 * 
	 * @return array
	 */
	public function toArray() {
		return $this->attrs;
	}
	/**
	 * 
	 * @param NN6Attributes $attributes
	 */
	public function append(Attributes $attributes) {
		$this->appendAll($attributes->toArray());
	}
	/**
	 * 
	 * @param array $attrs
	 */
	public function appendAll(array $attrs, bool $ignoreNull = false) {
		foreach ($attrs as $key => $value) {
			if ($ignoreNull && $value === null) continue;
			
			if (is_array($value) && isset($this->attrs[$key]) && is_array($this->attrs[$key])) {
				$value = array_merge($this->attrs[$key], $value);
// 				$value = $this->merge($this->attrs[$key], $value);
			}
			
			$this->attrs[$key] = $value;
		}
	}
	
	public function removeNulls(bool $recursive = false) {
		$this->removeNullsR($this->attrs, $recursive);
	}
	
	private function removeNullsR(array &$attrs, bool $recursive = false) {
		foreach ($attrs as $key => $value) {
			if (!isset($attrs[$key])) {
				unset($attrs[$key]);
			} else if ($recursive && is_array($attrs[$key])) {
				$this->removeNullsR($attrs[$key], true);
			}
		}
	}
	/**
	 * 
	 * @param array $attrs
	 * @param array $attrs2
	 */
	protected function merge(array $attrs, array $attrs2) {
		foreach ($attrs2 as $key => $value) {
			if (is_numeric($key)) {
				$attrs[] = $attrs2[$key];
				continue;
			}
				
			if (!array_key_exists($key, $attrs)) {
				$attrs[$key] = $value;
				continue;
			}
				
			if (is_array($attrs[$key])) {
				$attrs[$key] = $this->merge($attrs[$key], $attrs2[$key]);
				continue;
			}
				
			$attrs[$key] = $value;
		}
	
		return $attrs;
	}
	/**
	 * 
	 * @return string
	 */
	public function serialize() {
		return serialize($this->attrs);
	}
	/**
	 * 
	 * @param string $serialized
	 * @param \n2n\util\UnserializationFailedException
	 */
	public static function createFromSerialized($serialized) {
		$attrs = StringUtils::unserialize($serialized);
		if (!is_array($attrs)) $attrs = array();
		return new Attributes($attrs);
	}
}
