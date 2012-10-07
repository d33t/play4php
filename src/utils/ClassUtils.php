<?php
class ClassUtils {
	public static function convertToClass($path) {
		return (strpos($path, "/") !== false) ? "\\" . str_replace("/", "\\", $path) : $path;
	}
	
	public static function convertToPath($className) {
		return substr($className, 0, 1) == "\\" ? str_replace("\\", "/", substr($className, 1)) : str_replace("\\", "/", $className);
	}
}