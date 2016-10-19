<?php
/**
 * AyDataFactory 'update' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Factory
 * @author     Ray Fung
 *
 * @access public
 * @param array $array (default: array())
 * @return AyDataFactory
 */
function aydf_factory_update($array = array()) {
	if (is_array($array) || class_implements($data) == 'ArrayObject') {
		foreach ($this as $key => $value) {
			if (array_key_exists($key, $array)) {
				$this[$key] = $array[$key];
			}
		}
	}
	return $this;
}
?>