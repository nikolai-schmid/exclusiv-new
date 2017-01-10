<?php
namespace exclusiv\controller;

use n2n\web\http\controller\ControllerAdapter;

class BuildController extends ControllerAdapter {
	public function index() {
		$this->forward('..\view\build.html');
	}
}