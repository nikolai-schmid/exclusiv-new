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
namespace n2n\util\uri;

use n2n\util\ex\NotYetImplementedException;
class Authority {
	const USER_INFO_SUFFIX = '@';
	const PORT_PREFIX = ':';
	
	private $userInfo;
	private $host;
	private $port;
	
	public function __construct($userInfo = null, $host = null, $port = null) {
		$this->userInfo = $userInfo;
		$this->host = $host;
		$this->port = $port;
	}
	
	public function getUserInfo() {
		return $this->userInfo;
	}
	
	public function hasUserInfo() {
		return $this->userInfo !== null;
	}
	
	public function getHost() {
		return $this->host;
	}
	
	public function hasHost() {
		return $this->host !== null;
	}
	
	public function getPort() {
		return $this->port;
	}
	
	public function hasPort() {
		return $this->port !== null;
	}
	
	public function isEmpty() {
		return $this->userInfo === null && $this->host === null && $this->port === null;
	}
	
	public function chHost($host) {
		if ($this->host === $host) return $this;
		
		return new Authority($this->userInfo, $host, $this->port);
	}
	
	public function __toString(): string {
		$str = '';
		
		if ($this->userInfo !== null) {
			$str = $this->userInfo . self::USER_INFO_SUFFIX;
		}
		
		$str .= $this->host;
		
		if ($this->port !== null) {
			$str .= self::PORT_PREFIX . $this->port;
		}
		
		return $str;
	}
	
	public static function create($param) {
		if ($param instanceof Authority) {
			return $param;
		}
		
		throw new NotYetImplementedException();
	}
}
