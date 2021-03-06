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
namespace n2n\impl\persistence\meta\mssql\management;

use n2n\impl\persistence\meta\mssql\MssqlIntegerColumn;
use n2n\impl\persistence\meta\mssql\MssqlIndexStatementStringBuilder;
use n2n\impl\persistence\meta\mssql\MssqlColumnStatementStringBuilder;
use n2n\persistence\meta\structure\common\AlterMetaEntityRequest;
use n2n\persistence\meta\structure\Table;
use n2n\persistence\meta\structure\View;
use n2n\persistence\meta\structure\common\ChangeRequestAdapter;
use n2n\impl\persistence\meta\mssql\MssqlMetaEntityBuilder;
use n2n\persistence\meta\structure\InvalidColumnAttributesException;
use n2n\persistence\Pdo;

class MssqlAlterMetaEntityRequest extends ChangeRequestAdapter implements AlterMetaEntityRequest{
	
	public function execute(Pdo $dbh) {
		$columnStatementStringBuilder = new MssqlColumnStatementStringBuilder($dbh);
		$indexStatementStringBuilder = new MssqlIndexStatementStringBuilder($dbh);
		$metaEntityBuilder = new MssqlMetaEntityBuilder($dbh, $this->getMetaEntity()->getDatabase());
		
		if ($this->getMetaEntity() instanceof View) {
			$dbh->exec('ALTER VIEW ' . $dbh->quoteField($this->getMetaEntity()->getName()) . ' AS ' . $this->getMetaEntity()->getQuery());
			return;
		}
		
		if ($this->getMetaEntity() instanceof Table) {
			//columns to Add
			$columns = $this->getMetaEntity()->getColumns();
			$persistedTable =  $metaEntityBuilder->createMetaEntity($this->getMetaEntity()->getName());
			$persistedColumns = $persistedTable->getColumns();
			
			foreach ($columns as $column) {
				if (!(isset($persistedColumns[$column->getName()]))) {
					$dbh->exec('ALTER TABLE ' . $dbh->quoteField($this->getMetaEntity()->getName()) . ' ADD ' . $columnStatementStringBuilder->generateStatementString($column));
				} elseif (isset($persistedColumns[$column->getName()]) && (!($column->equals($persistedColumns[$column->getName()])))) {
					//Identity not allowed in ALTER Statement
					if ($column instanceof MssqlIntegerColumn && $column->isGeneratedIdentifier()) {
						throw new InvalidColumnAttributesException(SysTextUtils::get('n2n_persistance_meta_mssql_altering_generated_identifiers_not_possible', array('table' => $this->getMetaEntity()->getName(), 'column' => $column->getName())));
					} else {
						$dbh->exec('ALTER TABLE ' . $dbh->quoteField($this->getMetaEntity()->getName()) . ' ALTER COLUMN ' . $columnStatementStringBuilder->generateStatementString($column));
					} 
				}
			}
			
			foreach ($persistedColumns as $persistedColumn) {
				if (!(isset($columns[$persistedColumn->getName()]))) {
					$dbh->exec('ALTER TABLE ' . $dbh->quoteField($this->getMetaEntity()->getName()) . ' DROP COLUMN ' . $dbh->quoteField($persistedColumn->getName()));
				}
			}
			
			$indexes = $this->getMetaEntity()->getIndexes();
			$persistedIndexes = $persistedTable->getIndexes();
			
			foreach ($indexes as $index) {
				if (!isset($persistedIndexes[$index->getName()])) {
					$dbh->exec($indexStatementStringBuilder->generateCreateStatementStringForIndex($index));
				}
			}
			
			foreach ($persistedIndexes as $persistedIndex) {
				if (!isset($indexes[$persistedIndex->getName()]) ) {
					$dbh->exec($indexStatementStringBuilder->generateDropStatementStringForIndex($persistedIndex));
				}
			}
		}
	}
}
