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
namespace rocket\spec\ei\component\command\impl\common\model;

use n2n\web\dispatch\Dispatchable;
use rocket\spec\ei\manage\EiState;
use n2n\persistence\orm\criteria\Criteria;
use n2n\persistence\orm\util\NestedSetUtils;
use rocket\spec\ei\manage\util\model\EiuFrame;
use n2n\util\ex\IllegalStateException;
use rocket\spec\ei\manage\EntryGui;
use rocket\spec\config\mask\model\EntryGuiTree;
use n2n\persistence\orm\criteria\item\CrIt;
use n2n\persistence\orm\util\NestedSetStrategy;
use rocket\spec\ei\manage\DraftEiSelection;

class DraftListModel implements Dispatchable {	
	private $utils;
	private $listSize;
	private $draftManager;
	private $draftDefinition;
	
	private $currentPageNo;
	private $numPages;
	private $numDrafts;
	
	private $entryGuis;
	private $entryGuiTree;
		
	public function __construct(EiState $eiState, int $listSize) {
		$this->utils = new EiuFrame($eiState);
		$this->listSize = $listSize;
		$this->draftManager = $eiState->getManageState()->getDraftManager();
		$this->draftDefinition = $eiState->getContextEiMask()->getEiEngine()->getDraftDefinition();
	}
	
	public function getEiState(): EiState {
		return $this->utils->getEiState();
	}
	
	public function initialize($pageNo): bool {
		if (!is_numeric($pageNo) || $pageNo < 1) return false;
		
		$this->numDrafts = $this->draftManager->countUnbounds($this->utils->getClass(), $this->draftDefinition);
		
		$this->currentPageNo = $pageNo;
		$limit = ($pageNo - 1) * $this->listSize;
		if ($limit > $this->numDrafts) {
			return false;
		}
		$this->numPages = ceil($this->numDrafts / $this->listSize);
		if (!$this->numPages) $this->numPages = 1;
		
		$drafts = $this->draftManager->findUnbounds($this->utils->getClass(), $limit, 
				$this->listSize, $this->draftDefinition);
		$this->simpleLookup($drafts);
				
		return true;
	}
	
// 	public function initByIdReps(array $idReps) {
// 		$eiState = $this->getEiState();
				
// 		$eiSpec = $eiState->getContextEiMask()->getEiEngine()->getEiSpec();
// 		$ids = array();
// 		foreach ($idReps as $idRep) {
// 			$ids[] = $eiSpec->idRepToId($idRep);
// 		}
	
// 		$criteria = $eiState->createCriteria(NestedSetUtils::NODE_ALIAS, false);
// 		$criteria->select(NestedSetUtils::NODE_ALIAS)
// 			->where()->match(CrIt::p(NestedSetUtils::NODE_ALIAS, $eiSpec->getEntityModel()->getIdDef()->getEntityProperty()), 'IN', $idReps);
		
// 		if (null !== ($nestedSetStrategy = $eiSpec->getNestedSetStrategy())) {
// 			$this->treeLookup($criteria, $nestedSetStrategy);
// 		} else {
// 			$this->simpleLookup($criteria);
// 		}
		
// 		return true;
// 	}
	
	private function simpleLookup(array $drafts) {
		$eiState = $this->utils->getEiState();
		$eiMask = $eiState->getContextEiMask();
		
		$this->entryGuis = array();
		foreach ($drafts as $draft) {
			$eiMapping = $this->utils->createEiMapping(new DraftEiSelection($draft));
			$this->entryGuis[$draft->getId()] = new EntryGui($eiMask->createListEntryGuiModel($eiState, 
					$eiMapping, false)); 
		}
	}
		
	public function getNumPages() {
		return $this->numPages;
	}
	
	public function getCurrentPageNo() {
		return $this->currentPageNo;
	}
	
	public function getNumEntries() {
		return $this->numDrafts;
	}
	
	public function getEntryGuis(): array {
		if ($this->entryGuis !== null) {
			return $this->entryGuis;
		}
		
		throw new IllegalStateException();
	}
	
// 	protected $selectedObjectIds = array();
// 	protected $executedPartialCommandKey = null;
	
// 	public function getSelectedObjectIds() {
// 		return $this->selectedObjectIds;
// 	}
	
// 	public function setSelectedObjectIds(array $selectedObjectIds) {
// 		$this->selectedObjectIds = $selectedObjectIds;
// 	}
	
// 	public function getExecutedPartialCommandKey() {
// 		return $this->executedPartialCommandKey;
// 	}
	
// 	public function setExecutedPartialCommandKey($executedPartialCommandKey) {
// 		$this->executedPartialCommandKey = $executedPartialCommandKey;
// 	}
	
// 	private function _validation() {}
	
// 	public function executePartialCommand() {
// 		$executedEiCommand = null;
// 		if (isset($this->partialEiCommands[$this->executedPartialCommandKey])) {
// 			$executedEiCommand = $this->partialEiCommands[$this->executedPartialCommandKey];
// 		}
		
// 		$selectedObjects = array();
// 		foreach ($this->selectedObjectIds as $entryId) {
// 			if (!isset($this->eiSelections[$entryId])) continue;
			
// 			$selectedObjects[$entryId] = $this->eiSelections[$entryId];
// 		}
		
// 		if (!sizeof($selectedObjects)) return;
		
// 		$executedEiCommand->processEntries($this->eiState, $selectedObjects);
// 	}
}
