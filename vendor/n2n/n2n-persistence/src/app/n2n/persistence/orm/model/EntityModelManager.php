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
namespace n2n\persistence\orm\model;

use n2n\persistence\orm\proxy\EntityProxy;
use n2n\persistence\orm\OrmConfigurationException;
use n2n\persistence\orm\model\EntityModel;
use n2n\reflection\ReflectionContext;
use n2n\persistence\orm\OrmErrorException;
use n2n\reflection\ReflectionUtils;
use n2n\web\dispatch\model\ModelInitializationException;
use n2n\persistence\orm\annotation\AnnoMappedSuperclass;
// @todo create ReflectionClass later because fatal error if class has errors
class EntityModelManager {
	private $entityClasses = array();
	private $entityModelFactory;
	private $entityModels = array();

	public function __construct(array $entityClassNames, EntityModelFactory $entityModelFactory) {
		foreach ($entityClassNames as $entityClassName) {
			$entityClass = ReflectionUtils::createReflectionClass($entityClassName);
			$this->validateEntityClass($entityClass);
			$this->entityClasses[$entityClass->getName()] = $entityClass;
		}
		
		$this->entityModelFactory = $entityModelFactory;
	}
	/**
	 * @return \ReflectionClass[]
	 */
	public function getEntityClasses() {
		return $this->entityClasses;
	}
	/**
	 * @param \ReflectionClass $class
	 * @return EntityModel
	 */
	public function getEntityModelByClass(\ReflectionClass $class) {
		$className = $class->getName();
		if (isset($this->entityModels[$className])) {
			return $this->entityModels[$className];
		}
		
		$entityModel = null;
		foreach ($this->resolveEntityClasses($class) as $class) {
			$className = $class->getName();
			
			if (isset($this->entityModels[$className])) {
				$entityModel = $this->entityModels[$className];
			} else {
				try {
					$entityModel = $this->entityModels[$className]
							= $this->entityModelFactory->create($class, $entityModel);
				} catch (ModelInitializationException $e) {
					throw new OrmConfigurationException('Invalid entity registered: ' . $class->getName(), 0, $e);
				}
				$this->entityModelFactory->cleanUp($this);
			}
		}
		
		$this->initSubEntityModels($entityModel);
		
		return $entityModel;
	}
	
	private function initSubEntityModels(EntityModel $entityModel) {
		$class = $entityModel->getClass();
		foreach ($this->entityClasses as $entityClass) {
			// @todo ReflectionClass::isSubclassOf(): Internal error: Failed to retrieve the reflection object
			$entityClass = new \ReflectionClass($entityClass->getName());
			if (!$entityClass->isSubclassOf($class)) continue;
			
			$this->getEntityModelByClass($entityClass);
		}
	}
		
	public function getEntityModelByEntityObj($entity) {		
		$class = new \ReflectionClass($entity);
		if ($entity instanceof EntityProxy) {
			$class = $class->getParentClass();
		}
		return $this->getEntityModelByClass($class);
	}
	
	private function validateEntityClass(\ReflectionClass $class) {
		if (!$class->isInterface() && !$class->isTrait()) return;
		
		throw new \InvalidArgumentException('Class ' . $class->getName()
				. ' does not implement n2n\persistence\orm\Entity');
	}
	
	public function isRegistered(\ReflectionClass $class) {
		return in_array($class, $this->entityClasses);
	}
	
	private function validateRegistration(\ReflectionClass $class) {
		if (!$this->isRegistered($class)) {
			throw new OrmConfigurationException('Class not registered as entity: ' . $class->getName());
		}
		
		$annoMappedSuperClass = ReflectionContext::getAnnotationSet($class)
				->getClassAnnotation(AnnoMappedSuperclass::class);
		if ($annoMappedSuperClass !== null) {
			throw OrmErrorException::create('Class can not be registered as Entity and be'
					. ' annotated as MappedSuperClass at the same time:' . $class->getName(),
					array($annoMappedSuperClass));
		}
	}
	
	private function isEntityClass(\ReflectionClass $class) {
		if (!$this->isRegistered($class)) return false;
			
		$this->validateEntityClass($class);
		
		return true;
	}
	
	private function resolveEntityClasses(\ReflectionClass $class) {
		$this->validateRegistration($class);
		
		$classes = array($class);
		
		while (false !== ($class = $class->getParentClass())) {
			if ($this->isEntityClass($class)) {
				$classes[] = $class;
			}
		}
		
		return array_reverse($classes);
	}	
}
