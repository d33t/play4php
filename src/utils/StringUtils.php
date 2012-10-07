<?php

class StringUtils {
	/**
	 * Tests if the string haystack starts with the specified needle
	 * 
	 * Source: http://stackoverflow.com/a/834355/777679
	 * 
	 * @param string $haystack the string to test
	 * @param string $needle the searched string
	 * @return boolean true if the haystack starts with the specified needle, otherwise false
	 */
	public static function startsWith($haystack, $needle) {
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
	
	/**
	 * Tests if the string haystack ends with the specified needle
	 * 
	 * Source: http://stackoverflow.com/a/834355/777679
	 * 
	 * @param string $haystack the string to test
	 * @param string $needle the searched string
	 * @return boolean true if the haystack ends with the specified needle, otherwise false
	 */
	public static function endsWith($haystack, $needle) {
		$length = strlen($needle);
		$start  = $length * -1; //negative
		return (substr($haystack, $start) === $needle);
	}
	
	/**
	 * Replaces first occurrence of the search string with the replacement string in the subject
	 * 
	 * @param string $subject
	 * @param string $search
	 * @param string $replacement
	 * @return string the old subject if nothing replaced, otherwise the subject replaced by replacement string
	 */
	public static function replaceFirst($subject, $search, $replacement) {
		if(self::startsWith($subject, $search) === false) {
			return $haystack;
		}
		$search = str_replace("/", "\\/", $search);
		return preg_replace('/^' . $search . '/', $replacement, $subject, 1);
	}
	
}