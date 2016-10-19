<?php
/**
 * AyDataFactoryProcessor 'trim' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage String
 * @author     Ray Fung
 *
 * @access public
 * @return AyDataFactoryProcessor
 */
function aydf_string_trim() {
	$this->set(trim($this->get()));
	return $this;
}
?>