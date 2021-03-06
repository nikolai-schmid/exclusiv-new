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

	use rocket\user\model\RocketUserGroupForm;
	use n2n\web\ui\Raw;
	use n2n\impl\web\ui\view\html\HtmlView;

	$view = HtmlView::view($this);
	$html = HtmlView::html($this);
	$formHtml = HtmlView::formHtml($this);

	$userGroupForm = $view->getParam('userGroupForm'); 
	$view->assert($userGroupForm instanceof RocketUserGroupForm);
 
	$view->useTemplate('~\core\view\template.html', array('title' => $view->getL10nText('user_groups_title')));
	$html->meta()->addJs('js/user-group.js');
?>

<?php $formHtml->open($userGroupForm, null, 'post', array('class' => 'rocket-edit-form'))?>
	<div class="rocket-panel">
		<h3><?php $html->l10nText('common_properties_title') ?></h3>
		
		<?php $formHtml->messageList() ?>
		
		<div class="rocket-properties">
			<div>
				<?php $formHtml->label('name', $html->getL10nText('common_name_label')) ?>
				<div class="rocket-controls">
					<?php $formHtml->input('name', array('maxlength' => 64)) ?>
					<?php $formHtml->message('name', 'div', array('class' => 'rocket-message-error')) ?>
				</div>
			</div>
			<div>
				<label><?php $html->text('user_accessible_menu_items_label') ?></label>
				<div class="rocket-controls rocket-user-group-menu-items"
					data-accessible-items-title="<?php $html->text('user_accessible_menu_items_label') ?>"
					data-unaccessible-items-title="<?php $html->text('user_unaccessible_menu_items_title') ?>"
					data-assign-title="<?php $html->text('common_assign_label') ?>"
					data-unassign-title="<?php $html->text('common_unassign_label') ?>">
					<?php $formHtml->inputCheckbox('menuItemRestrictionEnabled', true, null, 'MenuItemRestrictionEnabled') ?>
					<ul>
						<?php foreach ($userGroupForm->getaccessibleMenuItemIdOptions() as $id => $label): ?>
							<li><?php $formHtml->inputCheckbox('accessibleMenuItemIds[' . $id . ']', $id, null, $label)?></li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div id="rocket-page-controls">	
		<ul>
			<li>
				<?php $formHtml->buttonSubmit('save', new Raw('<i class="fa fa-save"></i><span>' 
								. $html->getL10nText('common_save_label') . '</span>'),
						array('class' => 'rocket-control-warning rocket-important')) ?>
			</li>
		</ul>
	</div>
<?php $formHtml->close() ?>
