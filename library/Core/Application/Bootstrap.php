<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-20
 */

class Core_Application_Bootstrap
{
    /**
     * Configuration
     */
    public $config;

    /**
     * Les méthodes de la classe
     */
    protected $_methods;

    /**
     * Ressources lancées
     */
    protected $_run = array();

    /***
     * Construction
     */
    public function __construct(Core_Config $config)
    {
        $this->config = $config;
    }

    /**
     * Initialisation
     */
    public function init()
    {
        /// Récupération des méthodes pour lancer dans l'ordre celles avec _init au début
        $this->_methods = $methods = get_class_methods($this);
        foreach ($methods as $method) {
            $match = array();
            if (preg_match('`^_init(.+)`', $method, $match)) {
                $this->_markAsRun($match[1]);
            }
        }
    }

    /**
     * Lancement
     */
    public function run()
    {
        /// La requête
        $request = Core_Request::getInstance();

        if (isset($this->config->request)) {
            $config = $this->config->request;
            if (isset($config['default'])) {
                $defaultAction      = null;
                $defaultController  = null;
                $defaultModule      = null;
                if (isset($config['default']['action'])) {
                    $defaultAction = $config['default']['action'];
                }
                if (isset($config['default']['controller'])) {
                    $defaultController = $config['default']['controller'];
                }
                if (isset($config['default']['module'])) {
                    $defaultModule = $config['default']['module'];
                }
                $request->setDefaultRoute($defaultAction, $defaultController, $defaultModule);
            }
        }

        /// On regarde si on doit utiliser les modules ou non
        if (!isset($this->config->modules) || !is_array($this->config->modules)) {
            $request->disableModule(true);
        }
        $controllerFilename = APPLICATION_PATH . $request->getControllerFilename();
        $module = $request->getModuleName();

        /// Récupération du bon contrôleur
        if (!is_file($controllerFilename)) {
            throw new Core_Exception(sprintf("Contrôleur %s inconnu.", $controllerFilename), 404);
        }

        /// Instance du contrôleur
        require_once $controllerFilename;
        $controllerClass = $request->getControllerClassName(null, $module);
        if (!class_exists($controllerClass, false)) {
            throw new Core_Exception(sprintf("Contrôleur %s non défini.", $controllerClass), 404);
        }
        $controller = new $controllerClass($request, array(
            'config'    => $this->config,
            'bootstrap' => $this
        ));

        /// Récupération de l'action
        $methods = get_class_methods($controller);
        if (!in_array($actionName = $request->getActionName() . 'Action', $methods)) {
            throw new Core_Exception(sprintf("Action %s non définie.", $actionName));
        }

        /// Récupération de la vue
        if ($this->hasResource('view')) {
            $view = $this->getResource('view');
            if (!($view instanceof Core_View)) {
                throw new Core_Exception('Type de vue incorrect', 500);
            }
        } else {
            $view = new Core_View;
        }

        $controller->setView($view);

        /// Affichage final
        if ($this->hasResource('layout')) {
            $view->setLayout($this->getResource('layout'));
        }

        /// Lancement de l'action
        ob_start();
        $display = $controller->$actionName();
        $content = ob_get_contents();
        ob_end_clean();
        $controller->dispatch($display);
        echo $content;
    }

    /**
     * Marquage d'une ressource comme étant lancée
     */
    protected function _markAsRun($name)
    {
        if (!$this->hasResource($name)) {
            $methodName = "_init" . ucfirst($name);
            if (!in_array($methodName, $this->_methods)) {
                throw new Core_Exception("Ressource '$name' introuvable.");
            }
            $this->_run[strtolower($name)] = $this->{$methodName}();
        }
    }

    /**
     * On informe si la resource existe
     */
    public function hasResource($name)
    {
        return isset($this->_run[strtolower($name)]);
    }

    /**
     * On récupère une resource
     */
    public function getResource($name)
    {
        $this->_markAsRun($name);
        return $this->_run[strtolower($name)];
    }
}
