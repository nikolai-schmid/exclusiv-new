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
namespace rocket\spec\ei\manage\draft\stmt;

use rocket\spec\ei\EiFieldPath;
use n2n\io\IoUtils;
use rocket\spec\ei\EiThing;

class DraftMetaInfo {
	const TABLE_PREFIX = 'rocket_draft_';
	
	const COLUMN_ID = 'id';
	const COLUMN_LAST_MOD = 'last_mod';
	const COLUMN_TYPE = 'type';
	const COLUMN_USER_ID = 'user_id';
	const COLUMN_ENTIY_OBJ_ID = 'entity_obj_id';
	
	const DRAFT_COLUMN_PREFIX = 'd_';
	const DRAFT_COLUMN_PART_SEPARATOR = '_';
	
	public static function buildTableName(EiThing $eiThing, EiFieldPath $eiFieldPathExt = null) {
		$ids = array();
		do {
			$id = $eiThing->getId();
			if (null === $id) {
				throw new \InvalidArgumentException('EiThing has no id: ' . $eiThing);
			}
			
			$ids[] = IoUtils::replaceStrictSpecialChars($id);
		} while (null !== ($eiThing = $eiThing->getMaskedEiThing()));
		
		$ids = array_reverse($ids);
		$tableName = self::TABLE_PREFIX . implode(self::DRAFT_COLUMN_PART_SEPARATOR, $ids);
		
		if ($eiFieldPathExt !== null) {
			return self::buildFieldTableName($tableName, $eiFieldPathExt->toDbColumnName());
		}
		
		return $tableName;
	}
	
	public static function buildFieldTableName(string $draftTableName, EiFieldPath $eiFieldPath) {
 		return $draftTableName . self::DRAFT_COLUMN_PART_SEPARATOR . $eiFieldPath->toDbColumnName();
 	}
	
	public static function buildDraftColumnName(EiFieldPath $eiFieldPath) {
		return self::DRAFT_COLUMN_PREFIX . $eiFieldPath->toDbColumnName();
	}
}
