<?php
/**
 * AyDataFactory 'denull' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Factory
 * @author     Ray Fung
 *
 * @access public
 * @return AyDataFactory
 */
function aydf_factory_denull() {
	foreach ($this as $index => $value) {
		if (is_null($this[$index])) {
			$this[$index] = '';
		}
	}
	return $this;
}
?>