<?php
namespace exclusiv\model\dao;

use n2n\context\RequestScoped;
use n2n\persistence\orm\EntityManager;
use exclusiv\bo\product\ProductAdapter;
use exclusiv\bo\product\Motherboard;
use template\controller\ProductOverviewPageController;
use template\controller\ProductPageController;

class ProductDao implements RequestScoped {
	private $em;
	
	private function _init(EntityManager $em) {
		$this->em = $em;
	}
	
	public function getProducts() {
		return $this->em->createSimpleCriteria(ProductAdapter::getClass())->toQuery()->fetchArray();
	}
	
	public function getProductPages() {
		return $this->em->createSimpleCriteria(ProductOverviewPageController::getClass())->toQuery()->fetchArray();
	}
	
	public function getProductsOfType($productType) {
		$products = $this->em->createSimpleCriteria(ProductAdapter::getClass())->toQuery()->fetchArray();
		$foundProducts = array();
	
		while (null !== ($product = array_shift($products))) {
			if ($product->getType() !== $productType) continue;
			$foundProducts[] = $product;
		}
		
		return $foundProducts;
	}
}