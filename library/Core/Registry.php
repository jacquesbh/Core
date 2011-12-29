<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-17
 */

class Core_Registry
{
    /**
     * Instance de la classe
     */
    private static $_instance;

    /**
     * Le registre
     */
    protected static $_registry = array();

    /**
     * Chargement du singleton
     */
    private function __construct()
    {
    }

    /**
     * Récupération du singleton
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Enregistrement d'une variable dans le registre
     */
    public static function set($nom, $valeur)
    {
        self::$_registry[$nom] = $valeur;
    }

    /**
     * Récupération d'une variable dans le registre
     */
    public static function get($nom)
    {
        if (!isset(self::$_registry[$nom])) {
            throw new Exception("Aucune valeur ne correspond dans le registre");
        }
        return self::$_registry[$nom];
    }

    /**
     * Setter
     */
    public function __set($nom, $valeur)
    {
        $this->_registry[$nom] = $valeur;
    }

    /**
     * Unsetter
     */
    public function __unset($name)
    {
        if ($this->_registry[$name]) {
            unset($this->_registry[$name]);
        }
    }

    /**
     * Getter
     */
    public function __get($nom)
    {
        if (!isset($this->_registry[$nom])) {
            throw new Exception("Aucune valeur ne correspond dans le registre");
        }
        return $this->_registry[$nom];
    }

    /**
     * Isset
     */
    public function __isset($name)
    {
        return isset($this->_registry[$name]);
    }
}
