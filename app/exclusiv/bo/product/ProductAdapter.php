<?php
namespace exclusiv\bo\product;


use exclusiv\bo\Brand;
use n2n\persistence\orm\annotation\AnnoInheritance;
use n2n\persistence\orm\annotation\AnnoManagedFile;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use n2n\persistence\orm\annotation\AnnoMappedSuperclass;
use n2n\persistence\orm\InheritanceType;
use n2n\reflection\annotation\AnnoInit;
use n2n\reflection\ObjectAdapter;

class ProductAdapter extends ObjectAdapter implements Product {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoInheritance(InheritanceType::JOINED));
		
		$ai->p('image', new AnnoManagedFile());
		$ai->p('brand', new AnnoManyToOne(Brand::getClass()));
	}
	
	protected $id;
	protected $name;
	protected $brand;
	protected $price;
	protected $image;
	
	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @param mixed $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * @return mixed
	 */
	public function getBrand() {
		return $this->brand;
	}
	
	/**
	 * @param mixed $brand
	 */
	public function setBrand($brand) {
		$this->brand = $brand;
	}
	
	/**
	 * @return mixed
	 */
	public function getPrice() {
		return $this->price;
	}
	
	/**
	 * @param mixed $price
	 */
	public function setPrice($price) {
		$this->price = $price;
	}
	
	/**
	 * @return mixed
	 */
	public function getImage() {
		return $this->image;
	}
	
	/**
	 * @param mixed $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}
}