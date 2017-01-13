<?php
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\web\ui\view\View;
use page\ui\PageHtmlBuilder;

$view = HtmlView::view($view);
$pageHtml = new PageHtmlBuilder($view);
?>
<div class="jumbotron">
	<div class="container">
		<h1><?php $pageHtml->title() ?></h1>
	</div>
</div>

<div class="container">
	Content kommt hier...
</div>