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
namespace n2n\io\managed\impl\engine;

use n2n\io\fs\FsPath;
use n2n\util\StringUtils;
use n2n\io\managed\File;
use n2n\io\managed\FileManagingException;
use n2n\io\managed\impl\CommonFile;

class TmpFileEngine {
	const INFO_SUFFIX = '.inf';
	const SESS_PREFIX = 's_';
	const THREAD_PREFIX = 't_';
	
	const INFO_ORIGINAL_NAME_KEY = 'originalName';
	const INFO_SESSION_ID_KEY = 'sessionId';
	
	private $fsPath;
	private $filePerm;
	
	public function __construct(FsPath $fsPath, $filePerm) {
		$this->fsPath = $fsPath;
		$this->filePerm = $filePerm;
	}
	
	private function createThreadTmpFileSource() {
		$fileFsPath = new FsPath(tempnam((string) $this->fsPath, self::THREAD_PREFIX));
		$fileFsPath->chmod($this->filePerm);
		
		return new TmpFileSource($fileFsPath->getName(), $fileFsPath);
	}
	
	private function createSessionTmpFileSource($sessionId, $originalName) {
		$fileFsPath = new FsPath(tempnam($this->fsPath, self::SESS_PREFIX));
		$fileFsPath->chmod($this->filePerm);
		
		$fileInfoDingsler = new FileInfoDingsler($fileFsPath);
		$fileInfoDingsler->write(array(self::INFO_ORIGINAL_NAME_KEY => $originalName, 
				self::INFO_SESSION_ID_KEY => $sessionId));
		
		return new TmpFileSource($fileFsPath->getName(), $fileFsPath, $fileInfoDingsler->getInfoFsPath(), $sessionId);
	}
	

	private function createTmpFileSource($sessionId, $originalName) {
		if ($sessionId === null) {
			return $this->createThreadTmpFileSource();
		}
		 
		return $this->createSessionTmpFileSource($sessionId, $originalName);
	}
	
	public function createFile($sessionId = null, $originalName = null) {
		$tmpFileSource = $this->createTmpFileSource($sessionId, $originalName);	
		
		if ($originalName === null) {
			$originalName = $tmpFileSource->getFileFsPath()->getName();
		}
		
		return new CommonFile($tmpFileSource, $originalName);
	}
	
	public function addFile(File $file, $sessionId = null) {
		$originalName = $file->getOriginalName();
		$tmpFileSource = $this->createTmpFileSource($sessionId, $originalName);
		
		$file->getFileSource()->move($tmpFileSource->getFileFsPath(), $this->filePerm, true);
		$file->setFileSource($tmpFileSource);
		
		return $tmpFileSource->getQualifiedName();
	}
	
	public function createCopyFromFile(File $file, $sessionId = null) {
		$originalName = $file->getOriginalName();
		$tmpFileSource = $this->createTmpFileSource($sessionId, $originalName);
		
		$file->getFileSource()->copy($tmpFileSource->getFileFsPath(), $this->filePerm, true);
		return new CommonFile($tmpFileSource, $originalName);
	}
	
	public function containsSessionFile(File $file, $sessionId) {
		return $file->getFileSource() instanceof TmpFileSource 
				&& $file->getFileSource()->getSessionId() === $sessionId;
	}
	
	public function getSessionFile($qualifiedName, $sessionId) {
		QualifiedNameBuilder::validateLevel($qualifiedName);
		
		$fileFsPath = $this->fsPath->ext($qualifiedName);
		if (!$fileFsPath->exists() || !StringUtils::startsWith(self::SESS_PREFIX, $fileFsPath->getName())) return null;

		$fileInfoDingsler = new FileInfoDingsler($fileFsPath);
		$infoFsPath = $fileInfoDingsler->getInfoFsPath();
		
		$infoData = null;
		try {
			$infoData = $fileInfoDingsler->read();
		} catch (FileManagingException $e) { }
		
		if ($infoData === null || !array_key_exists(self::INFO_SESSION_ID_KEY, $infoData)
				|| !array_key_exists(self::INFO_ORIGINAL_NAME_KEY, $infoData)) {
			$fileFsPath->delete();
			$infoFsPath->delete();
			return null;
		}
		
		if ($infoData[self::INFO_SESSION_ID_KEY] !== $sessionId) {
			return null;
		}
		
		$fileFsPath->touch();
		$infoFsPath->touch();
		
		$originalName = $infoData[self::INFO_ORIGINAL_NAME_KEY];
		if ($originalName === null) {
			$originalName = $fileFsPath->getName();
		}
		
		try {
			return new CommonFile(new TmpFileSource($qualifiedName, $fileFsPath, $infoFsPath, $sessionId),
					$originalName);
		} catch (\InvalidArgumentException $e) {
			return null;
		}
				
	}
	
	public function deleteOldSessionFiles($gcMaxLifetime) {
		foreach ($this->fsPath->getChildren(self::SESS_PREFIX . '*') as $fsPath) {
			if ($gcMaxLifetime < (time() - $fsPath->getMTime())) {
				$fsPath->delete();
			}
		}
	}
	
}
