<?php
/**
 * AyDataFactoryProcessor 'isip' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Any
 * @author     Ray Fung
 *
 * @access public
 * @return bool
 */
function aydf_any_isip() {
	if (!is_string($this->get())) {
		return false;
	}
	return !!preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $this->get());
}
?>