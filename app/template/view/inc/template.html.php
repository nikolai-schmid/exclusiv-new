<?php
$html->meta()->addCss("assets/css/hanshel.css");
?>
<html>
<?php $html->headStart()?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php $html->headEnd()?>

<?php $html->bodyStart()?>
<div class="container-fluid">
	<header class="row">
		<div class="container">
			<nav>
				<button id="nav-toggle" type="button" class="navbar-toggle collapsed">
					
					<span class="sr-only">Toggle navigation</span>
					<i class="glyphicon glyphicon-menu-hamburger"></i>
				</button>
				
				<div id="logo-header">
					<?php $html->linkStart($html->meta()->getContextUrl(''), null, 'home-link', array('class' => 'navbar-brand')) ?>
					<?php $html->imageAsset(array('img', 'logo.jpg'), 'logo')?>
					<?php $html->linkEnd() ?>
				</div>
				
				<div id="navbar" class="col-xs-12">
					<div id="nav-links" class="collapse">
						<ul class="nav navbar-nav navbar-right">
							<li role="presentation"><?php $html->linkToContext('', $html->getText('home_text')) ?></li>
							<li role="presentation"><?php $html->linkToContext(array('product'), $html->getText('product_text'))?></li>
							<li role="presentation"><?php $html->linkToContext(array('about'), $html->getText('about_text'))?></li>
							<li role="presentation"><?php $html->linkToContext(array('login'), $html->getText('login_text'))?></li>
							<li role="presentation"><?php $html->linkToContext(array('register'), $html->getText('register_text'))?></li>
						</ul>
					</div>
				</div>
			</nav>
		</div>
	</header>
	<div class="container">
		<div id="content">
			<?php $view->importContentView() ?>
		</div>
	</div>
	<div class="row">
		<footer>
			<div class="container">
				<p>&copy; Copyright <?php $html->out(date('Y')) ?> - Exclusiv-Pc</p>
				<div class="pull-right">
					<div class="footer-icon">
						<a href=""><i class="glyphicon glyphicon-earphone"></i></a>
					</div>
					<div class="footer-icon">
						<a href=""><i class="glyphicon glyphicon-envelope"></i></a>
					</div>
				</div>
			</div>
		</footer>
	</div>
</div>
<?php $html->bodyEnd()?>
</html>