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
namespace rocket\spec\ei\component\command\impl\common\controller;

use rocket\core\model\RocketState;
use n2n\l10n\DynamicTextCollection;
use n2n\web\http\controller\ControllerAdapter;
use rocket\spec\ei\component\command\impl\common\model\AddModel;
use rocket\spec\ei\component\command\impl\common\model\EntryCommandViewModel;
use n2n\web\http\controller\ParamGet;
use rocket\spec\ei\manage\util\model\EiuCtrl;

class AddController extends ControllerAdapter {
	private $dtc;
	private $rocketState;
	private $eiCtrlUtils;
	
	private $parentEiSelection;
	private $beforeEiSelection;
	private $afterEiSelection;
	
	public function prepare(DynamicTextCollection $dtc, EiuCtrl $eiCtrlUtils, RocketState $rocketState) {
		$this->dtc = $dtc;
		$this->eiCtrlUtils = $eiCtrlUtils;
	}
		
	public function index(ParamGet $refPath = null) {	
		$this->live($refPath);
	}
	
	public function doChild($parentIdRep, ParamGet $refPath = null) {
		$this->parentEiSelection = $this->eiCtrlUtils->lookupEiSelection($parentIdRep);	
		$this->live($refPath);	
	}
	
	public function doBefore($beforeIdRep, ParamGet $refPath = null) {
		$this->beforeEiSelection = $this->eiCtrlUtils->lookupEiSelection($beforeIdRep);	
		$this->live($refPath);
	}
	
	public function doAfter($afterIdRep, ParamGet $refPath = null) {
		$this->afterEiSelection = $this->eiCtrlUtils->lookupEiSelection($afterIdRep);	
		$this->live($refPath);
	}
	
	private function live(ParamGet $refPath = null) {
		$redirectUrl = $this->eiCtrlUtils->parseRefUrl($refPath);
			
		$eiStateUtils = $this->eiCtrlUtils->getEiuFrame();
		$entryForm = $eiStateUtils->createNewEntryForm(false);
		
		$eiState = $this->eiCtrlUtils->getEiState();
		$addModel = new AddModel($eiState, $entryForm, $eiStateUtils->getNestedSetStrategy());
		if ($this->parentEiSelection !== null) {
			$addModel->setParentEntityObj($this->parentEiSelection->getLiveObject());
		} else if ($this->beforeEiSelection !== null) {
			$addModel->setBeforeEntityObj($this->beforeEiSelection->getLiveObject());
		} else if ($this->afterEiSelection !== null) {
			$addModel->setAfterEntityObj($this->afterEiSelection->getLiveObject());
		}
		
		if (is_object($eiSelection = $this->dispatch($addModel, 'create'))) {
			$this->redirect($this->eiCtrlUtils->buildRefRedirectUrl($redirectUrl, $eiSelection));
			return;
		} else if ($this->dispatch($addModel, 'createAndRepeate')) {
			$this->refresh();
			return;
		}
		
		$this->eiCtrlUtils->applyCommonBreadcrumbs(null, $this->getBreadcrumbLabel());
		
		$viewModel = new EntryCommandViewModel($this->eiCtrlUtils->getEiuFrame(), null, $redirectUrl);
		$viewModel->setTitle($this->dtc->translate('ei_impl_add_title', array(
				'type' => $this->eiCtrlUtils->getEiState()->getContextEiMask()->getLabelLstr()
						->t($this->getN2nContext()->getN2nLocale()))));
		$this->forward('..\view\add.html',
				array('addModel' => $addModel, 'entryViewInfo' => $viewModel));
	}
	
	public function doDraft(ParamGet $refPath = null) {
		$redirectUrl = $this->eiCtrlUtils->parseRefUrl($refPath);
			
		$entryForm = $this->eiCtrlUtils->getEiuFrame()->createNewEntryForm(true);
		
		$eiState = $this->eiCtrlUtils->getEiState();
		$addModel = new AddModel($eiState, $entryForm);
		
		if (is_object($eiSelection = $this->dispatch($addModel, 'create'))) {
			$this->redirect($this->eiCtrlUtils->buildRefRedirectUrl($redirectUrl, $eiSelection));
			return;
		}
		
		$viewModel = new EntryCommandViewModel($this->eiCtrlUtils->getEiuFrame(), null, $redirectUrl);
		$viewModel->setTitle($this->dtc->translate('ei_impl_add_draft_title', 
				array('type' => $this->eiCtrlUtils->getEiuFrame()->getGenericLabel())));
		$this->forward('..\view\add.html', array('addModel' => $addModel, 'entryViewInfo' => $viewModel));
	}
	
	private function getBreadcrumbLabel() {
		$eiStateUtils = $this->eiCtrlUtils->getEiuFrame();
		
		if (null === $eiStateUtils->getNestedSetStrategy()) {
			return $this->dtc->translate('ei_impl_add_breadcrumb');
		} else if ($this->parentEiSelection !== null) {
			return $this->dtc->translate('ei_impl_add_child_branch_breadcrumb',
					array('parent_branch' => $eiStateUtils->createIdentityString($this->parentEiSelection)));
		} else if ($this->beforeEiSelection !== null) {
			return$this->dtc->translate('ei_impl_add_before_branch_breadcrumb',
					array('branch' => $eiStateUtils->createIdentityString($this->beforeEiSelection)));
		} else if ($this->afterEiSelection !== null) {
			return $this->dtc->translate('ei_impl_add_after_branch_breadcrumb',
					array('branch' => $eiStateUtils->createIdentityString($this->afterEiSelection)));
		} else {
			return $this->dtc->translate('ei_impl_add_root_branch_breadcrumb');
		}
	}
}
