<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-28
 */

class Core_Filter_StrToCamelCase implements Core_Filter_Interface
{
    protected $_separator = '-';

    public function filter($value)
    {
        $tab = explode($this->_separator, $value);
        //$f = function ($val) { return ucfirst($val); };
        $f = create_function('$val', '$val[0] = strtolower($val[0]); return $val;');
        return implode('', array_map($f, $tab));
    }

    public function setSeparator($separator)
    {
        $this->_separator = (string) $separator;
    }
}

