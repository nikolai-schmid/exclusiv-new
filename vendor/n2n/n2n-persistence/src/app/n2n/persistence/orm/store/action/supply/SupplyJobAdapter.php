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
namespace n2n\persistence\orm\store\action\supply;

use n2n\util\ex\IllegalStateException;
use n2n\persistence\orm\store\action\EntityAction;
use n2n\persistence\orm\store\ValuesHash;

abstract class SupplyJobAdapter implements SupplyJob {
	protected $entityAction;
	protected $onResetClosures = array();
	protected $whenInitializedClosures = array();
	protected $oldValuesHash;
	protected $values;
	protected $init = false;
	
	public function __construct(EntityAction $entityAction, ValuesHash $oldValuesHash = null){
		$this->entityAction = $entityAction;
		$this->oldValuesHash = $oldValuesHash;
		
		$that = $this;
		$entityAction->executeOnDisable(function () use ($that) {
			$that->reset();
		});
	}
	
	public function getActionQueue() {
		return $this->entityAction->getActionQueue();	
	}
	
	public function isInsert() {
		return $this->oldValuesHash === null;
	}
	
	public function executeOnReset(\Closure $closure) {
		IllegalStateException::assertTrue(!$this->init);
		
		$this->onResetClosures[] = $closure;
	}
	
	public function executeWhenInitialized(\Closure $closure) {
		IllegalStateException::assertTrue(!$this->init);
		
		$this->whenInitializedClosures[] = $closure;
	}
	
	public function isDisabled() {
		return $this->entityAction->isDisabled();
	}
	
	public function init() {
		IllegalStateException::assertTrue(!$this->init);
		
		$this->init = true;
		while (null !== ($closure = array_shift($this->whenInitializedClosures))) {
			$closure();
		}
	}

// 	public function prepare() {
// 		$this->reset();
// 	}
	
	protected function reset() {
		IllegalStateException::assertTrue(!$this->init);
		
		$this->whenInitializedClosures = array();
		while (null !== ($closure = array_shift($this->onResetClosures))) {
			$closure();
		}
		$this->init = false;
	}
	
	public function getOldValuesHash() {
		return $this->oldValuesHash;
	}
	
	public function setValues(array $values) {
		$this->values = $values;
	}

	public function getValues() {
		return $this->values;
	}
	
	protected function getOldValueHash($propertyName) {
		IllegalStateException::assertTrue($this->oldValuesHash !== null 
				&& $this->oldValuesHash->containsPropertyName($propertyName));
		return $this->oldValuesHash->getValuesHash($propertyName);
	}

	protected function getValue($propertyName) {
		IllegalStateException::assertTrue(array_key_exists($propertyName, $this->values));
		return $this->values[$propertyName];
	}
}
