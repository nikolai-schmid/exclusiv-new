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
namespace rocket\spec\config\extr;

use rocket\spec\config\extr\EiSpecExtraction;
use n2n\util\config\source\WritableConfigSource;
use n2n\util\config\Attributes;
use n2n\util\config\InvalidConfigurationException;
use n2n\util\config\AttributesException;
use rocket\spec\config\extr\SpecExtractor;
use rocket\spec\config\InvalidSpecConfigurationException;
use rocket\spec\config\InvalidEiMaskConfigurationException;
use n2n\reflection\ArgUtils;

class SpecConfigSourceDecorator {
	private $specRawer;
	private $configSource;
	private $moduleNamespace;
	
	private $attributes;
	private $specExtractions = array();
	private $commonEiMaskExtractionGroups = array();
	private $menuItemExtractions = array();
	
	public function __construct(WritableConfigSource $configSource, $module) {
		$this->attributes = new Attributes();
		$this->configSource = $configSource;
		$this->moduleNamespace = (string) $module;
	} 
	
	public function getModuleNamespace() {
		return $this->moduleNamespace;
	}
	
	public function getConfigSource() {
		return $this->configSource;
	}
	
	public function load() {
		$this->attributes = new Attributes($this->configSource->readArray());
		
		$specExtractor = new SpecExtractor($this->attributes, $this->moduleNamespace);
		
		try {
			$this->specExtractions = $specExtractor->extractSpecs();
			$this->commonEiMaskExtractionGroups = $specExtractor->extractCommonEiMaskGroups();
			$this->menuItemExtractions = $specExtractor->extractMenuItems();
		} catch (AttributesException $e) {
			throw $this->createDataSourceException($e);
		} catch (InvalidSpecConfigurationException $e) {
			throw $this->createDataSourceException($e);
		} catch (InvalidEiMaskConfigurationException $e) {
			throw $this->createDataSourceException($e);
		}
	}
	
	public function flush() {
		$this->specRawer = new SpecRawer($this->attributes);
		$this->specRawer->rawSpecs($this->specExtractions);
		$this->specRawer->rawCommonEiMasks($this->commonEiMaskExtractionGroups);
		$this->specRawer->rawMenuItems($this->menuItemExtractions);
		
		$this->configSource->writeArray($this->attributes->toArray());
	}
	
	public function clear() {
		if ($this->specRawer !== null) {
			$this->specRawer->clear();
		}
		
		$this->attributes = new Attributes();
		
		$this->specExtractions = array();
		$this->commonEiMaskExtractionGroups = array();
		$this->menuItemExtractions = array();
	}
		
	public function getSpecExtractions() {		
		return $this->specExtractions;	
	}
	
	public function setSpecExtractions(array $specExtractions) {
		ArgUtils::valArray($specExtractions, SpecExtraction::class);
		$this->specExtractions = $specExtractions;
	}
	
	public function addSpecExtraction(SpecExtraction $specExtraction) {
		$this->specExtractions[$specExtraction->getId()] = $specExtraction;
	}
	
	private function createDataSourceException(\Exception $previous) {
		throw new InvalidConfigurationException('Configruation error in data source: ' . $this->configSource, 0, $previous);
	}
	
	public function getCommonEiMaskEiSpecIds() {
		return array_keys($this->commonEiMaskExtractionGroups);
	}
	
	public function getCommonEiMaskExtractionsByEiSpecId($eiSpecId) {
		if (isset($this->commonEiMaskExtractionGroups[$eiSpecId])) {
			return $this->commonEiMaskExtractionGroups[$eiSpecId];
		}

		return array();
	}
	
	public function setCommonEiMaskExtractions($eiSpecId, array $commonEiMaskExtractions) {
		$this->commonEiMaskExtractionGroups[$eiSpecId] = $commonEiMaskExtractions;
	}
	
	public function addCommonEiMaskExtraction($eiSpecId, CommonEiMaskExtraction $commonEiMaskExtraction) {
		if (!isset($this->commonEiMaskExtractionGroups[$eiSpecId])) {
			$this->commonEiMaskExtractionGroups[$eiSpecId] = array();
		}
		
		$this->commonEiMaskExtractionGroups[$eiSpecId][] = $commonEiMaskExtraction;
	}
	
	public function containsSpecId($specId) {
		return isset($this->specExtractions[$specId]);
	}
	
	public function containsEntityClassName(string $entityClassName): bool {
		foreach ($this->specExtractions as $id => $spec) {
			if ($spec instanceof EiSpecExtraction && $spec->getEntityClassName() == $entityClassName) {
				return true;
			}
		}
		
		return false;
	}
	
	public function containsCommonEiMaskId(string $eiSpecId, string $commonEiMaskId): bool {
		return isset($this->commonEiMaskExtractionGroups[$eiSpecId][$commonEiMaskId]);
	}
	
	public function containsMenuItemId(string $menuItemId): bool {
		return isset($this->menuItemExtractions[$menuItemId]);
	}
	
	public function getMenuItemExtractions(): array {
		return $this->menuItemExtractions;
	}
	
	public function setMenuItemExtractions(array $menuItemExtractions) {
		ArgUtils::valArray($menuItemExtractions, MenuItemExtraction::class);
		$this->menuItemExtractions = $menuItemExtractions;
	}
	
	public function addMenuItemExtraction(MenuItemExtraction $menuItemExtraction) {
		$this->menuItemExtractions[$menuItemExtraction->getId()] = $menuItemExtraction;
	}
}
