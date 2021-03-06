<?php
namespace page\rocket\ei\field\conf;

use rocket\spec\ei\component\EiSetupProcess;
use n2n\reflection\CastUtils;
use n2n\core\container\N2nContext;
use page\config\PageConfig;
use rocket\spec\ei\component\field\impl\adapter\AdaptableEiFieldConfigurator;
use page\model\PageDao;
use page\rocket\ei\field\PageHookEiField;
use rocket\spec\ei\component\IndependentEiComponent;
use n2n\web\dispatch\mag\MagDispatchable;
use page\rocket\ei\field\modificator\ConfigEiFieldModificator;

class PageHookEiFieldConfigurator extends AdaptableEiFieldConfigurator {
	private $pageHookEiField;
	
	public function __construct(PageHookEiField $pageHookEiField) {
		parent::__construct($pageHookEiField);
		$this->autoRegister();
		$this->pageHookEiField = $pageHookEiField;
	}
	
	public function getTypeName(): string {
		return 'Hooks Ei Field (Page)';
	}
	
	public function setup(EiSetupProcess $eiSetupProcess) {
		parent::setup($eiSetupProcess);
		
		$pageConfig = $eiSetupProcess->getN2nContext()->getModuleConfig('page');
		CastUtils::assertTrue($pageConfig instanceof PageConfig);
		
		
//		@todo later
// 		$pageDao = $eiSetupProcess->getN2nContext()->lookup('page\model\PageDao');
// 		$pageDao instanceof PageDao;
		
// 		$choicesMap = array();
// 		foreach ($pageConfig->getCharacteristicKeys() as $characteristicKey) {
// 			if (null !== $pageDao->getPageByCharacteristicKey($characteristicKey)) continue;
// 			$choicesMap[$characteristicKey] = $characteristicKey;
// 		}
		
		$this->pageHookEiField->setOptions($pageConfig->getHooks());
	}
}