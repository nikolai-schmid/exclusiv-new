<?php
namespace template\controller;

use exclusiv\bo\product\ProductAdapter;
use exclusiv\controller\ProductController;
use n2n\reflection\ArgUtils;
use page\bo\PageController;
use n2n\reflection\annotation\AnnoInit;
use page\annotation\AnnoPage;

class ExclusivPageController extends PageController {
	private static function _annos(AnnoInit $ai) {
		$ai->m('home', new AnnoPage());
	}
	
	public function home() {
		$this->forward('..\view\home.html');
	}
}