<?php
/**
 * AyDataFactoryProcessor 'isemail' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Any
 * @author     Ray Fung
 *
 * @access public
 * @return bool
 */
function aydf_any_isemail() {
	if (!is_string($this->get())) {
		return false;
	}
	return (filter_var($this->get(), FILTER_VALIDATE_EMAIL));
}
?>