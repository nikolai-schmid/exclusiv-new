<?php
namespace page\rocket\ei\field\conf;

use n2n\core\container\N2nContext;

use rocket\spec\ei\component\field\impl\ci\conf\ContentItemsEiFieldConfigurator;

class PageContentItemsEiFieldConfigurator extends ContentItemsEiFieldConfigurator {
	
	public function createMagCollection(N2nContext $n2nContext) {
		$optionCollection = parent::createMagCollection($n2nContext);
		$optionCollection->removeOptionByPropertyName(self::ATTR_PANELS_KEY);
		return $optionCollection;
	}
}