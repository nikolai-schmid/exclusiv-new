<?php
namespace exclusiv\controller;

use n2n\web\http\controller\ControllerAdapter;

class IntroController extends ControllerAdapter {
	public function index() {
		$this->forward('..\view\intro.html');
	}
}