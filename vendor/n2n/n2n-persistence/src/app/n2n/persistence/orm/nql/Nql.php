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

class Nql {
	const GROUP_START = '(';
	const GROUP_END = ')';
	const EXPRESSION_SEPERATOR = ',';
	const PLACHOLDER_PREFIX = ':';
	const QUOTATION_MARK = '"';

	const KEYWORD_SELECT = 'SELECT';
	const KEYWORD_FROM = 'FROM';
	const KEYWORD_WHERE = 'WHERE';
	const KEYWORD_GROUP = 'GROUP';
	const KEYWORD_HAVING = 'HAVING';
	const KEYWORD_ORDER = 'ORDER';
	const KEYWORD_BY = 'BY';
	const KEYWORD_ALIAS = 'AS';
	const KEYWORD_JOIN = 'JOIN';
	const KEYWORD_ON = 'ON';
	const KEYWORD_FETCH = 'FETCH';
	const KEYWORD_AND = 'AND';
	const KEYWORD_OR = 'OR';
	const KEYWORD_DISTINCT = 'DISTINCT';
	const KEYWORD_LIMIT = 'LIMIT';
	const KEYWORD_NOT = 'NOT';
	const KEYWORD_EXISTS = 'EXISTS';
	
	public static function getNoticeableKeyWords() {
		return array(self::KEYWORD_SELECT, self::KEYWORD_FROM, self::KEYWORD_WHERE, 
				self::KEYWORD_GROUP, self::KEYWORD_HAVING, self::KEYWORD_ORDER, self::KEYWORD_LIMIT);
	}
}
