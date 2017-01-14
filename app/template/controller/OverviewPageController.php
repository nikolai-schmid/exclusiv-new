<?php
namespace template\controller;

use exclusiv\bo\product\ProductAdapter;
use exclusiv\controller\ProductController;
use exclusiv\model\dao\ProductDao;
use n2n\reflection\ArgUtils;
use page\bo\PageController;
use n2n\reflection\annotation\AnnoInit;
use page\annotation\AnnoPage;

class OverviewPageController extends PageController {
	private static function _annos(AnnoInit $ai) {
		$ai->m('overview', new AnnoPage());
	}
	
	public function overview(ProductDao $productDao) {
		$productPages = $productDao->getProductPages();
		
		$this->forward('..\view\product-overview.html', array('productPages' => $productPages));
	}
}