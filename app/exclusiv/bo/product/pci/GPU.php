<?php
namespace exclusiv\bo\product\pci;

use exclusiv\bo\Brand;
use exclusiv\bo\product\Product;
use exclusiv\bo\product\ProductAdapter;
use n2n\io\managed\File;
use n2n\persistence\orm\annotation\AnnoManagedFile;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use n2n\reflection\ObjectAdapter;

class GPU extends ProductAdapter {
	public function getType() {
		return ProductAdapter::TYPE_GPU;
	}
}