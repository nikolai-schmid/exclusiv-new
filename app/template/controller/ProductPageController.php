<?php
namespace template\controller;

use exclusiv\bo\product\ProductAdapter;
use exclusiv\controller\ProductController;
use n2n\reflection\ArgUtils;
use page\bo\PageController;
use n2n\reflection\annotation\AnnoInit;
use page\annotation\AnnoPage;

class ProductPageController extends PageController {
	private static function _annos(AnnoInit $ai) {
		$ai->m('overview', new AnnoPage());
	}

	private $type;
	
	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function overview(ProductController $productController) {
		$productController->setType($this->type);
		$this->delegate($productController);
	}
}