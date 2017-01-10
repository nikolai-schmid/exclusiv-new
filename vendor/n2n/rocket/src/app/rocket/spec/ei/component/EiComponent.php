<?php
/*
 * Copyright (c) 2012-2016, Hofmänner New Media.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This file is part of the n2n module ROCKET.
 *
 * ROCKET is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Lesser General Public License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * ROCKET is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details: http://www.gnu.org/licenses/
 *
 * The following people participated in this project:
 *
 * Andreas von Burg...........:	Architect, Lead Developer, Concept
 * Bert Hofmänner.............: Idea, Frontend UI, Design, Marketing, Concept
 * Thomas Günther.............: Developer, Frontend UI, Rocket Capability for Hangar
 */
namespace rocket\spec\ei\component;

use rocket\spec\ei\EiSpec;
use rocket\spec\ei\EiThing;
use rocket\spec\ei\EiEngine;

interface EiComponent {
	
	/**
	 * @return string
	 */
	public function getId();
	
	/**
	 * @param string $id
	 */
	public function setId($id);
	
	/**
	 * @return string
	 */
	public function getIdBase();
	
	/**
	 * @return \rocket\spec\ei\EiEngine
	 * @throws \n2n\util\ex\IllegalStateException
	 */
	public function getEiEngine(): EiEngine;
	
	/**
	 * @param EiSpec $eiThing
	 */
	public function setEiEngine(EiEngine $eiEngine);
		
	/**
	 * @param mixed $obj
	 * @return boolean
	 */
	public function equals($obj);
	
	/**
	 * @return string 
	 */
	public function __toString(): string;
}
