<?php
/**
 * AyDataFactoryProcessor 'deserialize' Plugin
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
function aydf_string_deserialize($arrayType = false) {
	$this->set(json_decode($this->get(), $arrayType));
	return $this;
}
?>