// <?php
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
// namespace rocket\spec\ei;

// use rocket\spec\config\SpecManager;
// use n2n\persistence\orm\EntityManager;
// use n2n\persistence\orm\model\EntityModelManager;
// use rocket\spec\ei\component\field\DraftableEiField;

// use n2n\persistence\orm\store\PersistenceActionQueueImpl;
// use n2n\persistence\orm\store\RemoveActionQueueImpl;
// use n2n\persistence\orm\model\EntityModel;
// use rocket\spec\ei\manage\draft\DraftManager;

// class ScriptDraftManager implements DraftManager {
// 	private $scriptManager;
// 	private $draftModels = array();
// 	private $entityModelManager;
// 	private $translationManager;
// 	private $drafts = array();
	
// 	public function __construct(SpecManager $scriptManager, EntityManager $em) {
// 		$this->scriptManager = $scriptManager;
// 		$this->em = $em;
// 		// @todo fixen
// 		$this->entityModelManager = EntityModelManager::getInstance();
// 	}
	
	
// 	public function getOrCreateDraftModel(EntityModel $entityModel) {
// 		$class = $entityModel->getEntityModel()->getTopEntityModel();
// 		if (isset($this->draftModels[$class->getName()])) {
// 			return $this->draftModels[$class->getName()];
// 		}
		
// 		$eiSpec = $this->scriptManager->getEiSpecByClass($class);
		
// 		$this->draftModels[$class->getName()] = $draftModel = new DraftModel($em);
		
// 		foreach ($eiSpec->getEiFieldCollection()->combineAll() as $field) {
// 			if ($field instanceof DraftableEiField && $field->isDraftable()) {
// 				$draftModel->addDraftable($field);
// 			}
// 		}
		
// 		foreach ($eiSpec->getEiModificatorCollection()->combineAll() as $constraint) {
// 			$constraint->setupDraftModel($draftModel, $this->drafManager !== null);
// 		}

// 		return $draftModel;
// 	}
	
// 	public function containsManagedDraftId(Entity $baseEntity, $id) {
		
// 	}
	
// 	public function getManagedDraftById(Entity $baseEntity, $id) {
		
// 	}
	
// 	public function findDraftById(Entity $baseEntity, $id) {
// 		$objectHash = spl_object_hash($baseEntity);
// 		if (isset($this->drafts[$objectHash][$id])) {
// 			return $this->drafts[$objectHash][$id];
// 		}
		
// 		$entityModel = $this->entityModelManager->getEntityModelByObject($baseEntity);
// 		$translationModel = $this->getOrCreateDraftModel($entityModel);
		
// 		return $this->drafts[$objectHash][$n2nLocale->getId()] 
// 				= $translationModel->getOrCreateTranslationByN2nLocaleAndElementId($n2nLocale, $elementId, $baseEntity);
// 	}
	
// 	public function findLatestDraft(Entity $baseEntity) {
		
// 	}
	
// 	public function findDrafts(Entity $baseEntity) {
		
// 	}
	
// // 	public function determineElementId(Entity $baseEntity) {
// // 		if ($this->draftManager === null) {
// // 			$entityModel = $this->entityModelManager->getEntityModelByObject($entity);
// // 			return OrmUtils::extractId($baseEntity, $entityModel);
// // 		}
		
// // 		return $this->draftManager->getDraftByDraftedEntity($baseEntity)->getId();
// // 	}
	
// 	public function saveDraft(Draft $draft) {
// 		$this->em->getPersistenceContext()->addBufferedActionQueue(
// 				new TranslationPersistingActionQueue(new PersistenceActionQueueImpl(
// 						$this->em->getPersistenceContext(), false), $this));
// 	}
	
// 	public function removeDraft(Draft $draft) {
// 		$persistingActionQueue = new PersistenceActionQueueImpl($this->em->getPersistenceContext(), false);
// 		$removingActionQueue = new RemoveActionQueueImpl($this->em->getPersistenceContext(), $persistingActionQueue);
// 		$this->em->getPersistenceContext()->addBufferedActionQueue(new TranslationRemovingActionQueue($removeActionQueue, $this));
// 	}
// }
