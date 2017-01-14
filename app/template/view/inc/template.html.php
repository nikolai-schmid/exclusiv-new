<?php
    $html->meta()->addjs("js/jquery-2.1.4.min.js");
    $html->meta()->addjs("js/bootstrap.min.js");
    $html->meta()->addjs("js/search.min.js");
    $html->meta()->addjs("js/shoppingCart.min.js");
    $html->meta()->addjs("js/hanshel.min.js");
    $html->meta()->addCss("css/bootstrap_3.3.7/bootstrap-theme.min.css");
    $html->meta()->addCss("css/bootstrap_3.3.7/bootstrap.min.css");
    $html->meta()->addCss("css/hanshel.css");
    
    $pageHtml = new \page\ui\PageHtmlBuilder($view);
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
						<?php $pageHtml->navigation() ?>
					</div>
				</div>
			</nav>
		</div>
	</header>
	<?php $pageHtml->breadcrumbs() ?>
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