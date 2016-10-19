<?php
/**
 * AyDataFactoryProcessor 'unserialize' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage String
 * @author     Ray Fung
 *
 * @access public
 * @param bool $arrayType (default: false)
 * @return AyDataFactoryProcessor
 */
function aydf_string_unserialize($arrayType = false) {
	$this->set(function() use ($arrayType) {
		return json_decode($this->get(), $arrayType);
	});
	return $this;
}
?>