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

class RAM extends ObjectAdapter implements Product  {
	private static function _annos(AnnoInit $ai) {
        $ai->p('brand', new AnnoManyToOne(Brand::getClass()));
	    $ai->p('ramType', new AnnoManyToOne(RAMType::getClass()));
        $ai->p('image', new AnnoManagedFile());
    }

    private $id;
    private $name;
    private $brand;
    private $price;
    private $image;
    private $ramType;

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function getBrand() {
        return $this->brand;
    }

    public function setBrand(Brand $brand) {
        $this->brand = $brand;
    }

    public function getPrice() {
        return $this->name;
    }

    public function setPrice(float $price) {
        $this->price = $price;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage(File $image) {
        $this->image = $image;
    }
		
	public function getRamType() {
		return $this->ramType;
	}
	
	public function setRamType(RAMType $ramType) {
		$this->ramType = $ramType;
	}
}