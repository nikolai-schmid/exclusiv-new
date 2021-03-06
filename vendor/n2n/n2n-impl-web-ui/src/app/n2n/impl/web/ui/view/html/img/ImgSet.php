<?php
namespace n2n\impl\web\ui\view\html\img;

use n2n\reflection\ArgUtils;

class ImgSet {
	private $defaultSrcAttr;
	private $defaultAltAttr;
	private $defaultWidthAttr;
	private $defaultHeightAttr;
	private $imageSourceSets;
	
	public function __construct(string $defaultSrcAttr, string $defaultAltAttr, int $defaultWidthAttr, 
			int $defaultHeightAttr, array $imageSourceSets) {
		ArgUtils::valArray($imageSourceSets, ImageSourceSet::class);
		$this->defaultSrcAttr = $defaultSrcAttr;
		$this->defaultAltAttr = $defaultAltAttr;
		$this->defaultWidthAttr = $defaultWidthAttr;
		$this->defaultHeightAttr = $defaultHeightAttr;
		$this->setImageSourceSets($imageSourceSets);
	}
	
	public function getDefaultSrcAttr() {
		return $this->defaultSrcAttr;
	}
	
	public function setDefaultSrcAttr(string $defaultImageSrc) {
		$this->defaultSrcAttr = $defaultImageSrc;
	}
	
	public function getDefaultAltAttr() {
		return $this->defaultAltAttr;
	}
	
	public function setDefaultAltAttr(string $defaultAltAttr) {
		$this->defaultAltAttr = $defaultAltAttr;
	}
	
	public function getDefaultWidthAttr() {
		return $this->defaultWidthAttr;
	}
	
	public function setDefaultWidthAttr(int $defaultWidthAttr) {
		$this->defaultWidthAttr = $defaultWidthAttr;
	}
	
	public function getDefaultHeightAttr() {
		return $this->defaultHeightAttr;
	}
	
	public function setDefaultHeightAttr(int $defaultHeightAttr) {
		$this->defaultHeightAttr = $defaultHeightAttr;
	}
	
	/**
	 * @return ImageSourceSet[]
	 */
	public function getImageSourceSets() {
		return $this->imageSourceSets;
	}

	/**
	 * @param array $imageSourceSets
	 */
	public function setImageSourceSets(array $imageSourceSets) {
		ArgUtils::valArray($imageSourceSets, ImageSourceSet::class);
		$this->imageSourceSets = $imageSourceSets;
	}
	
	/**
	 * @return boolean
	 */
	public function isPictureRequired() {
		if (count($this->imageSourceSets) > 1) return true;
		
		foreach ($this->imageSourceSets as $imageSourceSet) {
			if (null !== $imageSourceSet->getMediaAttr()) return true;
		}
		
		return false;
	}
}