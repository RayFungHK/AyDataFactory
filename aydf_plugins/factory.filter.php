<?php
/**
 * AyDataFactory 'filter' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Factory
 * @author     Ray Fung
 *
 * @access public
 * @return AyDataFactory
 */
function aydf_factory_filter() {
	$args = func_get_args();
	$dataArray = $this->getArrayCopy();
	if (count($args)) {
		foreach ($args as $arg) {
			if (is_string($arg)) {
				$arg = array($arg);
			}

			if (is_array($arg)) {
				$dataArray = array_intersect_key($dataArray, array_flip($arg));
			} elseif ($arg instanceof Closure) {
				$filtered = array();
				foreach ($dataArray as $key => $value) {
					if ($arg($key, $value)) {
						$filtered[$key] = $value;
					}
				}
				$dataArray = $filtered;
			}
		}
	}
	return new AyDataFactory($dataArray);
}
?>