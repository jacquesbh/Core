<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-20
 */

class Core_Request
{
    /**
     * L'instance de la classe : Singleton
     */
    protected static $_instance;

    /**
     * Les paramètres de la requête
     */
    protected $_default = array(
        'module' => 'default',
        'controller' => 'index',
        'action' => 'index'
    );
    protected $_module;
    protected $_controller;
    protected $_action;

    protected $_moduleEnabled = true;

    /**
     * Construction de la requête
     */
    private function __construct()
    {
        if (isset($_GET['module'])) {
            $this->_module = strtolower($_GET['module']);
        }
        if (isset($_GET['controller'])) {
            $this->_controller = strtolower($_GET['controller']);
        }
        if (isset($_GET['action'])) {
            $this->_action = strtolower($_GET['action']);
        }
    }

    /**
     * Enregistrement de la route à suivre
     */
    public function setRoute($action = null, $controller = null, $module = null)
    {
        if (null !== $module) {
            if (false === $module) {
                $module = $this->_default['module'];
            }
            $this->_module = strtolower($module);
        }
        if (null !== $controller) {
            $this->_controller = strtolower($controller);
        }
        if (null !== $action) {
            $this->_action = strtolower($action);
        }
    }

    /**
     * Récupération de l'instance de la classe
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Setters statiques
     */
    public function setDefaultRoute($action = null, $controller = null, $module = null)
    {
        if (is_string($action)) {
            $this->_default['action'] = $action;
        }
        if (is_string($controller)) {
            $this->_default['controller'] = $controller;
        }
        if (is_string($module)) {
            $this->_default['module'] = $module;
        }
    }

    /**
     * Désactivation des modules
     */
    public function disableModule($bool)
    {
        $this->_moduleEnabled = (bool) !$bool;
    }

    /**
     * Caller
     */
    public function __call($method, $arguments)
    {
        $filter = new Core_Filter_StrToCamelCase;
        $filter->setSeparator('-');

        $lcfirst = create_function('$v', '$v[0] = strtolower($v[0]); return $v;');

        switch ($method) {
        case "getModuleName":
            return $lcfirst($filter->filter(($this->_moduleEnabled) ? (($this->_module !== null) ? $this->_module : $this->_default['module']) : null));
            break;
        case "getControllerName":
            return $lcfirst($filter->filter(($this->_controller !== null) ? $this->_controller : $this->_default['controller']));
            break;
        case "getActionName":
            return $lcfirst($filter->filter(($this->_action !== null) ? $this->_action : $this->_default['action']));
            break;
        default:
            throw new Core_Exception("Méthode inconnue");
            break;
        }
    }

    /***
     * Retourne le path du module demandé
     */
    public function getModulePath($name = null)
    {
        if (!$this->_moduleEnabled) {
            return '';
        } else {
            if (!defined('MODULE_PATH')) {
                throw new Core_Exception("MODULE_PATH non définie.");
            }

            if (null !== $name) {
                return MODULE_PATH . '/' . strtolower($name) . '/';
            }
            return MODULE_PATH . '/' . $this->getModuleName() . '/';
        //} else {
            //return $this->_module . '/';
        }
    }

    /**
     * Retourne le filename du contrôleur demandé
     */
    public function getControllerFilename($controller = null, $module = null)
    {
        $filename = $this->getModulePath($module);
        if (null === $controller) {
            $controller = $this->getControllerName();
        }
        return $filename .= '/controllers/' . ucfirst(strtolower($controller)) . 'Controller.php';
    }

    /**
     * Retourne le nom de la classe du contrôleur passé en paramètre
     */
    public function getControllerClassName($controller = null, $module = null)
    {
        $className = "";
        if (null !== $module) {
            $className .= ucfirst(strtolower($module)) . '_';
        }
        if (null === $controller) {
            $controller = $this->getControllerName();
        }
        return $className .= ucfirst(strtolower($controller)) . 'Controller';
    }

    /**
     * Retourne le chemin vers le fichier phtml de l'action demandée
     */
    public function getActionViewFilename($action, $controller, $module = null)
    {
        if (null === $module) {
            return '/views/scripts/'
                . strtolower($controller)
                . '/'
                . strtolower($action)
                . '.phtml';
        }
        return $this->getModulePath($module)
            . '/views/scripts/'
            . strtolower($controller)
            . '/'
            . strtolower($action)
            . '.phtml';
    }

}
