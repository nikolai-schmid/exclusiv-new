<?php
namespace n2n\core;

class TypeLoaderErrorException extends \ErrorException {
	private $typeName;
	
	public function __construct($typeName, $message, $code, $severity, $errFilePath = null, $errLineNo = null, 
			$previous = null) {
		parent::__construct((string) $message, $code, $severity, $errFilePath, $errLineNo, $previous);
		
		$this->typeName = $typeName;
	}
	
	public function getTypeName(): string {
		return $this->typeName;
	}
}