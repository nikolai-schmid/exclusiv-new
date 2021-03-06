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
namespace rocket\spec\ei\component\field\impl\l10n\conf;

use rocket\spec\ei\component\field\impl\adapter\AdaptableEiFieldConfigurator;
use rocket\spec\ei\component\field\impl\l10n\N2nLocaleEiField;
use n2n\web\dispatch\option\impl\model\BoolMag;
use n2n\web\dispatch\option\impl\model\StringArrayMag;
use rocket\spec\ei\component\EiSetupProcess;
use n2n\util\ex\IllegalStateException;
use rocket\spec\ei\component\modificator\impl\l10n\N2nLocaleEiModificator;
use rocket\spec\ei\manage\gui\DisplayDefinition;

class N2nLocaleEiFieldConfigurator extends AdaptableEiFieldConfigurator {
	
	const OPTION_TAKE_LOCALES_FROM_CONFIG_KEY = 'takeN2nLocalesFromConfig';
	const OPTION_CUSTOM_LOCALE_ALIAS_KEY = 'customN2nLocaleAlias';
	
	public function __construct(N2nLocaleEiField $n2nLocaleEiField) {
		parent::__construct($n2nLocaleEiField);
		
		$this->autoRegister($n2nLocaleEiField);
	}
	
	public function setup(EiSetupProcess $setupProcess) {
		parent::setup($setupProcess);
		
		$n2nLocaleEiField = $this->eiComponent;
		IllegalStateException::assertTrue($n2nLocaleEiField instanceof N2nLocaleEiField);
		
		$takeN2nLocalesFromConfig = $this->attributes->get(self::OPTION_TAKE_LOCALES_FROM_CONFIG_KEY, false, true);
		
		if ($takeN2nLocalesFromConfig) {
			$n2nLocaleEiField->setN2nLocales($setupProcess->getN2nContext()->getContextN2nLocales());
		} else {
			$customN2nLocales = array();
			foreach ($this->attributes->get(self::OPTION_CUSTOM_LOCALE_ALIAS_KEY,
					true, array()) as $n2nLocaleAlias) {
				$customN2nLocales[$n2nLocaleAlias] = $setupProcess->getN2nContext()
						->getContextN2nLocaleByAlias($n2nLocaleAlias);
			}
			$n2nLocaleEiField->setN2nLocales($customN2nLocales);
		}
		
		if ($n2nLocaleEiField->isMultiLingual()) return;
		
		$setupProcess->getEiDef()->getEiModificatorCollection()
				->add(new N2nLocaleEiModificator($this));
		$n2nLocaleEiField->getDisplayDefinition()
				->setDefaultDisplayedViewModes(DisplayDefinition::NO_VIEW_MODES);
	}
	

	public function createMagCollection() {
		$magCollection = parent::createMagCollection();
		$magCollection->addMag(self::OPTION_TAKE_LOCALES_FROM_CONFIG_KEY,
				new BoolMag('Take N2nLocales from Configuration (app.ini)', true, false, array(),
						array('class' => 'rocket-impl-take-locale-from-config')));
		$magCollection->addMag(self::OPTION_CUSTOM_LOCALE_ALIAS_KEY,
				new StringArrayMag('Custom N2nLocales'));
		return $magCollection;
	}
}
