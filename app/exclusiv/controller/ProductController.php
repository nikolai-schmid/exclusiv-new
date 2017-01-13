<?php
namespace exclusiv\controller;

use n2n\web\http\controller\ControllerAdapter;
use exclusiv\model\dao\ProductDao;

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
		$this->forward('..\view\product.html');
	}
}