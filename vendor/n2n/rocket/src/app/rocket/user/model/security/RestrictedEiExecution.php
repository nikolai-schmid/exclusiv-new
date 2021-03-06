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
namespace rocket\user\model\security;

use rocket\spec\ei\security\EiExecution;
use rocket\spec\ei\component\command\EiCommand;
use rocket\spec\ei\manage\critmod\filter\EiMappingFilterDefinition;
use rocket\spec\ei\EiCommandPath;
use n2n\util\ex\IllegalStateException;
use rocket\spec\ei\manage\critmod\filter\EiMappingConstraintGroup;
use rocket\spec\ei\EiFieldPath;
use rocket\spec\security\PrivilegeDefinition;
use rocket\spec\ei\security\EiFieldAccess;
use rocket\spec\ei\security\InaccessibleControlException;
use rocket\spec\ei\manage\critmod\filter\ComparatorConstraintGroup;
use rocket\spec\ei\manage\mapping\EiMapping;
use rocket\user\bo\EiPrivilegeGrant;
use rocket\spec\ei\manage\mapping\WhitelistEiCommandAccessRestrictor;

class RestrictedEiExecution implements EiExecution {
	private $eiCommand;
	private $eiPrivilegeGrants;
	private $privilegeDefinition;
	private $eiMappingFilterDefinition;
	
	private $eiCommandPath;
	private $eiMappingConstraintGroup;
	private $comparatorConstraintGroup;

	public function __construct(EiCommand $eiCommand = null, EiCommandPath $eiCommandPath, array $eiPrivilegeGrants, 
			PrivilegeDefinition $privilegeDefinition, EiMappingFilterDefinition $eiMappingFilterDefinition) {
		$this->eiCommand = $eiCommand;
		$this->eiPrivilegeGrants = $eiPrivilegeGrants;
		$this->privilegeDefinition = $privilegeDefinition;
		$this->eiMappingFilterDefinition = $eiMappingFilterDefinition;
		$this->init($eiCommandPath);
	}

	public function isGranted(): bool {
		return true;
	}

	public function hasEiCommand(): bool {
		return $this->eiCommand !== null;
	}

	public function getEiCommand(): EiCommand {
		if ($this->eiCommand === null) {
			throw new IllegalStateException('No EiCommand executed.');
		}

		return $this->eiCommand;
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\security\EiExecution::getEiCommandPath()
	 */
	public function getEiCommandPath(): EiCommandPath {
		return $this->eiCommandPath;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\security\EiExecution::getEiMappingConstraint()
	 */
	public function getEiMappingConstraint() {
		return $this->eiMappingConstraintGroup;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\security\EiExecution::getCriteriaConstraint()
	 */
	public function getCriteriaConstraint() {
		return $this->comparatorConstraintGroup;
	}

	
	public function createEiFieldAccess(EiFieldPath $eiFieldPath): EiFieldAccess {
		$attributes = array();
		foreach ($this->eiPrivilegeGrants as $eiPrivilegeGrant) {
			$eiFieldAttributes = PrivilegeDefinition::extractAttributesOfEiFieldPrivilege($eiFieldPath, 
					$eiPrivilegeGrant->readEiFieldPrivilegeAttributes());
			if ($eiFieldAttributes !== null) {
				$attributes[] = $eiFieldAttributes;
			}
		}
		return new RestrictedEiFieldAccess($attributes);
	}

	private function init(EiCommandPath $eiCommandPath) {
		if (!$this->privilegeDefinition->checkEiCommandPathForPrivileges($eiCommandPath)) {
			if (empty($this->eiPrivilegeGrants)) {
				throw new InaccessibleControlException('EiCommandPath not accessible for current user: ' . $eiCommandPath);
			}	
			
			$this->eiCommandPath = $eiCommandPath;
			$this->initCriteriaConstraint();
			$this->initEiMappingConstraint();
			return;
		}
		
		$newEiPrivilegeGrants = array();
		foreach ($this->eiPrivilegeGrants as $eiPrivilegeGrant) {
			if ($eiPrivilegeGrant->acceptsEiCommandPath($eiCommandPath)) {
				$newEiPrivilegeGrants[] = $eiPrivilegeGrant;
			}
		}
		
		if (empty($newEiPrivilegeGrants)) {
			throw new InaccessibleControlException('Privileged EiCommandPath not accessible for current user: ' . $eiCommandPath);
		}
		
		$this->eiPrivilegeGrants = $newEiPrivilegeGrants;
		$this->eiCommandPath = $eiCommandPath;
		$this->initCriteriaConstraint();
		$this->initEiMappingConstraint();
	}
	
	private function initCriteriaConstraint() {
		$this->comparatorConstraintGroup = new ComparatorConstraintGroup(false);
			
		foreach ($this->eiPrivilegeGrants as $eiPrivilegeGrant) {
			if (!$eiPrivilegeGrant->isRestricted()) {
				$this->comparatorConstraintGroup = null;
				return;
			}
				
			$this->comparatorConstraintGroup->addComparatorConstraint($this->eiMappingFilterDefinition
					->createComparatorConstraint($eiPrivilegeGrant->readRestrictionFilterGroupData()));
		}
	}
	
	private function initEiMappingConstraint() {
		$this->eiMappingConstraintGroup = new EiMappingConstraintGroup(false);
		$this->cachedEiMappingConstraints = array();
			
		foreach ($this->eiPrivilegeGrants as $eiPrivilegeGrant) {
			$eiMappingConstraint = $this->getOrBuildEiMappingConstraint($eiPrivilegeGrant);
			
			if ($eiMappingConstraint === null) {
				$this->eiMappingConstraintGroup = null;
				return;
			}
		
			$this->eiMappingConstraintGroup->add($eiMappingConstraint);
		}
	}
	
	private $cachedEiMappingConstraints = array();
	
	/**
	 * @param EiPrivilegeGrant $eiPrivilegeGrant
	 * @return \rocket\spec\ei\manage\mapping\EiMappingConstraint
	 */
	private function getOrBuildEiMappingConstraint(EiPrivilegeGrant $eiPrivilegeGrant) {
		if (!$eiPrivilegeGrant->isRestricted()) return null;
		
		$objHash = spl_object_hash($eiPrivilegeGrant);
		
		if (isset($this->cachedEiMappingConstraints[$objHash])) {
			return $this->cachedEiMappingConstraints[$objHash];
		}
		
		return $this->cachedEiMappingConstraints[$objHash] = $this->eiMappingFilterDefinition
					->createEiMappingConstraint($eiPrivilegeGrant->readRestrictionFilterGroupData());
	}
	
	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\security\EiExecution::extEiCommandPath($ext)
	 */
	public function extEiCommandPath(string $ext) {
		$this->init($this->eiCommandPath->ext($ext));
	}
	
	public function buildEiCommandAccessRestrictor(EiMapping $eiMapping) {
		$restrictor = new WhitelistEiCommandAccessRestrictor();
		
		foreach ($this->eiPrivilegeGrants as $eiPrivilegeGrant) {
			$eiMappingConstraint = $this->getOrBuildEiMappingConstraint($eiPrivilegeGrant);
			
			if ($eiMappingConstraint !== null && !$eiMappingConstraint->check($eiMapping)) {
				continue;
			}
			
			$restrictor->getEiCommandPaths()->addAll($eiPrivilegeGrant->getEiCommandPaths());
		}
		
		if ($restrictor->getEiCommandPaths()->isEmpty()) return null;
		
		return $restrictor;
	}
}
