<?php
namespace exclusiv\controller;

use n2n\web\http\controller\ControllerAdapter;
use exclusiv\model\dao\ProductDao;

class ProductController extends ControllerAdapter {
	private $productDao;
	
	private function _init(ProductDao $productDao) {
		$this->productDao = $productDao;
	}
	
	public function index() {
		$this->forward('..\view\product.html');
	}
}