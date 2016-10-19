<?php
/**
 * AyDataFactory 'each' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Factory
 * @author     Ray Fung
 *
 * @access public
 * @param callback $callback
 * @return AyDataFactory
 */
function aydf_factory_each($callback) {
	foreach ($this as $key => $value) {
		$data = new AyDataFactoryProcessor($this, $key);
		call_user_func($data->reflection($callback));
	}
	return $this;
}
?>