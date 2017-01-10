<?php
	use n2n\impl\web\ui\view\html\HtmlView;
	use n2n\core\err\ThrowableModel;

	$view = HtmlView::view($this);
	$html = HtmlView::html($this);
	
	$throwableModel = $view->getParam('throwableModel');
	$view->assert($throwableModel instanceof ThrowableModel);
	
	$e = $throwableModel->getException();
?>

<div id="status-container">
	<div class="exception">
		<?php if (0 < mb_strlen($message = $e->getMessage())): ?>
			<h2>Message</h2>
			<p class="message">
				<i class="fa fa-exclamation-triangle"></i>
				<?php $html->out($message) ?>
			</p>
		<?php endif ?>
		
		<section>
			<div class="debug-info stack-trace">
				<h3>Stack Trace</h3>
				<pre><?php $html->out(get_class($e) . ': ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString()) ?></pre>
				<?php while (null !== ($e = $e->getPrevious())): ?>
					<pre>caused by <?php $html->out(get_class($e) . ': ' . $e->getMessage() . PHP_EOL 
							. $e->getTraceAsString()) ?></pre>
				<?php endwhile ?>
			</div>
			
			<?php if (0 < mb_strlen($output = $throwableModel->getOutput())): ?>
				<div>
					<h3>Output</h3>
					<pre class="stack-trace"><?php $html->esc($output)?></pre>
				</div>
			<?php endif ?>		
		</section>
	</div>
</div>