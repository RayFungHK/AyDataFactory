<?php
/**
 * AyDataFactoryProcessor 'toint' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage String
 * @author     Ray Fung
 *
 * @access public
 * @return AyDataFactoryProcessor
 */
function aydf_string_toint() {
	$this->set(intval($this->get()));
	return $this;
}
?>