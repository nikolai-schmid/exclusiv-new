<?php
use n2n\impl\web\ui\view\html\HtmlView;
use page\ui\PageHtmlBuilder;

$view = HtmlView::view($view);
$pageHtml = new PageHtmlBuilder($view);

$productPages = $view->getParam('productPages');

$view->useTemplate('inc\template.html');
?>
<div class="">
	<?php foreach ($productPages as $productPage): ?>
		<?php $type = $productPage->getType(); ?>
		<div class="col-lg-3 prodcut-overview-item">
			<?php $html->linkStart(\page\model\nav\murl\MurlPage::obj($productPage))?>
            <?php $html->image($productPage->getNavImage(), \n2n\io\managed\img\impl\ThSt::crop(200, 200)) ?>
			<h3><?php $html->text($type . '_text') ?></h3>
			<?php $html->linkEnd() ?>
		</div>
	<?php endforeach ?>
</div>