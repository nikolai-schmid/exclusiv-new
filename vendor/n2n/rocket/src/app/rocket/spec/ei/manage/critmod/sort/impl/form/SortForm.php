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
namespace rocket\spec\ei\manage\critmod\sort\impl\form;

use n2n\web\dispatch\Dispatchable;
use rocket\spec\ei\manage\critmod\sort\SortData;
use rocket\spec\ei\manage\critmod\sort\SortDefinition;
use n2n\persistence\orm\criteria\Criteria;
use rocket\spec\ei\manage\critmod\sort\SortItemData;

class SortForm implements Dispatchable {
	private $sortData;
	private $sortDefinition;
	
	protected $sortFieldIds;
	protected $directions;
	
	public function __construct(SortData $sortData, SortDefinition $sortDefinition) {
		$this->sortData = $sortData;
		$this->sortDefinition = $sortDefinition;
		
		$this->sortFieldIds = array();
		$this->directions = array();
		foreach ($sortData->getSortItemDatas() as $key => $sortItemData) {
			$this->sortFieldIds[$key] = $sortItemData->getSortFieldId(); 
			$this->directions[$key] = $sortItemData->getDirection();
		}
	}
	
	public function getSortDefinition(): SortDefinition {
		return $this->sortDefinition;
	}
	
	public function getSortFieldIds(): array {
		return $this->sortFieldIds;
	}
	
	public function setSortFieldIds(array $sortFieldIds) {
		$this->sortFieldIds = $sortFieldIds;
	}
	
	public function getDirections(): array {
		return $this->directions;
	}
	
	public function setDirections(array $directions) {
		$this->directions = $directions;
	}

	private function _validation() {
	}
	
	public function buildSortData(): SortData {
		$sortData = new SortData();
		
		$sortItemDatas = $sortData->getSortItemDatas();
		foreach ($this->sortFieldIds as $key => $sortFieldId) {
			if (!$this->sortDefinition->containsSortFieldId($sortFieldId)) continue;
			
			$direction = Criteria::ORDER_DIRECTION_ASC;
			if (isset($this->directions[$key]) && $this->directions[$key] === Criteria::ORDER_DIRECTION_DESC) {
				$direction = Criteria::ORDER_DIRECTION_DESC;
			}
			$sortItemDatas[] = new SortItemData($sortFieldId, $direction);
		}
		
		return $sortData;
	}
	
	
}
