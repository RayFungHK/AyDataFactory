<?php
/**
 * AyDataFactoryProcessor 'tofloat' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage String
 * @author     Ray Fung
 *
 * @access public
 * @return AyDataFactoryProcessor
 */
function aydf_string_tofloat() {
	$this->set(floatval($this->get()));
	return $this;
}
?>