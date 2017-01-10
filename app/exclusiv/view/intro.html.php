<?php
	$view->useTemplate('inc\template.html', array('showSearch' => true));

	$html->meta()->setTitle($html->getText('home_tab_text'));
?>

<div class="row">
	<div class="panel col-lg-12">
		Suche: <input type="text" />
	</div>
</div>

<div class="row">
	<div class="panel col-lg-2" style="height: 1000px;">
		<ul>
			<?php for ($i = 0; $i < 10; $i++): ?>
				<li>Kategorie <?php $html->out($i) ?></li>
			<?php endfor ?>
		</ul>
	</div>
	<div class="panel col-lg-10">
		<h2><?php $html->text('build_exclusiv_pc_title') ?></h2>
		<?php $html->text('build_exclusiv_pc_text') ?>
	</div>
	<div class="panel col-lg-10">
		<h2><?php $html->text('products_title') ?></h2>
		<ul>
			<?php for ($i = 0; $i < 10; $i++): ?>
				<li>Set <?php $html->out($i) ?></li>
			<?php endfor ?>
		</ul>
	</div>
</div>