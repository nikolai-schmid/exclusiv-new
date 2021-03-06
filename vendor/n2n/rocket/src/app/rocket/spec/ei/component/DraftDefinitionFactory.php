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

use rocket\spec\ei\component\field\EiFieldCollection;
use rocket\spec\ei\component\modificator\EiModificatorCollection;
use n2n\reflection\ArgUtils;
use rocket\spec\ei\component\field\DraftableEiField;
use rocket\spec\ei\manage\draft\DraftDefinition;
use n2n\persistence\orm\model\EntityModel;
use rocket\spec\ei\manage\draft\DraftProperty;

class DraftDefinitionFactory {
	private $entityModel;
	private $eiFieldCollection;
	private $eiModificatorCollection;
	
	public function __construct(EntityModel $entityModel, EiFieldCollection $eiFieldCollection, EiModificatorCollection $eiModificatorCollection) {
		$this->entityModel = $entityModel;
		$this->eiFieldCollection = $eiFieldCollection;
		$this->eiModificatorCollection = $eiModificatorCollection;
	}
	
	public function create(string $tableName) {
		$draftDefinition = new DraftDefinition($tableName, $this->entityModel);
	
		foreach ($this->eiFieldCollection as $id => $eiField) {
			if (!($eiField instanceof DraftableEiField && $eiField->isDraftable())) continue;
			
			$draftProperty = $eiField->getDraftProperty();
			ArgUtils::valTypeReturn($draftProperty, DraftProperty::class, $eiField, 'getDraftProperty', true);
			
			if ($draftProperty !== null) {
				$draftDefinition->putDraftProperty($id, $draftProperty);
			}
		}
	
		foreach ($this->eiModificatorCollection as $eiModificator) {
			$eiModificator->setupDraftDefinition($draftDefinition);
		}
	
		return $draftDefinition;
	}
}
