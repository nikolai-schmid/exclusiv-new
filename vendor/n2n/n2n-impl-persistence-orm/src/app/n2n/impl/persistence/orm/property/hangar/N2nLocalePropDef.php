<?php
/*
 * Copyright (c) 2012-2016, Hofmänner New Media.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This file is part of the N2N FRAMEWORK.
 *
 * The N2N FRAMEWORK is free software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * N2N is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details: http://www.gnu.org/licenses/
 *
 * The following people participated in this project:
 *
 * Andreas von Burg.....: Architect, Lead Developer
 * Bert Hofmänner.......: Idea, Community Leader, Marketing
 * Thomas Günther.......: Developer, Hangar
 */
namespace n2n\impl\persistence\orm\property\hangar;

use hangar\entity\model\HangarPropDef;
use hangar\entity\model\PropSourceDef;
use n2n\util\config\Attributes;
use n2n\web\dispatch\mag\MagCollection;
use hangar\entity\model\DbInfo;
use n2n\persistence\orm\property\EntityProperty;
use n2n\reflection\annotation\AnnotationSet;
use n2n\impl\persistence\orm\property\N2nLocaleEntityProperty;
use n2n\l10n\N2nLocale;
use n2n\reflection\ArgUtils;
use hangar\core\config\ColumnDefaults;
use n2n\persistence\meta\structure\common\CommonStringColumn;
use hangar\entity\model\CompatibilityLevel;

class N2nLocalePropDef implements HangarPropDef {
	const DEFAULT_LOCALE_COLUMN_LENGTH = '12';
	
	protected $columnDefaults;
	
	public function setup(ColumnDefaults $columnDefaults) {
		$this->columnDefaults = $columnDefaults;
	}
	
	public function getName() {
		return 'N2nLocale';
	}
	
	public function getEntityPropertyClass() {
		return new \ReflectionClass(N2nLocaleEntityProperty::class);
	}
	
	public function createMagCollection(PropSourceDef $propSourceDef = null) {
		return new MagCollection();
	}
	
	public function updatePropSourceDef(Attributes $attributes, PropSourceDef $propSourceDef) {
		$propSourceDef->setBoolean(false);
		$propSourceDef->setReturnTypeName(N2nLocale::class);
		$propSourceDef->setSetterTypeName(N2nLocale::class);
	}
	/**
	 * Apply to Database
	 *
	 * @param string $columnName
	 * @param ColumnFactory $columnFactory
	 * @param PropSourceDef $propSourceDef
	 */
	public function applyDbMeta(DbInfo $dbInfo, PropSourceDef $propSourceDef, EntityProperty $entityProperty, 
			AnnotationSet $annotationSet) {
		ArgUtils::assertTrue($entityProperty instanceof N2nLocaleEntityProperty);
		$columnName = $entityProperty->getColumnName();
		$dbInfo->removeColumn($columnName);
		
		$dbInfo->getTable()->createColumnFactory()
				->createStringColumn($columnName, self::DEFAULT_LOCALE_COLUMN_LENGTH);
	}
	

	/**
	 * @param PropSourceDef $propSourceDef
	 * @return \n2n\persistence\meta\structure\Column
	 */
	public function createMetaColumn(EntityProperty $entityProperty, PropSourceDef $propSourceDef) {
		ArgUtils::assertTrue($entityProperty instanceof N2nLocaleEntityProperty);
		return new CommonStringColumn($entityProperty->getColumnName(), self::DEFAULT_LOCALE_COLUMN_LENGTH);
	}
	
	/**
	 * @param EntityProperty $entityProperty
	 * @return int
	 */
	public function testCompatibility(EntityProperty $entityProperty) {
		if ($entityProperty instanceof N2nLocaleEntityProperty) return CompatibilityLevel::COMMON;
	
		return CompatibilityLevel::NOT_COMPATIBLE;
	}
}
