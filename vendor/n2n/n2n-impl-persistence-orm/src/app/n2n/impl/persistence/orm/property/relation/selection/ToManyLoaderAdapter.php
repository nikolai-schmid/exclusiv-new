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
namespace n2n\impl\persistence\orm\property\relation\selection;

use n2n\persistence\orm\query\from\MetaTreePoint;
use n2n\persistence\meta\data\SelectStatementBuilder;
use n2n\persistence\orm\query\from\TreePath;
use n2n\persistence\orm\CorruptedDataException;
use n2n\persistence\orm\store\SimpleLoaderUtils;

abstract class ToManyLoaderAdapter implements ToManyLoader {
	private $orderDirectives = array();
	
	public function setOrderDirectives(array $orderDirectives) {
		$this->orderDirectives = $orderDirectives;
	}
	
	protected function applyOrderDirectives(SelectStatementBuilder $selectBuilder, MetaTreePoint $metaTreePoint) {
		foreach ($this->orderDirectives as $orderDirective) {
			$queryItem = $metaTreePoint->requestPropertyRepresentableQueryItem(new TreePath($orderDirective->propertyNames));
			$selectBuilder->addOrderBy($queryItem, $orderDirective->direction);
		}	
	}
	
	protected function fetchArray(SimpleLoaderUtils $utils) {
		$entityObjs = $utils->createQuery()->fetchArray();
		
		foreach ($entityObjs as $entityObj) {
			if ($entityObj === null) {
				throw new CorruptedDataException('Database contains entries of entity '
						. $utils->entityModel->getClass()->getName() . ' with id null.');
			}
		}
		
		return $entityObjs;
	}
}
