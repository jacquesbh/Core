<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-20
 */

/**
 * Utilisation d'un fichier de config en .ini
 */
class Core_Config_Ini extends Core_Config
{
    /**
     * Le nom du fichier parsé
     */
    protected $_filename;

    /**
     * Séparateur de nom dans la config
     */
    protected $_nestSeparator = ".";

    /**
     * Les constantes globales
     */
    protected $_constantsKeys;
    protected $_constantsValues;

    /***
     * Construction
     */
    public function __construct($filename, $part = null)
    {
        if (!is_string($filename) || !is_file($filename)) {
            throw new Core_Exception();
        }

        /// On garde en mémoire le nom du fichier
        $this->_filename = $filename;

        /// On parse
        parent::__construct($this->_parseIni($filename, $part));
    }

    /***
     * Parsage du fichier Ini
     */
    protected function _parseIni($filename, $part = null)
    {
        $file = parse_ini_file($filename, true);
        if (is_string($part)) {
            if (!isset($file[$part])) {
                throw new Core_Exception("Section incorrecte");
            }
            $config = $file[$part];
        } else {
            $config = $file;
        }

        return $this->_parseTab($config);
    }

    /***
     * Parsage d'un tableau .ini pour en faire un tableau en plusieurs dimensions
     */
    protected function _parseTab(array $tab)
    {
        $ret = array();
        foreach ($tab as $key => $value) {
            if (strpos($key, ".") !== false) {
                $ret = array_merge_recursive($ret, $this->_processKey(array(), $key, $value));
            } else {
                $ret[$key] = $this->_processConstants($value);
            }
        }
        return $ret;
    }

    /**
     * From Zend Framework 1.10
     *
     * Assign the key's value to the property list. Handles the
     * nest separator for sub-properties.
     *
     * @param  array  $config
     * @param  string $key
     * @param  string $value
     * @throws Core_Config_Exception
     * @return array
     */
    protected function _processKey($config, $key, $value)
    {
        if (strpos($key, ".") !== false) {
            $pieces = explode($this->_nestSeparator, $key, 2);
            if (strlen($pieces[0]) && strlen($pieces[1])) {
                if (!isset($config[$pieces[0]])) {
                    if ($pieces[0] === '0' && !empty($config)) {
                        // convert the current values in $config into an array
                        $config = array($pieces[0] => $config);
                    } else {
                        $config[$pieces[0]] = array();
                    }
                } elseif (!is_array($config[$pieces[0]])) {
                    throw new Core_Config_Exception("Cannot create sub-key for '{$pieces[0]}' as key already exists");
                }
                $config[$pieces[0]] = $this->_processKey($config[$pieces[0]], $pieces[1], $value);
            } else {
                throw new Core_Config_Exception("Invalid key '$key'");
            }
        } else {
            $config[$key] = $this->_processConstants($value);
        }
        return $config;
    }

    /**
     * Récupération des constantes du système
     */
    protected function _getConstants()
    {
        if (null === $this->_constantsKeys) {
            //$func = function ($v) { return ":$v"; };
            $func = create_function('$v', 'return ":$v";');
            $constants = get_defined_constants(true);
            $this->_constantsKeys = array_map($func, array_keys($constants['user']));
            $this->_constantsValues = array_values($constants['user']);
            unset($func, $constants);
        }
        return array($this->_constantsKeys, $this->_constantsValues);
    }

    /**
     * Remplacement des constantes
     */
    protected function _processConstants($value)
    {
        list($keys, $values) = $this->_getConstants();
        return str_replace($keys, $values, $value);
    }
}
