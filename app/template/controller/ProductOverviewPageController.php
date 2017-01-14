<?php
namespace template\controller;

use exclusiv\bo\product\ProductAdapter;
use exclusiv\controller\ProductController;
use n2n\io\managed\File;
use n2n\persistence\orm\annotation\AnnoManagedFile;
use n2n\reflection\ArgUtils;
use page\bo\PageController;
use n2n\reflection\annotation\AnnoInit;
use page\annotation\AnnoPage;

class ProductOverviewPageController extends PageController {
	private static function _annos(AnnoInit $ai) {
		$ai->p('navImage', new AnnoManagedFile());
		$ai->m('overview', new AnnoPage());
	}
	
	private $type;
	private $navImage;
	
	public function getType() {
		return $this->type;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getNavImage() {
		return $this->navImage;
	}
	
	public function setNavImage(File $image = null) {
		$this->navImage = $image;
	}
	
	public function overview(ProductController $productController) {
		$productController->setType($this->type);
		$this->delegate($productController);
	}
}