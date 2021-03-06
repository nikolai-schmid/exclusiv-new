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

use n2n\util\config\Attributes;
use n2n\web\dispatch\mag\MagCollection;
use n2n\core\container\N2nContext;
use n2n\web\dispatch\mag\MagDispatchable;

interface EiConfigurator {

	/**
	 * @return Attributes 
	 */
	public function getAttributes(): Attributes;
	
	/**
	 * @param Attributes $attributes
	 */
	public function setAttributes(Attributes $attributes);
	
	/**
	 * @return string 
	 */
	public function getTypeName(): string;
	
	/**
	 * @return EiComponent 
	 */
	public function getEiComponent(): EiComponent;
	
	/**
	 * No Exception should be thrown if Attributes are invalid. Use of {@link \n2n\util\config\LenientAttributeReader}
	 * recommended. {@link EiConfigurator::setup()} may have already been called or not.
	 * @return MagDispatchable 
	 */
	public function createMagDispatchable(N2nContext $n2nContext): MagDispatchable;
	
	/**
	 * @param MagCollection $magCollection
	 * @param N2nContext $n2nContext
	 */
	public function saveMagDispatchable(MagDispatchable $magDispatchable, N2nContext $n2nContext);
	
	/**
	 * @param EiSetupProcess $setupProcess
	 * @throws InvalidEiComponentConfigurationException can be created with {@link EiSetupProcess::createExcpetion()}
	 * @throws \n2n\util\config\AttributesException will be converted to InvalidEiComponentConfigurationException
	 */
	public function setup(EiSetupProcess $setupProcess);
}
