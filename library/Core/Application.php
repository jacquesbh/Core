<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-17
 */

/**
 * Chargement de l'autoload
 */
function __autoload($name)
{
    require_once str_replace('_', '/', $name) . '.php';
}

/**
 * Chargement initiale du projet
 */
class Core_Application
{
    /**
     * Environnement
     */
    protected $_environment;

    /**
     * Configuration
     */
    protected $_config;

    /**
     * Le bootstrap
     */
    protected $_bootstrap;


    /**
     * Constructeur
     */
    public function __construct($env, $config)
    {
        set_exception_handler("Core_Exception::handler");

        // L'environnement
        $this->_environment = (string) $env;

        // Chargement de la configuration
        if (is_string($config)) {
            if (!is_file($config)) {
                throw new Core_Exception("Si la configuration est une chaîne de caractères, cela doit représenter un fichier .ini existant.");
            }
            $this->_config = new Core_Config_Ini($config, $env);
        } else {
            throw new Core_Exception("La configuration doit être de type string.");
        }
    }

    /***
     * Lancement du bootstrap
     */
    public function bootstrap()
    {
        if (!isset($this->_config['bootstrap'])) {
            throw new Core_Exception("Impossible de charger le fichier bootstrap : section manquante dans la configuration.");
        }

        $conf = $this->_config['bootstrap'];

        if (!isset($conf['filename'], $conf['class'])) {
            throw new Core_Exception("les variables filename et class sont manquantes dans la configuration du bootstrap.");
        }

        if (!is_file($conf['filename'])) {
            throw new Core_Exception("Le fichier Bootstrap n'existe pas.");
        }

        require_once $conf['filename'];
        $this->_bootstrap = new $conf['class']($this->_config);
        $this->_bootstrap->init();

        return $this;
    }

    /**
     * Récupération du Bootstrap
     */
    public function getBootstrap()
    {
        if (null === $this->_bootstrap) {
            $this->_bootstrap = $this->bootstrap();
        }
        return $this->_bootstrap;
    }

    /***
     * Lancement de l'application
     */
    public function run()
    {
        try {
            ob_start();
            $this->getBootstrap()->run();
            ob_end_flush();
        } catch (Core_Exception $e) {
            ob_end_clean();
            /// On affiche la page d'erreur
            Core_Registry::set('response', $e);
            $request = Core_Request::getInstance();
            $request->setRoute("error", "error", false);
            $this->getBootstrap()->run();
        }
    }

    /***
     * Destruction
     */
    public function __destruct()
    {
        restore_exception_handler();
    }

}
