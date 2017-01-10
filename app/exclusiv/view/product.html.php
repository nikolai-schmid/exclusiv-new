<?php
	$view->useTemplate('inc\template.html');
	
	$html->meta()->setTitle($html->getText('products_tab_text'));
?>
<div class="">
	<?php for ($i = 0; $i < 10; $i++): ?>
		<div class="col-lg-3" style="height: 200px; width: 200px; background: white; margin: 30px;">
			Komponent <?php $html->out($i) ?>
		</div>
	<?php endfor ?>
</div>