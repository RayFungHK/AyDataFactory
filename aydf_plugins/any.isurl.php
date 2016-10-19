<?php
/**
 * AyDataFactoryProcessor 'isurl' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Any
 * @author     Ray Fung
 *
 * @access public
 * @return bool
 */
function aydf_any_isurl() {
	if (!is_string($this->get())) {
		return false;
	}
	return !!preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $this->get());
}
?>