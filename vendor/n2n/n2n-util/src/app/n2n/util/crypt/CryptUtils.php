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

class CryptUtils {
	const MODE_ECB = MCRYPT_MODE_ECB;
	const MODE_CBC = MCRYPT_MODE_CBC;
	const MODE_CFB = MCRYPT_MODE_CFB;
	const MODE_OFB = MCRYPT_MODE_OFB;
	const MODE_NOFB = MCRYPT_MODE_NOFB;
	const MODE_STREAM = MCRYPT_MODE_STREAM;
	
	public static function mcryptGetKeySize($algorithm, $mode) {
		$res = @mcrypt_get_key_size($algorithm, $mode);
		if ($res === false && $err = error_get_last()) {
			throw new GetKeySizeFailedException($err['message']);
		}
		return $res;
	}
	
	public static function mcryptGetIvSize($algorithm, $mode) {
		$res = @mcrypt_get_iv_size($algorithm, $mode);
		if ($res === false && $err = error_get_last()) {
			throw new GetIvSizeFailedException($err['message']);
		}
		return $res;
	}
	
	/**
	 * 
	 * Method that wraps the internal PHP Function mcrypt_create_iv
	 * if no Source is given there is a fallback in case of unaccessible sources
	 * 
	 * The Order is the Following
	 * 
	 * 		1. MCRYPT_DEV_RANDOM
	 * 		2. MCRYPT_DEV_URANDOM
	 * 		3. MCRYPT_RAND
	 * 
	 * @param int $size
	 * @param int $source
	 * @throws CreateIvFailedException
	 * @return unknown
	 */
	public static function mcryptCreateIv($size, $source = null) {
		$res = @mcrypt_create_iv($size, $source);
		if ($res === false && $err = error_get_last()) {
			if (null == $source) {
				$res = @mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
				if ($res === false) {
					$res = @mcrypt_create_iv($size, MCRYPT_RAND);
					if ($res === false && $err = error_get_last()) {
						throw new CreateIvFailedException($err['message']);
					}
				}
			} else {
				throw new CreateIvFailedException($err['message']);
			}
		}
		return $res;
	}
	
	public static function mcryptEncrypt($algorithm, $key, $data, $mode, $iv) {
		$res = @mcrypt_encrypt($algorithm, $key, $data, $mode, $iv);
		if ($res === false && $err = error_get_last()) {
			throw new EncryptionFailedException($err['message']);
		}
		return $res;
	}
	
	public static function mcryptDecrypt($algorithm, $key, $data, $mode, $iv) {
		$res = @mcrypt_decrypt($algorithm, $key, $data, $mode, $iv);
		if ($res === false && $err = error_get_last()) {
			throw new DecryptionFailedException($err['message']);
		}
		return $res;
	}
}
