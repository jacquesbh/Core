<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-23
 */

class Core_View_Helper_BaseUrl extends Core_View_Helper_Abstract
{
    /**
     * Url de base
     */
    protected $_base;

    /**
     * Gestion des différents dossiers
     */
    protected $_dirs = array();

    /**
     * Le dossier en cours
     */
    protected $_dir;

    /**
     * Le fichier en cours
     */
    protected $_file;

    /**
     * Construction
     */
    public function __construct()
    {
        $this->_base = dirname($_SERVER['PHP_SELF']);
    }

    public function baseUrl($file = null, $dir = null)
    {
        $this->_file = $file;
        $this->_dir = $dir;
        return $this;
    }

    public function __set($name, $value)
    {
        $this->_dirs[$name] = $value;
    }

    public function __get($name)
    {
        if (!isset($this->_dirs[$name])) {
            return null;
        }
        return $this->_dirs[$name];
    }

    public function __toString()
    {
        return $this->_base . $this->{$this->_dir} . $this->_file;
    }

}
