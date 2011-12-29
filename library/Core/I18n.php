<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-22
 */

class Core_I18n
{
    /**
     * Traduction
     */
    protected $_i18n;

    /**
     * Récupération d'une traduction
     */
    protected function _($id /* , $args... */)
    {
        if (!isset($this->_getI18n()->{$id})) {
            throw new Core_Exception(sprintf('Identifiant "%s" introuvable', $id));
        }
        $str = $this->_getI18n()->{$id};
        if (func_num_args() > 1) {
            $args = func_get_args();
            array_shift($args);
            return vsprintf($str, $args);
        }
        return $str;
    }

    /**
     * Récupération du tableau des traduction
     */
    protected function _getI18n()
    {
        if (is_null($this->_i18n)) {
            $this->_i18n = Core_Registry::get('i18n');
        }
        return $this->_i18n;
    }
}

