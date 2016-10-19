<?php
/**
 * AyDataFactoryProcessor 'each' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Array
 * @author     Ray Fung
 *
 * @access public
 * @param callback $callback
 * @return AyDataFactoryProcessor
 */
function aydf_array_each($callback) {
	$dataset = $this->get();
	if (!is_a($dataset, 'AyDataFactory')) {
		$dataset = new AyDataFactory($dataset);
		$this->set($dataset);
	}
	foreach ($dataset as $key => $value) {
		$data = new AyDataFactoryProcessor($dataset, $key);
		call_user_func($data->reflection($callback));
	}
	return $this;
}
?>