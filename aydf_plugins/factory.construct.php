<?php
/**
 * AyDataFactory 'construct' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Factory
 * @author     Ray Fung
 *
 * @access public
 * @param array $array (default: array())
 * @param bool $definekey (default: false)
 * @return AyDataFactory
 */
function aydf_factory_construct($array = array(), $definekey = false) {
	if (is_array($array) || is_a($array, 'AyDataFactory')) {
		foreach ($array as $index => $value) {
			$key = ($definekey) ? $value : $index;
			if (!$definekey) {
				$this[$key] = $value;
			} elseif (!array_key_exists($key, $this)) {
				$this[$key] = null;
			}
		}
	}
	return $this;
}
?>