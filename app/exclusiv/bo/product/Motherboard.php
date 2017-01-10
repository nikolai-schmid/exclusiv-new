<?php
namespace exclusiv\bo\product;

use exclusiv\bo\Brand;
use n2n\io\managed\File;
use n2n\persistence\orm\annotation\AnnoManagedFile;
use n2n\persistence\orm\annotation\AnnoManyToMany;
use n2n\reflection\annotation\AnnoInit;
use exclusiv\bo\product\ram\RAMType;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use exclusiv\bo\product\cpu\CpuSocket;
use n2n\reflection\ObjectAdapter;

class Motherboard extends ObjectAdapter implements Product {
	private static function _annos(AnnoInit $ai) {
        $ai->p('brand', new AnnoManyToOne(Brand::getClass()));
	    $ai->p('cpuSocket', new AnnoManyToOne(CpuSocket::getClass()));
		$ai->p('ramTypes', new AnnoManyToMany(RAMType::getClass()));
        $ai->p('image', new AnnoManagedFile());
	}

    private $id;
    private $name;
    private $brand;
    private $price;
    private $image;
	private $cpuSocket;
	private $formFactor;
	private $chipset;
	private $ramTypes;

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

    public function getCpuSocket() {
        return $this->cpuSocket;
    }


    public function setCpuSocket($cpuSocket) {
        $this->cpuSocket = $cpuSocket;
    }


    public function getFormFactor() {
        return $this->formFactor;
    }


    public function setFormFactor($formFactor) {
        $this->formFactor = $formFactor;
    }


    public function getChipset() {
        return $this->chipset;
    }

    public function setChipset($chipset) {
        $this->chipset = $chipset;
    }

    public function getRamTypes() {
        return $this->ramTypes;
    }

    public function setRamTypes($ramTypes) {
        $this->ramTypes = $ramTypes;
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

	public function getProzessorSocket() {
		return $this->cpuSocket;
	}
	public function setProzessorSocket(CpuSocket $prozessorSocket) {
		$this->cpuSocket = $prozessorSocket;
	}
}