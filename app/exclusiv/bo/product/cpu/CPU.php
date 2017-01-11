<?php
namespace exclusiv\bo\product\cpu;

use exclusiv\bo\Brand;
use exclusiv\bo\product\Product;
use exclusiv\bo\product\ProductAdapter;
use n2n\io\managed\File;
use n2n\persistence\orm\annotation\AnnoManagedFile;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use n2n\reflection\ObjectAdapter;

class CPU extends ProductAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->p('cpuSocket', new AnnoManyToOne(CpuSocket::getClass()));
	}
	
	private $cpuSocket;

    public function getCpuSocket() {
        return $this->cpuSocket;
    }

    public function setCpuSocket(CpuSocket $cpuSocket) {
        $this->cpuSocket = $cpuSocket;
    }
}