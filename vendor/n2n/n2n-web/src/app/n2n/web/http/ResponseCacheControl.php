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
namespace n2n\web\http;

class ResponseCacheControl {
	private $cacheInterval;
	private $includedQueryParamNames;
	private $characteristics;
	
	public function __construct(\DateInterval $cacheInterval = null, array $includedQueryParamNames = null,
			array $characteristics = array()) {
		$this->cacheInterval = $cacheInterval;
		$this->includedQueryParamNames = $includedQueryParamNames;
		$this->characteristics = $characteristics;
	}
	
	public function getCacheInterval() {
		return $this->cacheInterval;
	}
	
	public function getIncludedQueryParamNames() {
		return $this->includedQueryParamNames;
	}
	
	public function getCharacteristics(): array {
		return $this->characteristics;
	}
}