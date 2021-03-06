<?php
	/*
	 * Copyright (c) 2012-2016, Hofmänner New Media.
	 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
	 *
	 * This file is part of the n2n module ROCKET.
	 *
	 * ROCKET is free software: you can redistribute it and/or modify it under the terms of the
	 * GNU Lesser General Public License as published by the Free Software Foundation, either
	 * version 2.1 of the License, or (at your option) any later version.
	 *
	 * ROCKET is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
	 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU Lesser General Public License for more details: http://www.gnu.org/licenses/
	 *
	 * The following people participated in this project:
	 *
	 * Andreas von Burg...........:	Architect, Lead Developer, Concept
	 * Bert Hofmänner.............: Idea, Frontend UI, Design, Marketing, Concept
	 * Thomas Günther.............: Developer, Frontend UI, Rocket Capability for Hangar
	 */

	use n2n\web\dispatch\map\PropertyPath;
	use rocket\spec\ei\manage\control\IconType;
	use n2n\impl\web\ui\view\html\HtmlView;
	use rocket\spec\ei\manage\critmod\filter\impl\controller\FilterAjahHook;

	$view = HtmlView::view($this);
	$html = HtmlView::html($this);
	$formHtml = HtmlView::formHtml($this);
	
	$propertyPath = $view->getParam('propertyPath');
	$view->assert($propertyPath instanceof PropertyPath);
	
	$filterDefinition = $formHtml->meta()->getMapValue($propertyPath)->getObject()->getFilterDefinition();

	$filterAjahHook = $view->getParam('filterAjahHook');
	$view->assert($filterAjahHook instanceof FilterAjahHook);
	
	$html->meta()->addJs('js/filters.js', 'rocket');
	
	$filterFieldAttrs = array();
	foreach ($filterDefinition->getFilterFields() as $id => $filterItem) {
		$filterFieldAttrs[$id] = $filterItem->getLabel($view->getN2nLocale());
	}
?>
<div class="rocket-filter" 
		data-icon-class-name-add="<?php $html->out(IconType::ICON_PLUS_CIRCLE) ?>"
		data-remove-icon-class-name="<?php $html->out(IconType::ICON_TIMES)?>"
		data-and-icon-class-name="fa fa-toggle-on" 
		data-or-icon-class-name="fa fa-toggle-off" 
		data-text-add-group="<?php $html->text('ei_filter_add_group_label') ?>" 
		data-text-add-field="<?php $html->text('ei_filter_add_field_label') ?>" 
		data-text-remove="<?php $html->text('common_delete_label') ?>"
		data-text-or="<?php $html->text('common_or_label') ?>"
		data-text-and="<?php $html->text('common_and_label') ?>"
		data-filter-field-item-form-url="<?php $html->out($filterAjahHook->getFieldItemFormUrl()) ?>"
		data-filter-group-form-url="<?php $html->out($filterAjahHook->getGroupFormUrl()) ?>"
		data-filter-fields="<?php $html->out(json_encode($filterFieldAttrs)) ?>">
	
	<?php $view->import('\rocket\spec\ei\manage\critmod\filter\impl\view\filterGroupForm.html', 
			array('propertyPath' => $propertyPath)) ?>
</div>
