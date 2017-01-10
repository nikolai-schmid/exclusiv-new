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
namespace n2n\core;

use n2n\reflection\ReflectionUtils;
use n2n\io\InvalidPathException;
use n2n\io\fs\FsPath;
use n2n\io\IoUtils;
use n2n\util\ex\IllegalStateException;

class VarStore {
	const CATEGORY_ETC = 'etc';
	const CATEGORY_LOG = 'log';
	const CATEGORY_SRV = 'srv';
	const CATEGORY_BAK = 'bak';
	const CATEGORY_TMP = 'tmp';
	
	private $varPath;
	private $dirPerm;
	private $filePerm;
	/**
	 * 
	 * @param string $varPath
	 * @param string $dirPerm
	 * @param string $filePerm
	 */
	public function __construct($varPath, $dirPerm, $filePerm) {
		$this->varPath = $varPath;
		$this->dirPerm = $dirPerm;
		$this->filePerm = $filePerm;
	}
	
	public function setDirPerm($dirPerm) {
		$this->dirPerm = $dirPerm;
	}
	
	public function getDirPerm() {
		return $this->dirPerm;
	}
	
	public function setFilePerm($filePerm) {
		$this->filePerm = $filePerm;
	}
	
	public function getFilePerm() {
		return $this->filePerm;
	}
	
	private function validatePathPart($pathPart) {
		if (!IoUtils::hasSpecialChars($pathPart)) return;
		
		throw new \InvalidArgumentException('Path part contains invalid chars: ' . $pathPart);
	}
	/**
	 * 
	 * @param string $category
	 * @param string $directoryName
	 * @param bool $create
	 * @param bool $required
	 * @throws VarStoreException
	 * @return \n2n\io\fs\FsPath
	 */
	public function requestDirFsPath($category, $module, $directoryName, $create = true, $required = true) {
		if (!in_array($category, self::getCategories())) {
			throw new \InvalidArgumentException('Invalid var category \'' . $category . '\'. Available categories: '
					. implode(', ', self::getCategories()));
		}
		
		$dirPath = $this->varPath . DIRECTORY_SEPARATOR . $category;
		if (isset($module)) {
			$modulePathPart = self::namespaceToDirName((string) $module);
			$this->validatePathPart($modulePathPart);
			$dirPath .= DIRECTORY_SEPARATOR . $modulePathPart;
		}
		
		if (isset($directoryName)) {
			$this->validatePathPart($directoryName);
			$dirPath .= DIRECTORY_SEPARATOR . $directoryName;
		}
		
		$path = new FsPath($dirPath);
		if ($path->isDir()) return $path;
		
		if ($create) {
			if ($this->dirPerm === null) {
				throw new IllegalStateException('Dir perm not defined for VarStore. Could not create directory: ' . $path);
			}
			$path->mkdirs($this->dirPerm);
			return $path;
		}
		
		if (!$required) return $path;
		
		throw new InvalidPathException('Var directory not found: ' . $path);
	}
	
	public function requestFileFsPath($category, $module, $folderName, $fileName, $createFolder = false, $createFile = false, $required = true) {
		$dirPath = $this->requestDirFsPath($category, $module, $folderName, $createFolder || $createFile, $required);
		
		$this->validatePathPart($fileName);
		$filePath = new FsPath($dirPath . DIRECTORY_SEPARATOR . $fileName);
		if ($filePath->isFile()) return $filePath;
		
		if ($createFile) {
			if ($this->filePerm === null) {
				throw new IllegalStateException('File perm not defined for VarStore. Could not create file: ' . $filePath);
			}
			
			$filePath->createFile($this->filePerm);
			return $filePath;
		}
		
		if (!$required) return $filePath;
		
		throw new InvalidPathException('Var file not found: ' . $filePath);
	}
	/**
	 * 
	 * @return array
	 */
	public static function getCategories() {
		return array(self::CATEGORY_ETC, self::CATEGORY_LOG, self::CATEGORY_SRV, self::CATEGORY_BAK, 
				self::CATEGORY_TMP);
	}
	
	public static function namespaceToDirName(string $namespace) {
		return ReflectionUtils::encodeNamespace($namespace);
	}
	
	public static function dirNameToNamespace(string $dirName) {
		$namespace = ReflectionUtils::decodeNamespace($dirName);
		
		if (ReflectionUtils::hasSpecialChars($namespace, false)) {
			throw new \InvalidArgumentException('Invalid namespace: ' . $namespace);
		}
		
		return $namespace;
	}
}
