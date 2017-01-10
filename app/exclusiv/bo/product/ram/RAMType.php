<?php
namespace exclusiv\bo\product\ram;

use n2n\persistence\orm\annotation\AnnoManyToMany;
use n2n\reflection\annotation\AnnoInit;
use n2n\reflection\ObjectAdapter;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use exclusiv\bo\product\Motherboard;

class RAMType extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->p('motherboards', new AnnoManyToMany(Motherboard::getClass(), 'ramTypes'));
		$ai->p('rams', new AnnoOneToMany(RAM::getClass(), 'ramType'));
	}
	
	private $id;
	private $name;
	private $rams;
	private $motherboards;
	
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
	
	public function getRams() {
		return $this->rams;
	}
	
	public function setRams(\ArrayObject $rams) {
		$this->rams = $rams;
	}
	
	public function getMotherboards() {
		return $this->motherboards;
	}
	
	public function setMotherboards(\ArrayObject $motherboards) {
		$this->motherboards = $motherboards;
	}
}