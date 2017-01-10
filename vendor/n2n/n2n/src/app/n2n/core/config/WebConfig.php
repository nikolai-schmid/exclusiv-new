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
namespace n2n\core\config;

use n2n\web\http\Supersystem;

class WebConfig {
	private $responseCachingEnabled;
	private $responseBrowserCachingEnabled; 
	private $responseSendEtagAllowed;
	private $responseSendLastModifiedAllowed; 
	private $viewCachingEnabled; 
	private $viewClassNames; 
	private $mainControllerDefs; 
	private $filterControllerDefs; 
	private $supersystem;
	private $subsystems;
	private $dispatchPropertyProviderClassNames;
	private $dispatchTargetCryptAlgorithm;
	private $aliasN2nLocales;
	
	public function __construct(bool $responseCachingEnabled, bool $responseBrowserCachingEnabled, bool $responseSendEtagAllowed, 
			bool $responseSendLastModifiedAllowed, bool $viewCachingEnabled, array $viewClassNames, array $mainControllerDefs,
			array $filterControllerDefs, Supersystem $supersystem, array $subsystems, array $dispatchPropertyProviderClassNames,
			string $dispatchTargetCryptAlgorithm = null, array $aliasN2nLocales) {
		$this->responseCachingEnabled = $responseCachingEnabled;
		$this->responseBrowserCachingEnabled = $responseBrowserCachingEnabled;
		$this->responseSendEtagAllowed = $responseSendEtagAllowed;
		$this->responseSendLastModifiedAllowed = $responseSendLastModifiedAllowed;
		$this->viewCachingEnabled = $viewCachingEnabled;
		$this->viewClassNames = $viewClassNames;
		$this->mainControllerDefs = $mainControllerDefs;
		$this->filterControllerDefs = $filterControllerDefs;
		$this->supersystem = $supersystem;
		$this->subsystems = $subsystems;
		$this->dispatchPropertyProviderClassNames = $dispatchPropertyProviderClassNames;
		$this->dispatchTargetCryptAlgorithm = $dispatchTargetCryptAlgorithm;
		$this->aliasN2nLocales = $aliasN2nLocales;
	}
	
	public function isResponseCachingEnabled(): bool {
		return $this->responseCachingEnabled;
	}
	
	public function isResponseBrowserCachingEnabled(): bool {
		return $this->responseBrowserCachingEnabled;
	}
	
	public function isResponseSendEtagAllowed(): bool {
		return $this->responseSendEtagAllowed;
	}
	
	public function isResponseSendLastModifiedAllowed(): bool {
		return $this->responseSendLastModifiedAllowed;
	}
	
	public function isViewCachingEnabled(): bool {
		return $this->viewCachingEnabled;
	}
	
	public function getViewClassNames(): array {
		return $this->viewClassNames;
	}
	
	public function getMainControllerDefs(): array {
		return $this->mainControllerDefs;
	}
	
	public function getFilterControllerDefs(): array {
		return $this->filterControllerDefs;
	}
	
	/**
	 * @return \n2n\l10n\N2nLocale[]
	 */
	public function getAllN2nLocales(): array {
		$n2nLocales = $this->supersystem->getN2nLocales();
		foreach ($this->subsystems as $supersystem) {
			$n2nLocales = array_merge($n2nLocales, $supersystem->getN2nLocales());
		}
		return $n2nLocales;
	}
	
	public function getSupersystem(): Supersystem {
		return $this->supersystem;
	}
	
	/**
	 * @return string[]
	 */
	public function getSubsystemNames(): array {
		return array_keys($this->subsystems);
	}
	
	/**
	 * @return Subsystem[]
	 */
	public function getSubsystems(): array {
		return $this->subsystems;
	}
	
	/**
	 * @return string[] 
	 */
	public function getDispatchPropertyProviderClassNames(): array {
		return $this->dispatchPropertyProviderClassNames;
	}
	
	/**
	 * @return string
	 */
	public function getDispatchTargetCryptAlgorithm() {
		return $this->dispatchTargetCryptAlgorithm;
	}
	
	/**
	 * @return N2nLocale[] 
	 */
	public function getAliasN2nLocales() {
		return $this->aliasN2nLocales;
	}
}
