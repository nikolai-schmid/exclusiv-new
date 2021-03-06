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
namespace rocket\spec\config\mask;

use rocket\spec\ei\manage\EiState;
use n2n\impl\web\ui\view\html\HtmlView;
use rocket\spec\ei\manage\control\EntryControlComponent;
use rocket\spec\ei\component\command\control\OverallControlComponent;
use rocket\spec\ei\EiSpec;
use rocket\util\Identifiable;
use n2n\l10n\N2nLocale;
use rocket\spec\ei\manage\gui\DisplayDefinition;
use rocket\spec\ei\manage\gui\EntrySourceInfo;
use rocket\spec\ei\mask\EiMask;
use n2n\web\dispatch\map\PropertyPath;
use rocket\spec\ei\EiDef;
use rocket\spec\ei\manage\preview\model\PreviewModel;
use rocket\spec\ei\manage\EiSelection;
use rocket\spec\ei\manage\mapping\EiMapping;
use rocket\spec\ei\component\MappingFactory;
use rocket\spec\ei\component\GuiFactory;
use rocket\spec\ei\component\DraftDefinitionFactory;
use rocket\spec\config\mask\model\GuiOrder;
use rocket\spec\config\mask\model\ControlOrder;
use rocket\spec\config\mask\model\GuiFieldOrder;
use n2n\reflection\ArgUtils;
use rocket\spec\ei\manage\gui\GuiDefinition;
use rocket\spec\ei\manage\gui\EiSelectionGui;
use rocket\spec\ei\manage\draft\DraftDefinition;
use rocket\spec\ei\manage\EntryGui;
use rocket\spec\config\mask\model\CommonEntryGuiModel;
use rocket\spec\ei\manage\model\EntryGuiModel;
use rocket\spec\config\mask\model\EntryGuiTree;
use n2n\web\ui\view\View;
use rocket\spec\ei\component\field\EiFieldCollection;
use rocket\spec\ei\component\modificator\EiModificatorCollection;
use rocket\spec\ei\component\command\EiCommandCollection;
use rocket\spec\config\mask\model\EntryListViewModel;
use rocket\spec\ei\component\CritmodFactory;
use rocket\spec\ei\component\command\control\PartialControlComponent;
use rocket\spec\ei\EiCommandPath;
use rocket\spec\ei\manage\control\PartialControl;
use rocket\spec\ei\manage\control\HrefControl;
use rocket\spec\ei\manage\critmod\CriteriaConstraint;
use n2n\web\ui\ViewFactory;
use rocket\spec\ei\EiEngine;
use rocket\spec\ei\EiThing;
use n2n\persistence\orm\model\EntityModel;
use n2n\l10n\Lstr;
use rocket\spec\ei\manage\preview\controller\PreviewController;
use n2n\util\config\InvalidConfigurationException;
use rocket\spec\ei\manage\preview\model\UnavailablePreviewException;
use rocket\spec\ei\manage\control\UnavailableControlException;
use rocket\spec\ei\manage\util\model\EiuGui;
use rocket\spec\ei\manage\util\model\Eiu;
use rocket\spec\ei\manage\util\model\EiuPerimeterException;

class CommonEiMask implements EiMask, Identifiable {
	private $id;
	private $eiSpec;
	private $moduleNamespace;
	private $subEiMaskIds;
	
	private $eiDef;
	private $eiEngine;
	private $guiOrder;
	
	private $mappingFactory;
	private $guiFactory;
	private $draftDefinitionFactory;
	private $critmodFactory;
	
	private $guiDefinition;
	private $draftDefinition;
	
	public function __construct(EiSpec $eiSpec, string $moduleNamespace, GuiOrder $guiOrder) {
		$this->eiSpec = $eiSpec;
		$this->moduleNamespace = $moduleNamespace;
		
		$this->eiDef = new EiDef();
		$this->eiEngine = new EiEngine($this->eiSpec, $this);
		$this->guiOrder = $guiOrder;
		
		$eiFieldCollection = $this->eiEngine->getEiFieldCollection();
		$eiFieldCollection->setInheritedCollection($this->eiSpec->getEiEngine()->getEiFieldCollection());
		
		$eiCommandCollection = $this->eiEngine->getEiCommandCollection();
		$eiCommandCollection->setInheritedCollection($this->eiSpec->getEiEngine()->getEiCommandCollection());
		
		$eiModificatorCollection = $this->eiEngine->getEiModificatorCollection();
		$eiModificatorCollection->setInheritedCollection($this->eiSpec->getEiEngine()->getEiModificatorCollection());
	}

	/* (non-PHPdoc)
	 * @see \rocket\util\Identifiable::getId()
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param string $id
	 */
	public function setId(string $id = null) {
		$this->id = $id;
	}
	
	public function getModuleNamespace(): string {
		return $this->moduleNamespace;
	}
	
	public function setModuleNamespace(string $moduleNamespace) {
		$this->moduleNamespace = $moduleNamespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\mask\EiMask::get()
	 */
	public function getEiSpec(): EiSpec {
		return $this->eiSpec;
	}
	
	public function getMaskedEiThing(): EiThing {
		return $this->eiSpec;
	}
	
	public function getEiDef() {
		return $this->eiDef;
	}
	
	public function getEiEngine(): EiEngine {
		return $this->eiEngine;
	}
	
	public function getEntityModel(): EntityModel {
		return $this->eiSpec->getEntityModel();
	}
	
	public function setGuiOrder(GuiOrder $guiOrder) {
		$this->guiOrder = $guiOrder;
	}
	
	public function getGuiOrder(): GuiOrder {
		return $this->guiOrder;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\EiThing::getLabelLstr()
	 */
	public function getLabelLstr(): Lstr {
		if (null !== ($label = $this->eiDef->getLabel())) {
			return new Lstr($label, $this->moduleNamespace);
		}
		
		return new Lstr((string) $this->eiSpec->getDefaultEiDef()->getLabel(), $this->moduleNamespace);
	}
	
	public function getPluralLabelLstr(): Lstr {
		if (null !== ($pluralLabel = $this->eiDef->getPluralLabel())) {
			return new Lstr($pluralLabel, $this->moduleNamespace);
		}
		
		return new Lstr((string) $this->eiSpec->getDefaultEiDef()->getPluralLabel(), $this->moduleNamespace);
	}
	
	
	/* (non-PHPdoc)
	 * @see \rocket\spec\ei\mask\EiMask::isDraftDisabled()
	 */
	public function isDraftingEnabled(): bool {
		if (null !== ($draftingAllowed = $this->eiDef->isDraftingAllowed())) {
			if (!$draftingAllowed) return false;
		} else if (null !== ($draftingAllowed = $this->eiSpec->getDefaultEiDef()->isDraftingAllowed())) {
			if (!$draftingAllowed) return false;
		}
		
		return !$this->eiEngine->getDraftDefinition()->isEmpty();
	}
	
	private function createEiSelectionGui(EiState $eiState, EiMapping $eiMapping, $viewMode, $makeEditable): EiSelectionGui {
		$guiIdPaths = $this->getGuiFieldOrderViewMode($viewMode)->getAllGuiIdPaths();
	
		return $this->eiEngine->createEiSelectionGui(new EntrySourceInfo($eiMapping, $eiState, $viewMode), 
				$makeEditable, $guiIdPaths);
	}
				
// 	/* (non-PHPdoc)
// 	 * @see \rocket\spec\ei\mask\EiMask::getCommands()
// 	 */
// 	public function getCommands() {
// 		return $this->eiEngine->getEiCommandCollection()->toArray();
// 	}
	
	
	
	/* (non-PHPdoc)
	 * @see \rocket\spec\ei\mask\EiMask::createIdentityString()
	 */
	public function createIdentityString(EiSelection $eiSelection, N2nLocale $n2nLocale): string {
		$identityStringPattern = $this->eiDef->getIdentityStringPattern();
		
		if ($identityStringPattern === null) {
			$identityStringPattern = $this->eiSpec->getDefaultEiDef()->getIdentityStringPattern();
		}
		
		if ($identityStringPattern === null) {
			return $this->getLabelLstr()->t($n2nLocale) . ' #' 
					. $this->eiSpec->idToIdRep($eiSelection->getLiveEntry()->getId());
		}
		
		return $this->eiEngine->getGuiDefinition()
				->createIdentityString($identityStringPattern, $eiSelection, $n2nLocale);
	}
	
	/**
	 * @param EiState $eiState
	 * @param HtmlView $htmlView
	 * @return \rocket\spec\ei\component\command\ControlButton[]
	 */
	public function createOverallHrefControls(EiState $eiState, HtmlView $htmlView): array {
		$controls = array();
		foreach ($this->eiEngine->getEiCommandCollection() as $eiCommandId => $eiCommand) {
			if (!($eiCommand instanceof OverallControlComponent)
					|| !$eiState->getManageState()->getEiPermissionManager()->isEiCommandAccessible($eiCommand)) continue;
				
			$hrefControls = $eiCommand->createOverallHrefControls($eiState, $htmlView);
			ArgUtils::valArrayReturn($hrefControls, $eiCommand, 'createOverallHrefControls', HrefControl::class);
			foreach ($hrefControls as $controlId => $control) {
				$controls[ControlOrder::buildControlId($eiCommandId, $controlId)] = $control;
			}
		}
		
		if (null !== ($overallControlOrder = $this->guiOrder->getOverallControlOrder())) {
			return $overallControlOrder->sort($controls);
		}
	
		return $controls;
	}
	
	/* (non-PHPdoc)
	 * @see \rocket\spec\ei\mask\EiMask::createEntryHrefControls()
	 */
	public function createEntryHrefControls(EiuGui $eiuGui, HtmlView $view): array {
		try {
			$eiuGui->getEiuEntry()->getEiuFrame();
		} catch (EiuPerimeterException $e) {
			throw new \InvalidArgumentException('Invalid EiuGui passed.', 0, $e);
		}
		
		$eiu = new Eiu($eiuGui);
		
		$controls = array();
		foreach ($this->eiEngine->getEiCommandCollection() as $eiCommandId => $eiCommand) {
			if (!($eiCommand instanceof EntryControlComponent)
					|| !$eiuGui->getEiuEntry()->isExecutableBy(EiCommandPath::from($eiCommand))) {
				continue;
			}
			
			$entryControls = $eiCommand->createEntryHrefControls($eiu, $view);
			ArgUtils::valArrayReturn($entryControls, $eiCommand, 'createEntryHrefControls', HrefControl::class);
			foreach ($entryControls as $controlId => $control) {
				$controls[ControlOrder::buildControlId($eiCommandId, $controlId)] = $control;
			}
		}
	
		if (null !== ($entryControlOrder = $this->guiOrder->getEntryControlOrder())) {
			return $entryControlOrder->sort($controls);
		}		
		
		return $controls;
	}
	
	public function createPartialControls(EiState $eiState, HtmlView $view): array {
		$controls = array();
		foreach ($this->getEiCommandCollection() as $eiCommandId => $eiCommand) {
			if (!($eiCommand instanceof PartialControlComponent)
					|| !$eiState->getManageState()->getEiPermissionManager()->isEiCommandAccessible($eiCommand)) continue;
				
			$executionPath = EiCommandPath::from($eiCommand);
			$partialControls = $eiCommand->createPartialControls($eiState, $view);
			ArgUtils::valArrayReturn($partialControls, $eiCommand, 'createPartialControls', PartialControl::class);
			foreach ($partialControls as $controlId => $control) {
				$controls[ControlOrder::buildControlId($eiCommandId, $controlId)] = $control;
				
				if (!$control->hasEiCommandPath()) {
					$control->setExecutionPath($executionPath->ext($controlId));
				}
			}
		}
		
		if (null !== ($overallControlOrder = $this->guiOrder->getOverallControlOrder())) {
			return $overallControlOrder->sortControls($controls);
		}
	
		return $controls;
	}
	
	
	private function getGuiFieldOrderViewMode($viewMode): GuiFieldOrder {
		$guiFieldOrder = null;
		
		switch ($viewMode) {
			case DisplayDefinition::VIEW_MODE_LIST_READ:
				if (null !== ($overviewGuiFieldOrder = $this->guiOrder->getOverviewGuiFieldOrder())) {
					return $overviewGuiFieldOrder;
				}
				return $this->createDefaultGuiFieldOrder($viewMode);
			case DisplayDefinition::VIEW_MODE_TREE_READ:
				if (null !== ($treeOverviewGuiFieldOrder = $this->guiOrder->getOverviewGuiFieldOrder())) {
					return $treeOverviewGuiFieldOrder;
				}
				return $this->createDefaultGuiFieldOrder($viewMode);
			case DisplayDefinition::VIEW_MODE_BULKY_READ:
				if (null !== ($detailGuiFieldOrder = $this->guiOrder->getDetailGuiFieldOrder())) {
					return $detailGuiFieldOrder;
				}
				break;
			case DisplayDefinition::VIEW_MODE_BULKY_EDIT:
				if (null !== $editGuiFieldOrder = $this->guiOrder->getEditGuiFieldOrder()) {
					return $editGuiFieldOrder;
				}
				break;
			case DisplayDefinition::VIEW_MODE_BULKY_ADD:
				if (null !== ($addGuiFieldOrder = $this->guiOrder->getAddGuiFieldOrder())) {
					return $addGuiFieldOrder;
				}
				break;
		}
		
		if (null !== ($bulkyGuiFieldOrder = $this->guiOrder->getBulkyGuiFieldOrder())) {
			return $bulkyGuiFieldOrder;
		}
		
		return $this->createDefaultGuiFieldOrder($viewMode);
	}
	
	private function createDefaultGuiFieldOrder($viewMode) {
		$guiFieldOrder = new GuiFieldOrder();
		foreach ($this->eiEngine->getGuiDefinition()->filterGuiIdPaths($viewMode) as $guiIdPath) {
			$guiFieldOrder->addGuiIdPath($guiIdPath);
		}
		return $guiFieldOrder;
	}
	
// 	public function createEiSelectionGui(EntrySourceInfo $entrySourceInfo) {
// 		$factory = new EiSelectionGuiFactory($this, $entrySourceInfo);
// 		return $this->createEiSelectionGui($entrySourceInfo, $this->getGuiFieldOrderViewMode(
// 				$entrySourceInfo->getViewMode())->getAllGuiIdPaths());
// 	}

	public function createListEntryGuiModel(EiState $eiState, EiMapping $eiMapping, 
			bool $makeEditable): EntryGuiModel {
		$eiSelectionGui = $this->createEiSelectionGui($eiState, $eiMapping, DisplayDefinition::VIEW_MODE_LIST_READ, 
				$makeEditable);
		
		return new CommonEntryGuiModel($this, $eiSelectionGui, $eiMapping);
	}
	
	public function createListView(EiState $eiState, array $entryGuis): View {
		ArgUtils::valArray($entryGuis, EntryGui::class);
		$guiFieldOrder = $this->getGuiFieldOrderViewMode(DisplayDefinition::VIEW_MODE_LIST_READ);
	
		return $eiState->getN2nContext()->lookup(ViewFactory::class)->create(
				'rocket\spec\config\mask\view\entryList.html', array('entryListViewModel' => new EntryListViewModel(
						$eiState, $entryGuis, $this->eiEngine->getGuiDefinition(), $guiFieldOrder)));
	}

	public function createTreeEntryGuiModel(EiState $eiState, EiMapping $eiMapping, 
			bool $makeEditable): EntryGuiModel {
		$eiSelectionGui = $this->createEiSelectionGui($eiState, $eiMapping, DisplayDefinition::VIEW_MODE_TREE_READ,
				$makeEditable);
		
		return new CommonEntryGuiModel($this, $eiSelectionGui, $eiMapping);
	}
	
	public function createTreeView(EiState $eiState, EntryGuiTree $entryGuiTree): View {
		$guiFieldOrder = $this->getGuiFieldOrderViewMode(DisplayDefinition::VIEW_MODE_TREE_READ);
	
		return $eiState->getN2nContext()->lookup(ViewFactory::class)->create(
				'rocket\spec\config\mask\view\entryList.html', array(
						'entryListViewModel' => new EntryListViewModel($eiState, $entryGuiTree->getEntryGuis(), 
								$this->eiEngine->getGuiDefinition(), $guiFieldOrder),
						'entryGuiTree' => $entryGuiTree));
	}
	
	public function createBulkyEntryGuiModel(EiState $eiState, EiMapping $eiMapping, 
			bool $makeEditable): EntryGuiModel {
		$viewMode = null;
		if (!$makeEditable) {
			$viewMode = DisplayDefinition::VIEW_MODE_BULKY_READ;
		} else if (!$eiMapping->getEiSelection()->getLiveEntry()->isPersistent()) {
			$viewMode = DisplayDefinition::VIEW_MODE_BULKY_ADD;
		} else {
			$viewMode = DisplayDefinition::VIEW_MODE_BULKY_EDIT;
		}
		
		$eiSelectionGui = $this->createEiSelectionGui($eiState, $eiMapping, $viewMode, $makeEditable);
		return new CommonEntryGuiModel($this, $eiSelectionGui, $eiMapping);
	}
	
	public function createBulkyView(EiState $eiState, EntryGui $entryGui): View {
		$viewMode = $entryGui->getEntryGuiModel()->getEiSelectionGui()->getViewMode();
		
		switch ($viewMode) {
			case DisplayDefinition::VIEW_MODE_BULKY_READ:
				$viewName = 'rocket\spec\config\mask\view\entryDetail.html';
				break;
			case DisplayDefinition::VIEW_MODE_BULKY_ADD:
			case DisplayDefinition::VIEW_MODE_BULKY_EDIT:
				$viewName = 'rocket\spec\config\mask\view\entryEdit.html';
				break;
			default:
				throw new \InvalidArgumentException('No bulky viewMode.');
		}
		
		$guiFieldOrder = $this->getGuiFieldOrderViewMode($viewMode);
		return $eiState->getN2nContext()->lookup(ViewFactory::class)->create($viewName, 
				array('guiFieldOrder' => $guiFieldOrder, 'eiState' => $eiState, 'entryGui' => $entryGui));
	}
	
	public function createEditView(EiState $eiState, EntryGuiModel $entryModel, PropertyPath $propertyPath = null): View {
		$viewMode = $this->determineEditViewMode($entryModel->getEiMapping());
	
		$guiFieldOrder = $this->getGuiFieldOrderViewMode($viewMode);
		
		return $eiState->getN2nContext()->lookup(ViewFactory::class)->create(
				'rocket\spec\config\mask\view\entryEdit.html',
				array('guiFieldOrder' => $guiFieldOrder, 'eiState' => $eiState, 'entryModel' => $entryModel, 
						'propertyPath' => $propertyPath));
	}
	
// 	public function createAddView(EiState $eiState, EntryModel $entryModel, PropertyPath $propertyPath = null) {
// 		$guiFieldOrder = $this->getGuiFieldOrderViewMode(DisplayDefinition::VIEW_MODE_BULKY_ADD);
	
// 		return $eiState->getN2nContext()->lookup(ViewFactory::class)->create(
// 				'rocket\spec\config\mask\view\entryEdit.html',
// 				array('guiFieldOrder' => $guiFieldOrder, 'eiState' => $eiState, 'entryModel' => $entryModel, 
// 						'propertyPath' => $propertyPath));
// 	}

// 	private function filterGuiFieldOrder(array $guiFieldOrder, GuiDefinition $guiDefinition) {
// 		foreach ($guiFieldOrder as $key => $fieldId) {
// 			if ($fieldId instanceof GroupedGuiFieldOrder) {
// 				$group = $fieldId->copy($this->filterGuiFieldOrder(
// 						$fieldId->getGuiFieldOrder(), $guiDefinition));
// 				if ($group->size()) {
// 					$guiFieldOrder[$key] = $group;
// 					continue;
// 				}
// 			}
			
// 			if (!$guiDefinition->containsGuiFieldId($fieldId)) {
// 				unset($guiFieldOrder[$key]);
// 			}
// 		}
// 		return $guiFieldOrder;
// 	}
	
// 	public function getFilterGroupData() {
// 		if (null !== ($filterData = $this->eiDef->getFilterGroupData())) {
// 			return $filterData;
// 		}
		
// 		return $this->eiSpec->getDefaultEiDef()->getFilterGroupData();
// 	}
	
// 	public function setFilterGroupData(FilterData $filterData = null) {
// 		$this->filterData = $filterData;
// 	}
	
// 	public function getDefaultSortData() {
// 		if (null !== ($defaultSortDirections = $this->eiDef->getDefaultSortData())) {
// 			return $defaultSortDirections;
// 		}
		
// 		return $this->eiSpec->getDefaultEiDef()->getDefaultSortData();
// 	}
	
// 	public function isFiltered()  {
// 		return null !== $this->eiDef->getFilterGroupData();
// 	}
	
	public function getSubEiMaskIds() {
		return $this->subEiMaskIds;
	}
	
	public function setSubEiMaskIds(array $subEiMaskIds) {
		$this->subEiMaskIds = $subEiMaskIds;
	}
	
	public function determineEiMask(EiSpec $eiSpec): EiMask {
		$eiSpecId = $eiSpec->getId();
		if ($this->eiSpec->getId() == $eiSpecId) {
			return $this;
		}
		
		if ($this->eiSpec->containsSubEiSpecId($eiSpecId)) {
			return $this->getSubEiMaskByEiSpecId($eiSpecId);
		}
				
		foreach ($this->eiSpec->getSubEiSpecs() as $subEiSpec) {
			if (!$subEiSpec->containsSubEiSpecId($eiSpecId, true)) continue;
			return $this->getSubEiMaskByEiSpecId($subEiSpec->getId())
					->determineEiMask($eiSpec);
		}
		
		throw new \InvalidArgumentException();
	}
	
	public function getSubEiMaskByEiSpecId($eiSpecId): EiMask {
		$subMaskIds = $this->getSubEiMaskIds();
		
		foreach ($this->eiSpec->getSubEiSpecs() as $subEiSpec) {
			if ($subEiSpec->getId() != $eiSpecId) continue;
			
			if (isset($subMaskIds[$eiSpecId])) {
				return $subEiSpec->getEiMaskCollection()->getById($subMaskIds[$eiSpecId]);
			} else {
				return $subEiSpec->getEiMaskCollection()->getOrCreateDefault();
			}
		}
		
		throw new \InvalidArgumentException('EiSpec ' . $eiSpecId . ' is no SubEiSpec of ' 
				. $this->eiSpec->getId());
	}
	
	public function isPreviewSupported(): bool {
		return null !== $this->eiDef->getPreviewControllerLookupId() 
				|| null !== $this->eiSpec->getDefaultEiDef()->getPreviewControllerLookupId();
	}
	
	public function lookupPreviewController(EiState $eiState, PreviewModel $previewModel = null): PreviewController {
		$lookupId = $this->eiDef->getPreviewControllerLookupId();
		if (null === $lookupId) {
			$lookupId = $this->eiSpec->getDefaultEiDef()->getPreviewControllerLookupId();	
		}
		
		if ($lookupId === null) {
			throw new UnavailablePreviewException('No PreviewController available for EiMask: ' . $this);
		}
		
		$previewController = $eiState->getN2nContext()->lookup($lookupId);
		if (!($previewController instanceof PreviewController)) {
			throw new InvalidConfigurationException('PreviewController must implement ' . PreviewController::class 
					. ': ' . get_class($previewController));
		}
		
		if ($previewModel === null) {
			return $previewController;
		}
		
		if (!array_key_exists($previewModel->getPreviewType(), $previewController->getPreviewTypeOptions($eiState, $previewModel->getEiSelection()))) {
			throw new UnavailableControlException('Unknown preview type \'' . $previewModel->getPreviewType() 
					. '\' for PreviewController: ' . get_class($previewController));
		}
		
		$previewController->setPreviewModel($previewModel);
		return $previewController;
	}
	
	public function __toString(): string {
		if ($this->id !== null) {
			return 'CommonEiMask (id: ' . $this->id . ') of ' . $this->eiSpec;
		}
		
		return 'Default CommonEiMask of ' . $this->eiSpec;
	}
	
	/**
	 * @todo move to EiEngine!!
	 * @param EiState $eiState
	 */
	public function setupEiState(EiState $eiState) {
		if (null !== ($filterGroupData = $this->eiDef->getFilterGroupData())
				|| null !== ($filterGroupData = $this->eiSpec->getDefaultEiDef()->getFilterGroupData())) {
			$criteriaConstraint = $this->createManagedFilterDefinition($eiState)
					->buildCriteriaConstraint($filterGroupData, false);
			if ($criteriaConstraint !== null) {
				$eiState->addCriteriaConstraint($criteriaConstraint);
			}
		}

		if (null !== ($defaultSortData = $this->eiDef->getDefaultSortData())
				|| null !== ($defaultSortData = $this->eiSpec->getDefaultEiDef()->getDefaultSortData())) {
			$criteriaConstraint = $this->eiEngine->createManagedSortDefinition($eiState)
					->builCriteriaConstraint($defaultSortData, false);
			if ($criteriaConstraint !== null) {
				$eiState->getCriteriaConstraintCollection()->add(CriteriaConstraint::TYPE_HARD_SORT, $criteriaConstraint);
			}
		}

		foreach ($this->eiEngine->getEiModificatorCollection()->toArray() as $modificator) {
			$modificator->setupEiState($eiState);
		}
	}

}
