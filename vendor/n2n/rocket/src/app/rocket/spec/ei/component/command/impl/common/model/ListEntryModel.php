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

use rocket\spec\ei\manage\model\EntryModel;
use rocket\spec\ei\manage\gui\GuiDefinition;
use rocket\spec\ei\manage\mapping\EiMapping;
use rocket\spec\ei\manage\gui\EiSelectionGui;
use rocket\spec\ei\mask\EiMask;

class ListEntryModel implements EntryModel {
	private $eiMask;
	private $eiSelectionGui;
	
	private $eiMapping;
	
	/**
	 * @param GuiDefinition $guiDefinition
	 * @param EiMapping $eiMapping
	 */
	public function __construct(EiMask $eiMask, EiSelectionGui $eiSelectionGui,	EiMapping $eiMapping) {
		$this->eiMask = $eiMask;
		$this->eiSelectionGui = $eiSelectionGui;
		$this->eiMapping = $eiMapping;
	}
	
	/* (non-PHPdoc)
	 * @see \rocket\spec\ei\manage\model\ManageModel::getEiMask()
	 */
	public function getEiMask() {
		return $this->eiMask;
	}
	
	/* (non-PHPdoc)
	 * @see \rocket\spec\ei\manage\model\EntryModel::getEiMapping()
	 */
	public function getEiMapping() {
		return $this->eiMapping;
	}
	
	/* (non-PHPdoc)
	 * @see \rocket\spec\ei\manage\model\ManageModel::getGuiDefinition()
	 */
	public function getGuiDefinition() {
		return $this->guiDefinition;	
	}
	
	/* (non-PHPdoc)
	 * @see \rocket\spec\ei\manage\model\EntryModel::getEiSelectionGui()
	 */
	public function getEiSelectionGui() {
		return $this->eiSelectionGui;
	}
}
