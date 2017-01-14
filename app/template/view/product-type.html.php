<?php
$products = $view->getParam('products');

$view->useTemplate('inc\template.html');
?>
<?php foreach ($products as $product): ?>
	<div class="col-lg-3 prodcut-overview-item">
		<?php $html->image($product->getImage(), \n2n\io\managed\img\impl\ThSt::crop(200, 200)) ?>
		<h3><?php $html->out($product->getName()) ?></h3>
	</div>
<?php endforeach ?>
