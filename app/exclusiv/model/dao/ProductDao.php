<?php
namespace exclusiv\model\dao;

use n2n\context\RequestScoped;
use n2n\persistence\orm\EntityManager;
use exclusiv\bo\product\ProductAdapter;
use exclusiv\bo\product\Motherboard;

class ProductDao implements RequestScoped {
	private $em;
	
	private function _init(EntityManager $em) {
		$this->em = $em;
	}
	
	public function getProducts() {
		return $this->em->createSimpleCriteria(Motherboard::getClass())->toQuery()->fetchArray();
	}
}