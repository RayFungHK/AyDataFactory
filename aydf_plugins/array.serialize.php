<?php
/**
 * AyDataFactoryProcessor 'serialize' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Array
 * @author     Ray Fung
 *
 * @access public
 * @return string
 */
function aydf_array_serialize() {
	$this->set(json_encode($this->get()));
	return $this;
}
?>