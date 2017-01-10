<?php
namespace exclusiv\bo;

use exclusiv\bo\product\cpu\CPU;
use exclusiv\bo\product\Motherboard;
use exclusiv\bo\product\pci\GPU;
use exclusiv\bo\product\ram\RAM;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use n2n\reflection\annotation\AnnoInit;
use n2n\reflection\ObjectAdapter;

class Brand extends ObjectAdapter {
    private static function _annos(AnnoInit $ai) {
        $ai->p('cpus', new AnnoOneToMany(CPU::getClass(), 'brand'));
        $ai->p('gpus', new AnnoOneToMany(GPU::getClass(), 'brand'));
        $ai->p('rams', new AnnoOneToMany(RAM::getClass(), 'brand'));
        $ai->p('motherboards', new AnnoOneToMany(Motherboard::getClass(), 'brand'));
    }

	private $id;
	private $name;

	private $cpus;
    private $gpus;
    private $rams;
    private $motherboards;
	private $products;
	
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

	public function getCpus() {
	    return $this->cpus;
    }

    public function setCpus(\ArrayObject $cpus) {
        $this->cpus = $cpus;
    }

    public function getRams() {
        return $this->rams;
    }

    public function setRams(\ArrayObject $rams) {
        $this->rams = $rams;
    }

    public function getGpus() {
        return $this->gpus;
    }

    public function setGpus($gpus) {
        $this->gpus = $gpus;
    }

    public function getMotherboards() {
        return $this->motherboards;
    }

    public function setMotherboards($motherboards) {
        $this->motherboards = $motherboards;
    }

	public function getProducts() {
		return $this->products;
	}
	
	public function setProducts(\ArrayObject $products) {
		$this->products = $products;
	}
}