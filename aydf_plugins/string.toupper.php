<?php
/**
 * AyDataFactoryProcessor 'toupper' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage String
 * @author     Ray Fung
 *
 * @access public
 * @return AyDataFactoryProcessor
 */
function aydf_string_toupper() {
	$this->set(strtoupper($this->get()));
	return $this;
}
?>