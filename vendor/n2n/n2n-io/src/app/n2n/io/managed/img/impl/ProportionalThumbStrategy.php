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
namespace n2n\io\managed\img\impl;

use n2n\io\managed\img\ThumbStrategy;
use n2n\io\managed\img\ImageDimension;
use n2n\io\img\ImageSource;
use n2n\io\img\ImageResource;
use n2n\reflection\ArgUtils;

class ProportionalThumbStrategy implements ThumbStrategy {
	private $autoCropMode;
	private $scaleUpAllowed;
	private $imageDimension;

	public function __construct(int $width, int $height, string $autoCropMode = null, bool $scaleUpAllowed = true) {
		ArgUtils::valEnum($autoCropMode, ImageResource::getAutoCropModes(), null, true);
		$this->autoCropMode = $autoCropMode;
		$this->scaleUpAllowed = $scaleUpAllowed;
		$this->imageDimension = new ImageDimension($width, $height, $this->buildIdExt());
	}
	
	private function buildIdExt() {
		$idExt = null;
		if ($this->autoCropMode !== null) {
			$idExt .= 'c' . $this->autoCropMode;
		}
		if ($this->scaleUpAllowed) {
			$idExt .= 's';
		}
		return $idExt;
	}
	
	public function getImageDimension(): ImageDimension {
		return $this->imageDimension;
	}

	public function isAutoCropEnabled(): bool {
		return $this->autoCropMode !== null;
	}
	
	public function getAutoCropMode() {
		return $this->autoCropMode;
	}

	public function isScaleUpAllowed(): bool {
		return $this->scaleUpAllowed;
	}

	public function matches(ImageSource $imageSource): bool {
		if ($this->imageDimension->getWidth() == $imageSource->getWidth()
				&& $this->imageDimension->getHeight() == $imageSource->getHeight()) {
			return true;
		}

		if ($this->scaleUpAllowed) return false;

		return $this->imageDimension->getWidth() > $imageSource->getWidth()
				&& $this->imageDimension->getHeight() > $imageSource->getHeight();
	}

	public function resize(ImageResource $imageResource) {
		$imageResource->proportionalResize($this->imageDimension->getWidth(), $this->imageDimension->getHeight(),
				$this->getAutoCropMode());
	}
}
