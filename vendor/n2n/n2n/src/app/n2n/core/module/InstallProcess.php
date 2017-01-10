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
namespace n2n\core\module;

use n2n\persistence\Pdo;
use n2n\core\module\Module;
use n2n\core\VarStore;
use n2n\util\ex\NotYetImplementedException;

class InstallProcess {
	private $varStore;
	private $pdo;
	private $rollbackReason;
	private $currentModule;
	private $newModule;
	/**
	 * @param Pdo $pdo
	 */
	public function __construct(VarStore $varStore, Pdo $pdo = null, Module $currentModule = null, Module $newModule = null) {
		throw new NotYetImplementedException('Thomas, bespreche das bitte zuerst mit mir. Ich denke, wir müssen da etwas ändern.');
		$this->varStore = $varStore;
		$this->pdo = $pdo;
		$this->currentModule = $currentModule;
		$this->newModule = $newModule;
	}
	/**
	 * @return VarStore
	 */
	public function getVarStore() {
		return $this->varStore;
	}
	/**
	 * @return boolean
	 */
	public function hasPdo() {
		return $this->pdo !== null;
	}
	/**
	 * @return Pdo 
	 * @throws ModuleInstallationException
	 */
	public function getPdo() {
		if ($this->pdo !== null) {
			return $this->pdo;
		}

		throw new ModuleInstallationException('No database available.');
	}
	/**
	 * @param string $reason
	 */
	public function rollBack($reason) {
		$this->rollbackReason = (string) $reason;
	}
	/**
	 * @return boolean
	 */
	public function isRolledBack() {
		return $this->rollbackReason !== null;
	}
	/**
	 * @return string
	 */
	public function getRollbackReason() {
		return $this->rollbackReason;
	}
	
	public function getCurrentModule() {
		return $this->currentModule;
	}
	
	public function getNewModule() {
		return $this->newModule;
	}
}
