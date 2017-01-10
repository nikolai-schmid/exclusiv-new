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
namespace n2n\io\orm;

use n2n\io\managed\File;
use n2n\util\ex\IllegalStateException;
use n2n\io\managed\FileSource;
use n2n\web\http\nav\UnavailableMurlException;
use n2n\util\uri\Url;

class UnknownFile implements File {
	private $qualifiedName;
	private $fileManagerName;
	
	public function __construct(string $qualifiedName, string $fileManagerName) {
		$this->qualifiedName = $qualifiedName;
		$this->fileManagerName = $fileManagerName;
	}
	
	public function isValid(): bool {
		return false;
	}
	
	public function getQualifiedName(): string {
		return $this->qualifiedName;
	}
	
	private function throwException() {
		throw new IllegalStateException('Unknown qualified name for FileManager \'' 
				. $this->fileManagerName . '\': ' . $this->qualifiedName);
	}
	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::getOriginalName()
	 */
	public function getOriginalName(): string {
		$this->throwException();
	}
	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::setOriginalName()
	 */
	public function setOriginalName(string $originalName = null) {
		$this->throwException();
	}

	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::getOriginalExtension()
	 */
	public function getOriginalExtension() {
		$this->throwException();
	}
	
	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::setFileSource()
	 */
	public function setFileSource(FileSource $fileSource) {
		$this->throwException();
	}
	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::getFileSource()
	 */
	public function getFileSource(): FileSource {
		$this->throwException();
	}
	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::__toString()
	 */
	public function __toString(): string {
		return 'missing file (' . $this->qualifiedName . ' ' . $this->fileManagerName .')';
	}

	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::delete()
	 */
	public function delete() {
		$this->throwException();
	}

	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::move()
	 */
	public function move($fsPath, string $filePerm, bool $overwrite = true) {
		$this->throwException();
	}

	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::copy()
	 */
	public function copy($fsPath, string $filePerm, bool $overwrite = true): File {
		$this->throwException();
	}

	/* (non-PHPdoc)
	 * @see \n2n\io\managed\File::equals()
	 */
	public function equals($o): bool {
		$this->throwException();
	}

	/* (non-PHPdoc)
	 * @see \n2n\web\http\ResponseContent::responseOut()
	 */
	public function responseOut() {
		$this->throwException();
	}

	/* (non-PHPdoc)
	 * @see \n2n\web\http\ResponseContent::getEtag()
	 */
	public function getEtag() {
		$this->throwException();
	}

	/* (non-PHPdoc)
	 * @see \n2n\web\http\ResponseContent::getLastModified()
	 */
	public function getLastModified() {
		$this->throwException();
	}

	/* (non-PHPdoc)
	 * @see \n2n\web\http\ResponseThing::prepareForResponse()
	 */
	public function prepareForResponse(\n2n\web\http\Response $response) {
		$this->throwException();
	}

	/* (non-PHPdoc)
	 * @see \n2n\web\http\ResponseThing::toKownResponseString()
	 */
	public function toKownResponseString() {
		return $this->__toString();
	}

	public function toUrl(string &$suggestedLabel = null): Url {
		try {
			return $this->getFileSource()->getUrl();
		} catch (IllegalStateException $e) {
			throw new UnavailableMurlException(false, null, 0, $e);
		}
	}
}
