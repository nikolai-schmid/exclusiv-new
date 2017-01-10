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
namespace n2n\io;

use n2n\io\IoException;
use n2n\io\fs\FileResourceStream;
use n2n\io\fs\FileOperationException;

class IoUtils {
	
// 	public static function hasFsSpecialChars($string) {
// 		return false === strpbrk($string, '<>:"/\|?*') && ctype_print($string)
// 				&& !StringUtils::isEmpty($string);
// 	}
	/**
 	 * strips and converts characters of a string so it can be enbedded into urls
 	 * or file paths whitout any encoding nessesary 
 	 *
 	 * @param string $string string to be cleaned
 	 * @return string
 	 */
	public static function stripSpecialChars($string, $pretty = true) {
		$string = trim($string);
		
		$unwanted = array('ä', 'à', 'â', 'ç', 'é', 'è', 'ê', 'î', 'ö', 'ß', 'ü',
				'Ä', 'À', 'Â', 'É', 'È', 'Ê', 'Î', 'Ö','Ü');
		$wanted = array('ae', 'a', 'a', 'c', 'e', 'e', 'e', 'i', 'oe', 'ss', 'ue',
				'Ae', 'A', 'A', 'E', 'E', 'E', 'I', 'Oe','Ue');
		$string = str_replace($unwanted, $wanted, $string);
	
		$string = preg_replace('/\s/s', '-', $string);
		$string = preg_replace('/[^0-9A-Za-z\\._-]/', '', $string);
		
		if ($pretty) {
			$string = preg_replace('/-{2,}/', '-', $string);
			$string = preg_replace('/-?\\.-?/', '.', $string);
			$string = preg_replace('/-?_-?/', '_', $string);
			$string = preg_replace('/\\.{2,}/', '.', $string);
			$string = preg_replace('/_{2,}/', '_', $string);
			$string = preg_replace('/[-,_\\.]+$/', '', $string);
			$string = preg_replace('/^[-,_\\.]+/', '', $string);
		}
		
		if ($string == '.') {
			$string = '';
		}
	
		return $string;
	}
 	/**
 	 * 
 	 * @param string $string
 	 * @return bool
 	 */
 	public static function hasSpecialChars($string) {
 		return preg_match('/[^0-9A-Za-z\\._-]/', $string) || $string == '.' || $string == '..';
 	}
 	
 	/**
 	 * @param string $string
 	 * @return boolean
 	 */
 	public static function hasStrictSpecialChars($string) {
 		return (boolean) preg_match('/\W/', $string);
 	}
 	
 	/**
 	 * @param string $string
 	 * @return string
 	 */
 	public static function replaceStrictSpecialChars($string) {
 		return preg_replace('/\W/', '_', $string);
 	}
	/**
	 * 
	 * @param string $oldPath
	 * @param string $newPath
	 * @throws IoException
	 */
	public static function rename($oldPath, $newPath) {
		if (!@rename($oldPath, $newPath)) {
			$err = error_get_last();
			throw new FileOperationException($err['message']);
		}
	}
	
	/**
	 * 
	 * @param string $path
	 * @param string $permission
	 * @throws IoException
	 */
	public static function mkdirs($path, $permission) {
		if (@mkdir($path, octdec($permission), true)) return true;

		$err = error_get_last();
		throw new FileOperationException('Mkdir of \'' . $path . '\' failed. Reason: ' . $err['message']);
	}
	
	public static function rmdir($path) {
		if (@rmdir($path)) return true;
		
		$err = error_get_last();
		throw new FileOperationException('Rmdir of \'' . $path . '\' failed. Reason: ' . $err['message']);
	}
	/**
	 * 
	 * @param string $path
	 * @throws IoException
	 */
	public static function rmdirs($path) {
		if (is_dir($path)) {
			if (!($handle = @opendir($path))) {
				$err = error_get_last();
				throw new IoException($err['message']);
			}
			
			while (false !== ($fileName = readdir($handle))) {
				if ($fileName == '.' || $fileName == '..') continue;
					self::rmdirs($path . DIRECTORY_SEPARATOR . $fileName);
			}
		
			closedir($handle);
			// @todo check requirements
			clearstatcache();		
			IoUtils::rmdir($path);
		} else if (is_file($path)) {
			IoUtils::chmod($path, '0777');
			IoUtils::unlink($path);
		}
	}
	
	/**
	 * @param unknown $path
	 * @param unknown $context
	 * @throws IoException
	 * @return boolean
	 */
	public static function opendir($path, $context = null) {
		if (false !== ($h = @opendir($path, $context))) {
			return $h;
		}
	
		$err = error_get_last();
		throw new IoException($err['message']);
	}
	
	/**
	 * 
	 * @param string $pattern
	 * @param string $flags
	 * @return array
	 */
	public static function glob($pattern, $flags = 0) {
		$paths = glob($pattern, $flags);

		// Return array on false due to different behaviour on different systems
		if (!is_array($paths)) {
			return array();
		}
		
		return $paths;
	}
	/**
	 * 
	 * @param string $path
	 * @param string $filePermission
	 * @throws IoException
	 * @deprecated use IoUtils::touch() and IoUtils::chmod()
	 */
	public static function createFile($path, $filePermission = null) {
		IoUtils::touch($path);
		if (isset($filePermission)) {
			IoUtils::chmod($path, $filePermission);
		}
	}
	/**
	 * 
	 * @param string $path
	 * @param string $contents
	 * @throws IoException
	 */
	public static function putContents($path, $contents, $flags = null, resource $context = null) {
		if (is_numeric(@file_put_contents((string) $path, $contents, $flags, $context))) return;

		$err = error_get_last();
		throw new FileOperationException($err['message']);
	}
	/**
	 * 
	 * @param string $path
	 * @return string
	 * @throws IoException
	 */
	public static function getContents($path) {
		$contents = @file_get_contents((string) $path);

		if ($contents === false) {
			$err = error_get_last();
			throw new FileOperationException($err['message']);
		}
		
		return $contents;
	}
	/**
	 * 
	 * @param string $path
	 * @return string
	 * @throws IoException
	 */
	public static function file($path) {
		$contents = @file((string) $path);

		if ($contents === false) {
			$err = error_get_last();
			throw new FileOperationException($err['message']);
		}
		
		return $contents;
	}
	/**
	 * 
	 * @param string $path
	 * @param string $targetPath
	 * @throws IoException
	 */
	public static function copy($path, $targetPath, $context = null) {
		if ($context === null) {
			if (@copy($path, $targetPath)) return;
		} else {
			if (@copy($path, $targetPath, $context)) return;
		}

		$err = error_get_last();
		// @todo check what goes wrong when you try to copy an empty file from a zip archive
		if (!sizeof($err)) return;
		throw new FileOperationException($err['message']);
	}
	/**
	 * 
	 * @param string $path
	 * @param string $filePermission octal string
	 * @throws IoException
	 */
	public static function chmod($path, $filePermission) {
		if (is_string($filePermission)) {
			$filePermission = octdec($filePermission);
		}
		
		if (@chmod($path, $filePermission)) return;

		$err = error_get_last();
		throw new FileOperationException($err['message']);
	}
	/**
	 * @todo add param time and atime  
	 */
	public static function touch($filename) {
		if (@touch($filename)) return;
			
		$err = error_get_last();
		throw new FileOperationException($err['message']);
	}
	/**
	 * 
	 * @param unknown_type $path
	 * @param unknown_type $mode
	 * @throws IoException
	 * @return resource
	 */
	public static function fopen($path, $mode) {
		$fh = @fopen($path, $mode);
		
		if (!$fh) {
			$err = error_get_last();
			throw new IoException($err['message']);
		}
		
		return $fh;
	}
	
	public static function stat($path) {
		$stat = @stat($path);
		
		if ($stat === false) {
			$err = error_get_last();
			throw new FileOperationException($err['message']);
		}
		
		return $stat;
	}
	
	public static function filesize($path) {
		$size = @filesize($path);
		
		if ($size === false) {
			$err = error_get_last();
			throw new FileOperationException($err['message']);
		}
		
		return $size;
	}
	
	public static function readfile($path) {
		$numBytes = @readfile($path);
		
		if ($numBytes === false) {
			$err = error_get_last();
			throw new FileOperationException($err['message']);
		}
		
		return $numBytes;
	}
	
	public static function fwrite($handle, $string) {
		$num = fwrite($handle, $string);
		if (false === $num) {
			throw new IoResourceException('Could not write to resource: ' . $handle);
		}
		
		return $num;
	}
	
	public static function fread($handle, $length) {
		$str = fread($handle, $length);
		if (false === $str) {
			throw new IoResourceException('Could not read from file');
		}
		
		return $str;
	}
	
	public static function streamGetContents($handle, $maxlength = -1, $offset = -1) {
		$str = @stream_get_contents($handle, $maxlength, $offset);
		if (false === $str) {
			$err = error_get_last();
			throw new IoResourceException($err['message']);
		}
	
		return $str;
	}
	
	public static function createSafeFileStream($filePath) {
		return new FileResourceStream($filePath, 'c+', LOCK_EX);
	}
	/**
	 * 
	 * @return FileResourceStream
	 */
	public static function createSafeFileOutputStream($filePath) {
		return new FileResourceStream($filePath, 'w', LOCK_EX);
	}
	/**
	 * 
	 * @param unknown_type $filePath
	 * @return FileResourceStream
	 */
	public static function createSafeFileInputStream($filePath) {
		return new FileResourceStream($filePath, 'r', LOCK_SH);
	}
	/**
	 * 
	 * @param string $path
	 * @param string $contents
	 * @throws IoException
	 */
	public static function putContentsSafe($path, $contents) {
		$fileOutputStream = self::createSafeFileOutputStream($path);
		$fileOutputStream->write($contents);
		$fileOutputStream->close();
	}
	/**
	 * 
	 * @param string $path
	 * @return string
	 * @throws IoException
	 */
	public static function getContentsSafe($path) {
		$fileReader = self::createSafeFileInputStream($path);
		$contents = $fileReader->read();
		$fileReader->close();
		return $contents;
	}
	/**
	 * 
	 * @param string $path
	 * @return string
	 * @throws IoException
	 */
	public static function filemtime($path) {
		$timestamp = @filemtime($path);

		if ($timestamp === false) {
			$err = error_get_last();
			throw new FileOperationException($err['message']);
		}
		
		return $timestamp;
	}
	/**
	 * 
	 * @param string $path
	 * @throws IoException
	 */
	public static function unlink($path) {
		if (!@unlink($path)) {
			$err = error_get_last();
			throw new FileOperationException($err['message']);
		}
	}	
	/**
	 * 
	 * @param string $iniString
	 * @param string $processSections
	 * @param string $scannerMode
	 * @throws IoException
	 * @return array
	 */
	public static function parseIniString($iniString, $processSections = false, $scannerMode = null) {
		$values = @parse_ini_string($iniString, $processSections, $scannerMode);
		// parse_ini_file can return null if disabled for security reasons
		if ($values === null || $values === false) {
			$err = error_get_last();
			throw new IoException($err['message']);
		}
		return $values;
	}
	/**
	 * 
	 * @param string $path
	 * @param string $processSections
	 * @param string $scannerMode
	 * @throws IoException
	 * @return array
	 */
	public static function parseIniFile($path, $processSections = false, $scannerMode = null) {
		$values = @parse_ini_file($path, $processSections, $scannerMode);
		if($values === false) {
			$err = error_get_last();
			throw new IoException($err['message']);
		}
		return $values;
	}
	
	public static function imageCreateFromPng($filePath) {
		$resource = @imagecreatefrompng($filePath);
		if (!$resource) {
			$err = error_get_last();
			throw new IoException($err['message']);
		}
		
		return $resource;
	}
	
	public static function imageCreateFromGif($filePath) {
		$resource = @imagecreatefromgif($filePath);
		if (!$resource) {
			$err = error_get_last();
			throw new IoException($err['message']);
		}
		
		return $resource;
	}
	
	public static function imageCreateFromJpeg($filePath) {
		$resource = @imagecreatefromjpeg((string) $filePath);
		if (!$resource) {
			$err = error_get_last();
			throw new IoException($err['message']);
		}
		
		return $resource;
	}
	
	public static function imagePng($resource, $filePath = null, $quality = null, $filters = null) {
		if (@imagepng($resource, $filePath, $quality, $filters)) return true;
		
		$err = error_get_last();
		throw new IoException($err['message']);
	}
	
	public static function imageGif($resource, $filePath = null) {
		if (@imagegif($resource, $filePath)) return true;
		
		$err = error_get_last();
		throw new IoException($err['message']);
	}
	
	public static function imageJpeg($resource, $filePath = null, $quality = null) {
		if (@imagejpeg($resource, $filePath, $quality)) return true;
		
		$err = error_get_last();
		throw new IoException($err['message']);
	}
	/**
	 * 
	 * @param unknown_type $filePath
	 * @param unknown_type $operation
	 * @return Flock
	 */
	public static function createFlock($filePath, $operation, $requried = true) {
		try {
			return new Flock(self::fopen($filePath, 'c'), $operation);
		} catch (CouldNotAchieveFlockException $e) {
			if (!$requried) return null;
			throw $e;
		}
	}
	/**
	 * 
	 * @param string $orgPath
	 * @throws PathDoesNotExistException
	 */
	public static function realpath($orgPath, $fileRequired = null) {
		$path = realpath($orgPath);
		
		if ($path === false) {
			throw new InvalidPathException('Path not found:' . $orgPath);
		}
		
		if ($fileRequired === true && !is_file($path)) {
			throw new InvalidPathException('Path points to non file:' . $path);
		}
		
		if ($fileRequired === false && !is_dir($path)) {
			throw new InvalidPathException('Path points to non directory: ' . $path);
		}
		
		return $path;
	}
	
	public static function flock($handle, $operation, &$wouldblock = null) {
		if (!flock($handle, $operation, $wouldblock)) {
			throw new CouldNotAchieveFlockException('Could not achieve flock: ' . $operation);
		}
		return true;
	}
	
	public static function ftruncate($handle, $size) {
		if (!ftruncate($handle, $size)) {
			throw new IoResourceException('Could not truncate.');
		}
	}
	
	public static function fseek($handle, $offset, $whence = SEEK_SET) {
		if (false === fseek($handle, $offset, $whence)) {
			throw new IoResourceException('Could not seek. Offset: ' . $offset);
		}
	}
	
	public static function ftell($handle, $offset, $whence = SEEK_SET) {
		$offset = ftell($handle);
		if (false === $offset) {
			throw new IoResourceException('Could not tell.');
		}
		return $offset;
	}
					
// 	public static function ensureFileIsAccessibleThroughHttp(Managable $file) {
// 		if ($file->getFileManager() instanceof HttpAccessible) return;
// 		throw new FileManagingException(
// 				SysTextUtils::get('n2n_error_io_file_is_not_accessible_through_http',
// 						array('file' => $file->getPath(), 'file_name' => $file->getOriginalName())));
// 	}
	
	public static function getimagesize($filename) {
		$imagesize = @getimagesize($filename);
		if (false === $imagesize) {
			$err = error_get_last();
			throw new IoException('Operation getimagesize of \'' . $filename . '\' failed. Reason: ' . ($err['message'] ?? 'unknown'));
		}
		return $imagesize;
	}
	/**
	 * Returns a file size limit in bytes based on the PHP upload_max_filesize
	 * and post_max_size
	 * @return Ambigous <number, unknown>
	 */
	public static function determineFileUploadMaxSize() {
		static $maxSize = -1;
	
		if ($maxSize < 0) {
			// Start with post_max_size.
			$maxSize = self::parsePhpIniSize(ini_get('post_max_size'));
	
			// If upload_max_size is less, then reduce. Except if upload_max_size is
			// zero, which indicates no limit.
			$upload_max = self::parsePhpIniSize(ini_get('upload_max_filesize'));
			if ($upload_max > 0 && $upload_max < $maxSize) {
				$maxSize = $upload_max;
			}
		}
		return $maxSize;
	}
	
	public static function curlExec($ch) {
		$response = @curl_exec($ch);
		if (false === $response) {
			curl_close($ch);
			throw new CurlOperationException(curl_error($ch) . ' (' . curl_errno($ch) . ')');
		}
		return $response;
	}
	
	public static function parsePhpIniSize($size) {
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
		if ($unit) {
			// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		} else {
			return round($size);
		}
	}
}
