<?php
/**
 * AyDataFactoryProcessor 'isdefined' Plugin
 * 
 * @version    1.0.0
 * @package    AyDataFactory
 * @subpackage Any
 * @author     Ray Fung
 *
 * @access public
 * @param bool $fullcheck (default: false)
 * @return AyDataFactoryProcessor
 */
function aydf_any_isdefined($fullcheck = false) {
	$data = $this->get();
	if (is_array($data)) {
		foreach ($data as $key => $value) {
			if (!$value && $fullcheck) {
				return false;
			} elseif ($value && !$fullcheck) {
				return true;
			}
		}
		return $fullcheck;
	}
	return (isset($data));
}
?>