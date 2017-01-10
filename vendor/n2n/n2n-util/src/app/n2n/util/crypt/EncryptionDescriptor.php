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
namespace n2n\util\crypt;

class EncryptionDescriptor {
	
	const ALGORITHM_3DES = MCRYPT_3DES;
	const ALGORITHM_ARCFOUR_IV = MCRYPT_ARCFOUR_IV; // (libmcrypt > 2.4.x only)
	const ALGORITHM_ARCFOUR = MCRYPT_ARCFOUR; // (libmcrypt > 2.4.x only)
	const ALGORITHM_BLOWFISH = MCRYPT_BLOWFISH;
	const ALGORITHM_CAST_128 = MCRYPT_CAST_128;
	const ALGORITHM_CAST_256 = MCRYPT_CAST_256;
	const ALGORITHM_CRYPT = MCRYPT_CRYPT;
	const ALGORITHM_DES = MCRYPT_DES;
	const ALGORITHM_GOST = MCRYPT_GOST;
	const ALGORITHM_IDEA = MCRYPT_IDEA; // (non-free)
	const ALGORITHM_LOKI97 = MCRYPT_LOKI97; // (libmcrypt > 2.4.x only)
	const ALGORITHM_MARS = MCRYPT_MARS; // (libmcrypt > 2.4.x only, non-free)
	const ALGORITHM_PANAMA = MCRYPT_PANAMA; // (libmcrypt > 2.4.x only)
	const ALGORITHM_RIJNDAEL_128 = MCRYPT_RIJNDAEL_128; // (libmcrypt > 2.4.x only)
	const ALGORITHM_RIJNDAEL_192 = MCRYPT_RIJNDAEL_192; // (libmcrypt > 2.4.x only)
	const ALGORITHM_RIJNDAEL_256 = MCRYPT_RIJNDAEL_256; // (libmcrypt > 2.4.x only)
	const ALGORITHM_RC2 = MCRYPT_RC2;
	const ALGORITHM_RC6 = MCRYPT_RC6; // (libmcrypt > 2.4.x only)
	const ALGORITHM_SAFER64 = MCRYPT_SAFER64;
	const ALGORITHM_SAFER128 = MCRYPT_SAFER128;
	const ALGORITHM_SAFERPLUS = MCRYPT_SAFERPLUS; // (libmcrypt > 2.4.x only)
	const ALGORITHM_SERPENT = MCRYPT_SERPENT; // (libmcrypt > 2.4.x only)
	const ALGORITHM_THREEWAY = MCRYPT_THREEWAY;
	const ALGORITHM_TRIPLEDES = MCRYPT_TRIPLEDES; // (libmcrypt > 2.4.x only)
	const ALGORITHM_TWOFISH = MCRYPT_TWOFISH; // (for older mcrypt 2.x versions, or mcrypt > 2.4.x )
	const ALGORITHM_WAKE = MCRYPT_WAKE; // (libmcrypt > 2.4.x only)
	const ALGORITHM_XTEA = MCRYPT_XTEA; // (libmcrypt > 2.4.x only)

	const DEFAULT_CRYPT_ALGORITHM = MCRYPT_RIJNDAEL_256;
	const DEFAULT_CRYPT_MODE = MCRYPT_MODE_CBC;
	/**
	* the mcrypt algorithm
	* initialised with the AES algorithm aka MCRYPT_RIJNDAEL
	* if you need a faster algorithm it is supposed to use MCRYPT_RIJNDAEL_128
	* @var string
	*/
	private $algorithm;
	/**
	 * the mcrypt mode.
	 * initialised with a quite safe mode
	 * if you need a faster mode use MCRYPT_MODE_ECB (enorm loosings in security if the text exceeds the algorithms block size)
	 * @var string
	 */
	private $mode;
	
	public function __construct($algorithm = self::DEFAULT_CRYPT_ALGORITHM, $mode = self::DEFAULT_CRYPT_MODE) {
		$this->setAlgorithm($algorithm);
		$this->setMode($mode);
	}
	
	public function getAlgorithm() {
		return $this->algorithm;
	}
	
	public function setAlgorithm($algorithm) {
		if (!self::isAlgorithmAvailable($algorithm)) {
			throw new AlgorithmNotAvailableException('n2n_error_crypt_algorithm_is_not_available');
		}
		$this->algorithm = $algorithm;
	}
	
	public function getMode() {
		return $this->mode;
	}
	
	public function setMode($mode) {
		if (!self::isModeAvailable($mode)) {
			throw new ModeNotAvailableException('n2n_error_crypt_mode_is_not_available');
		}
		$this->mode = $mode;
	}
	
	public function generateKey() {
		if(!($length = $this->getKeySize())) return null;
		return CryptUtils::mcryptCreateIv($length);
	}
	
	public function generateIv() {
		if(!($length = $this->getIvSize())) return null;
		return CryptUtils::mcryptCreateIv($length);
	}
	
	public function getIvSize() {
		return CryptUtils::mcryptGetIvSize($this->algorithm, $this->mode);
	}
	
	public function getKeySize() {
		return CryptUtils::mcryptGetKeySize($this->algorithm, $this->mode);
	}
	
	public static function isAlgorithmAvailable($algorithm) {
		return in_array($algorithm, self::getAvailableAlgorithms());
	}
	
	public static function getAvailableAlgorithms() {
		return mcrypt_list_algorithms();
	}
	
	public static function isModeAvailable($mode) {
		return in_array($mode, self::getAvailableModes());
	}
	
	public static function getAvailableModes() {
		return mcrypt_list_modes();
	}
}
