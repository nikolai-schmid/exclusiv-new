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
namespace n2n\persistence\orm\nql;

use n2n\persistence\orm\criteria\item\CrIt;
use n2n\persistence\orm\criteria\compare\ComparatorCriteria;
use n2n\persistence\orm\criteria\item\CriteriaConstant;
use n2n\util\StringUtils;

class ExpressionParser {
	
	private $parsingState;
	private $propertyExpressionParser;
	
	public function __construct(ParsingState $parsingState) {
		$this->parsingState = $parsingState;
		$this->propertyExpressionParser = new PropertyExpressionParser();
	}
	
	public function parse($expression) {
		$params = $this->parsingState->getParams();
	
		$expression = $this->clean($expression);
		
		if (NqlUtils::isPlaceholder($expression) 
				&& (array_key_exists(mb_substr($expression, 1), $params) || array_key_exists($expression, $params))) {
			if (array_key_exists($expression, $params)) {
				return new CriteriaConstant($params[$expression]);
			}
			
			return new CriteriaConstant($params[mb_substr($expression, 1)]);
		}

		$this->propertyExpressionParser->parse($expression);
		if (null !== ($property = $this->propertyExpressionParser->getProperty())) {
			return $property;
		}
		
		
		if (NqlUtils::isCriteria($expression)) {
			$parser = new CriteriaParser($this->parsingState, new ComparatorCriteria());
			$parser->parse($expression, $params);
			return $parser->getCriteria();
		}
		
		return CrIt::pf($expression);
	}
	
	private function clean($expression) {
		$expression = trim($expression);
		
		while (StringUtils::startsWith(Nql::GROUP_START, $expression)
				&& StringUtils::endsWith(Nql::GROUP_END, $expression)) {
			$expression = mb_substr($expression, 1, -1);
		};

		return $expression;
	}
}
