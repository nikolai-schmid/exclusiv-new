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
namespace rocket\spec\ei\component\field\impl\numeric\conf;

use n2n\core\container\N2nContext;
use rocket\spec\ei\component\EiSetupProcess;
use n2n\util\ex\IllegalStateException;
use n2n\impl\web\dispatch\mag\model\NumericMag;
use rocket\spec\ei\component\field\impl\numeric\NumericEiFieldAdapter;
use n2n\web\dispatch\mag\MagDispatchable;
use rocket\spec\ei\component\field\indepenent\PropertyAssignation;
use rocket\spec\ei\component\field\impl\numeric\IntegerEiField;
use rocket\spec\ei\component\field\indepenent\CompatibilityLevel;
use n2n\persistence\meta\structure\Column;
use n2n\persistence\meta\structure\IntegerColumn;
use n2n\util\config\LenientAttributeReader;
use rocket\spec\ei\component\field\impl\adapter\AdaptableEiFieldConfigurator;

class NumericEiFieldConfigurator extends AdaptableEiFieldConfigurator {
	const OPTION_MIN_VALUE_KEY = 'minValue';
	const OPTION_MAX_VALUE_KEY = 'maxValue';
	
	public function __construct(NumericEiFieldAdapter $numericAdapter) {
		parent::__construct($numericAdapter);
		
		$this->autoRegister($numericAdapter);
	}
	
	public function initAutoEiFieldAttributes(Column $column = null) {
		parent::initAutoEiFieldAttributes($column);
		
		if ($this->isGeneratedId()) {
			$this->attributes->set(self::ATTR_DISPLAY_IN_EDIT_VIEW_KEY, false);
			$this->attributes->set(self::ATTR_DISPLAY_IN_ADD_VIEW_KEY, false);
			$this->attributes->set(self::ATTR_READ_ONLY_KEY, true);
		}
		
		if ($column instanceof IntegerColumn) {
			$this->attributes->set(self::OPTION_MIN_VALUE_KEY, $column->getMinValue());
			$this->attributes->set(self::OPTION_MAX_VALUE_KEY, $column->getMaxValue());
		}
	}
	
	public function testCompatibility(PropertyAssignation $propertyAssignation): int {
		$comptibilityLevel = parent::testCompatibility($propertyAssignation);
		
		if ($this->eiComponent instanceof IntegerEiField 
				&& $propertyAssignation->getEntityProperty()->getName() === 'id') {
			return CompatibilityLevel::COMMON;
		}
		
		return $comptibilityLevel;
	}
	
	public function setup(EiSetupProcess $eiSetupProcess) {
		parent::setup($eiSetupProcess);
	
		IllegalStateException::assertTrue($this->eiComponent instanceof NumericEiFieldAdapter);
		
		if ($this->attributes->contains(self::OPTION_MIN_VALUE_KEY)) {
			$this->eiComponent->setMinValue($this->attributes->get(self::OPTION_MIN_VALUE_KEY));
		}
		
		if ($this->attributes->contains(self::OPTION_MAX_VALUE_KEY)) {
			$this->eiComponent->setMaxValue($this->attributes->get(self::OPTION_MAX_VALUE_KEY));
		}
	}
	
	protected function isGeneratedId(): bool {
		$entityProperty = $this->getAssignedEntityProperty();
		if ($entityProperty === null) return false;
		
		$idDef = $entityProperty->getEntityModel()->getIdDef();
		return $idDef->isGenerated() && $idDef->getEntityProperty() === $entityProperty;
	}
	
	public function createMagDispatchable(N2nContext $n2nContext): MagDispatchable {
		$magDispatchable = parent::createMagDispatchable($n2nContext);
		$magCollection = $magDispatchable->getMagCollection();
		
		$lar = new LenientAttributeReader($this->attributes);
		
		IllegalStateException::assertTrue($this->eiComponent instanceof NumericEiFieldAdapter);
		$magCollection->addMag(new NumericMag(self::OPTION_MIN_VALUE_KEY, 'Min Value',
				$lar->getNumeric(self::OPTION_MIN_VALUE_KEY, $this->eiComponent->getMinValue())));
		$magCollection->addMag(new NumericMag(self::OPTION_MAX_VALUE_KEY, 'Max Value',
				$lar->getNumeric(self::OPTION_MAX_VALUE_KEY, $this->eiComponent->getMaxValue())));
	
		return $magDispatchable;
	}
	
	public function saveMagDispatchable(MagDispatchable $magDispatchable, N2nContext $n2nContext) {
		parent::saveMagDispatchable($magDispatchable, $n2nContext);
		
		$magCollection = $magDispatchable->getMagCollection();
		
		if (null !== ($minValue = $magCollection->getMagByPropertyName(self::OPTION_MIN_VALUE_KEY)
				->getValue())) {
			$this->attributes->set(self::OPTION_MIN_VALUE_KEY, $minValue);
		}
		
		if (null !== ($maxValue = $magCollection->getMagByPropertyName(self::OPTION_MAX_VALUE_KEY)
				->getValue())) {
			$this->attributes->set(self::OPTION_MAX_VALUE_KEY, $maxValue);
		}
	}
	
}
