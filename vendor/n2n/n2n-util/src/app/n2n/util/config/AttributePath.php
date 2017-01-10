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
use n2n\reflection\ArgUtils;

class AttributePath {
	private $names;
	
	public function __construct(array $names) {
		$this->names = $names;
	}
	
	public function toArray() {
		return $this->names;
	}
	
	public static function create($expression) {
		if ($expression === null) {
			return null;
		}
		
		if ($expression instanceof AttributePath) {
			return $expression;
		}
		
		if (is_array($expression)) {
			return new AttributePath($expression);
		}
		
		if (is_scalar($expression)) {
			return new AttributePath(array($expression));
		}
		
		throw new \InvalidArgumentException('Invalid AttributePath expression type: ' 
				. ReflectionUtils::getTypeInfo($expression));
	}
	
	public function __toString(): string {
		return implode('/', $this->names);
	}
}
