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
namespace rocket\spec\ei\component\field\impl\string;

use n2n\util\config\Attributes;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\util\crypt\hash\algorithm\BlowfishAlgorithm;
use rocket\spec\ei\manage\mapping\EiMapping;
use rocket\spec\ei\manage\gui\EntrySourceInfo;
use n2n\impl\web\dispatch\mag\model\SecretStringMag;
use n2n\util\crypt\hash\algorithm\Sha256Algorithm;
use n2n\util\crypt\hash\HashUtils;
use rocket\spec\ei\component\field\indepenent\EiFieldConfigurator;
use rocket\spec\ei\component\field\impl\string\conf\PasswordEiFieldConfigurator;
use rocket\spec\ei\manage\gui\FieldSourceInfo;
use n2n\web\dispatch\mag\Mag;

class PasswordEiField extends AlphanumericEiField {
	const ALGORITHM_SHA1 = 'sha1';
	const ALGORITHM_MD5 = 'md5';
	const ALGORITHM_BLOWFISH = 'blowfish';
	const ALGORITHM_SHA_256 = 'sha-256';
		
	public function isMandatory(FieldSourceInfo $fieldSourceInfo): bool {
		return false;
	}
	
	public function createEiFieldConfigurator(): EiFieldConfigurator {
		return new PasswordEiFieldConfigurator($this);
	}
	
	public function createOutputUiComponent(HtmlView $view, FieldSourceInfo $entrySourceInfo)  {
		return null;
	}
	
	public function createMag(string $propertyName, FieldSourceInfo $entrySourceInfo): Mag {
		return new SecretStringMag($propertyName, $this->getLabelCode(), null,
				$entrySourceInfo->getEiMapping()->getEiSelection()->isNew(), $this->getMaxlength(), 
				array('placeholder' => $this->getLabelCode()));
	}
	
	public function optionAttributeValueToPropertyValue(Attributes $attributes, 
			EiMapping $eiMapping, EntrySourceInfo $entrySourceInfo) {
		$optionValue = $attributes->get($this->getId());
		$eiSelection = $eiMapping->getEiSelection();
		if (mb_strlen($optionValue) === 0 && !$eiSelection->isNew()) {
			return;
		}
		$propertyValue = null;
		switch ($this->getAttributes()->get(self::OPTION_ALGORITHM_KEY)) {
			case (self::ALGORITHM_BLOWFISH):
				$propertyValue = HashUtils::buildHash($optionValue, new BlowfishAlgorithm());
				break;
			case (self::ALGORITHM_SHA_256):
				$propertyValue = HashUtils::buildHash($optionValue, new Sha256Algorithm());
				break;
			case (self::ALGORITHM_MD5):
				$propertyValue = md5($optionValue);
				break;
			case (self::ALGORITHM_SHA1):
				$propertyValue = sha1($optionValue);
				break;
		}
		$eiMapping->setValue($this->getId(), $propertyValue);
	}
	
	public static function getAlgorithms() {
		return array(self::ALGORITHM_BLOWFISH, self::ALGORITHM_SHA1, self::ALGORITHM_MD5, self::ALGORITHM_SHA_256);
	}
}