<?php
/**
 * AyDataFactoryProcessor 'tolower' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage String
 * @author     Ray Fung
 *
 * @access public
 * @return AyDataFactoryProcessor
 */
function aydf_string_tolower() {
	$this->set(strtolower($this->get()));
	return $this;
}
?>