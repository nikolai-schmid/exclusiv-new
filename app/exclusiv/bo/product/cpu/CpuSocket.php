<?php
namespace exclusiv\bo\product\cpu;

use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use exclusiv\bo\product\Motherboard;

class CpuSocket extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->p('motherboards', new AnnoOneToMany(Motherboard::getClass(), 'cpuSocket'));
		$ai->p('cpus', new AnnoOneToMany(CPU::getClass(), 'cpuSocket'));
	}

	private $id;
	private $name;
	private $motherboards;
	private $cpus;

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

    public function getMotherboards() {
        return $this->motherboards;
    }

    public function setMotherboards($motherboards){
        $this->motherboards = $motherboards;
    }

	public function getCpus() {
		return $this->cpus;
	}
	
	public function setCpus(\ArrayObject $cpus) {
		$this->cpus = $cpus;
	}
}