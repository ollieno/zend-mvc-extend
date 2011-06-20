<?php

class Twm_View_Helper_FormValue extends Zend_View_Helper_Abstract {

    function formValue($name) {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }
        return '';
    }

}