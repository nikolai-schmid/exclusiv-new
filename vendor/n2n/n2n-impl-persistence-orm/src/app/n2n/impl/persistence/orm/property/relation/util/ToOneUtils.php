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
namespace n2n\impl\persistence\orm\property\relation\util;

use n2n\impl\persistence\orm\property\relation\ToOneRelation;
use n2n\persistence\orm\store\action\supply\SupplyJob;
use n2n\persistence\orm\store\ValueHash;
use n2n\reflection\ArgUtils;

class ToOneUtils {
	private $toOneRelation;
	private $master;
	
	public function __construct(ToOneRelation $toOneRelation, $master) {
		$this->toOneRelation = $toOneRelation;	
		$this->master = (boolean) $master;
	}

	public function prepareSupplyJob(SupplyJob $supplyJob, $value, ValueHash $oldValueHash = null) {
		ArgUtils::assertTrue($oldValueHash === null || $oldValueHash instanceof ToOneValueHash);
		
		if ($this->master && $supplyJob->isRemove()) {
			$marker = new RemoveConstraintMarker($supplyJob->getActionQueue(), 
					$this->toOneRelation->getTargetEntityModel(),
					$this->toOneRelation->getActionMarker());
			$marker->releaseByIdRep($oldValueHash->getIdRep());
		}
		
		if ($this->toOneRelation->isOrphanRemoval()) {
			$orphanRemover = new OrphanRemover($supplyJob, $this->toOneRelation->getTargetEntityModel(), 
					$this->toOneRelation->getActionMarker());
	
			if ($value !== null && !$supplyJob->isRemove()) {
				$orphanRemover->releaseCandiate($value);
			}
	
			if ($oldValueHash !== null) {
				$orphanRemover->reportCandidateByIdRep($oldValueHash->getIdRep());
			}
		}
	}
}
