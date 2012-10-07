<?php
/**
 * Smarty plugin
 * 
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty normalize_umlauts modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     normalize_umlauts<br>
 * Purpose:  normalize a string containing umlauts depending on the convert type
 * Input:<br>
 *          - string: input text
 *          - $esc_type: escape type (could be convert (converts to ae, ue, oe, ss, etc), html (html codes), url (url encoded)
 * 
 * @author Rusi Rusev <Sociographic UG>
 * @param string  $string        input string
 * @param string  $esc_type      escape type
 * @return string the normalized input string
 */
function smarty_modifier_normalize_umlauts($string, $esc_type='convert') {
	switch ($esc_type) {
		case 'convert': {
			$string = preg_replace('/ä/', 'ae', $string);
			$string = preg_replace('/Ä/', 'Ae', $string);
			$string = preg_replace('/ö/', 'oe', $string);
			$string = preg_replace('/Ö/', 'Oe', $string);
			$string = preg_replace('/ü/', 'ue', $string);
			$string = preg_replace('/Ü/', 'Ue', $string);
			$string = preg_replace('/ß/', 'ss', $string);
			return $string;
		}
		case 'html': {
			return htmlspecialchars($string, ENT_QUOTES, Smarty::$_CHARSET, true);
		}
		case 'url': {
			return rawurlencode($string);
		}
		default: {
			return $string;
		}
	}
} 

?>