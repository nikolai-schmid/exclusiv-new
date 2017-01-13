<?php
namespace exclusiv\bo\product\ram;

use exclusiv\bo\Brand;
use exclusiv\bo\product\Product;
use n2n\io\managed\File;
use n2n\persistence\orm\annotation\AnnoManagedFile;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use exclusiv\bo\product\ProductAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\reflection\ObjectAdapter;

class RAM extends ProductAdapter  {
	private static function _annos(AnnoInit $ai) {
	    $ai->p('ramType', new AnnoManyToOne(RAMType::getClass()));
    }

    private $ramType;

	public function getRamType() {
		return $this->ramType;
	}
	
	public function setRamType(RAMType $ramType) {
		$this->ramType = $ramType;
	}
	
	public function getType() {
		return ProductAdapter::TYPE_RAM;
	}
}