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
namespace rocket\spec\ei\component\command\impl\common\controller;

use rocket\spec\ei\manage\control\EntryNavPoint;
use n2n\l10n\N2nLocale;
use n2n\util\uri\Path;
use rocket\spec\ei\component\command\EiCommand;

class PathUtils {

	public static function createPathExtFromEntryNavPoint(EiCommand $eiCommand, EntryNavPoint $entryNavPoint): Path {
		$idRep = null;
		if (null !== ($id = $entryNavPoint->getLiveId())) {
			$idRep = $eiCommand->getEiEngine()->getEiSpec()->idRepToId($id);
		}
		$draftId = $entryNavPoint->getDraftId();
		$previewType = $entryNavPoint->getPreviewType();
	
		if (isset($draftId)) {
			return self::createDraftPathExt($idRep, $draftId, $previewType);
		}
	
		return self::createPathExt($idRep, $previewType);
	}
	
	
	public static function createPathExt($idRep, N2nLocale $translationN2nLocale = null, $previewType = null) {
		$pathParts = array('live');
		if (isset($previewType)) {
			$pathParts[] = 'preview';
			$pathParts[] = (string) $previewType;
		}
		
		$pathParts[] = $idRep;
		
		if (isset($translationN2nLocale)) {
			$pathParts[] = $translationN2nLocale->toHttpId();
		}
		
		return new Path($pathParts);
	}
	
	public static function createDraftPathExt($idRep, $draftId = null, $previewType = null) {
		$pathParts = array();
		if (isset($previewType)) {
			$pathParts[] = 'draftpreview';
			$pathParts[] = (string) $previewType;
		} else {
			$pathParts[] = 'draft';
		}
		
		$pathParts[] = $idRep;
		$pathParts[] = $draftId;
		
		if (isset($translationN2nLocale)) {
			$pathParts[] = $translationN2nLocale->toHttpId();
		}
		
		return new Path($pathParts);
	}
	
// 	public static function createDetailPathExtFromEntryNavPoint($commandId, EntryNavPoint $entryNavPoint) {
// 		$objectId = $entryNavPoint->getId();
// 		$draftId = $entryNavPoint->getDraftId();
// 		$translationN2nLocale = $entryNavPoint->getTranslationN2nLocale();
// 		$previewType = $entryNavPoint->getPreviewType();
		
// 		if (isset($draftId)) {
// 			return self::createDraftDetailPathExt($commandId, $objectId, $draftId, $translationN2nLocale, $previewType);
// 		}
		
// 		return self::createDetailPathExt($commandId, $objectId, $translationN2nLocale, $previewType);
// 	}
	
// 	public static function createDetailPathExtFromEiSelection($commandId, EiSelection $eiSelection, $previewType) {
// 		$objectId = $eiSelection->getId();
// 		$draftId = $eiSelection->getDraftId();
// 		$translationN2nLocale = $eiSelection->getTranslationN2nLocale();
	
// 		if (isset($draftId)) {
// 			return self::createDraftDetailPathExt($commandId, $objectId, $draftId, $translationN2nLocale, $previewType);
// 		}
	
// 		return self::createDetailPathExt($commandId, $objectId, $translationN2nLocale, $previewType);
// 	}
	
// 	public static function createDetailPathExtFromEiState($commandId, EiState $eiState, $includeDraft = false, 
// 			$includeTranslation = false, $includePreview = false) {
// 		$eiSelection = $eiState->getEiSelection();
// 		$objectId = $eiSelection->getId();
// 		$translationN2nLocale = null;
// 		if ($eiSelection->hasTranslation() && $includeTranslation) {
// 			$translationN2nLocale = $eiSelection->getTranslationN2nLocale();
// 		}
		
// 		$previewType = $eiState->getPreviewType();
		
// 		if ($eiSelection->isDraft() && $includeDraft) {
// 			return self::createDraftDetailPathExt($commandId, $objectId, $eiSelection->getDraft()->getId(), $translationN2nLocale, $previewType);
// 		} else {
// 			return self::createDetailPathExt($commandId, $objectId, $translationN2nLocale, $previewType);
// 		}
// 	}
}
