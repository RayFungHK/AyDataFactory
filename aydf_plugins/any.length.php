<?php
/**
 * AyDataFactoryProcessor 'length' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Any
 * @author     Ray Fung
 *
 * @access public
 * @return int
 */
function aydf_any_length() {
	$data = $this->get();
	if (is_array($data)) {
		return count($data);
	} elseif (is_string($data)) {
		return strlen($data);
	}
	return (isset($data)) ? 1 : 0;
}
?>