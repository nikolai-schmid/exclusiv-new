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

class Motherboard extends ProductAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->p('cpuSocket', new AnnoManyToOne(CpuSocket::getClass()));
		$ai->p('ramTypes', new AnnoManyToMany(RAMType::getClass()));
	}
	
	private $cpuSocket;
	private $formFactor;
	private $chipset;
	private $ramTypes;
	
	/**
	 * @return mixed
	 */
	public function getCpuSocket() {
		return $this->cpuSocket;
	}
	
	/**
	 * @param mixed $cpuSocket
	 */
	public function setCpuSocket(CpuSocket $cpuSocket) {
		$this->cpuSocket = $cpuSocket;
	}
	
	/**
	 * @return mixed
	 */
	public function getFormFactor() {
		return $this->formFactor;
	}
	
	/**
	 * @param mixed $formFactor
	 */
	public function setFormFactor($formFactor) {
		$this->formFactor = $formFactor;
	}
	
	/**
	 * @return mixed
	 */
	public function getChipset() {
		return $this->chipset;
	}
	
	/**
	 * @param mixed $chipset
	 */
	public function setChipset($chipset) {
		$this->chipset = $chipset;
	}
	
	/**
	 * @return mixed
	 */
	public function getRamTypes() {
		return $this->ramTypes;
	}
	
	/**
	 * @param mixed $ramTypes
	 */
	public function setRamTypes(\ArrayObject $ramTypes) {
		$this->ramTypes = $ramTypes;
	}
}