<?php
namespace exclusiv\controller;

use n2n\web\http\controller\ControllerAdapter;
use exclusiv\model\dao\ProductDao;
use n2n\web\http\PageNotFoundException;

class ProductController extends ControllerAdapter {
	private $productDao;
	private $type;
	
	private function _init(ProductDao $productDao) {
		$this->productDao = $productDao;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function index() {
		$products = $this->productDao->getProductsOfType($this->type);
		$this->forward('..\..\template\view\product-type.html', array('products' => $products));
	}
	
	public function detail(string $pathPart) {
		$product = $this->productDao->getProductByPathPart();
		if ($product === null) {
			throw new PageNotFoundException();
		}
		
		$this->forward('..\..\template\view\product-detail.html', array('product' => $product));
	}
}