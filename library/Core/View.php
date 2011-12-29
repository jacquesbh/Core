<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-17
 */

class Core_View extends Core_I18n
{
    /**
     * Registre interne
     */
    protected $_vars = array();

    /**
     * Le layout
     */
    protected $_layout;

    /**
     * Helpers path
     */
    private $_helpersPath = array();

    /**
     * Helpers
     */
    private $_helpers = array();

    /**
     * Construction de la vue
     */
    public function __construct()
    {
        $this->addHelperPath("Core/View/Helper", "Core_View_Helper_");
    }

    /***
     * Initialisation du layout
     */
    public function setLayout(Core_Layout $layout)
    {
        $this->_layout = $layout;
        $layout->setView($this);
        return $this;
    }

    /**
     * Récupération du layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Setter
     */
    public function __set($nom, $valeur)
    {
        $this->_vars[$nom] = $valeur;
    }


    /**
     * Getter
     */
    public function __get($nom)
    {
        if (!isset($this->_vars[$nom])) {
            throw new Exception('Variable introuvable');
        }
        return $this->_vars[$nom];
    }

    /**
     * Helpers
     */
    public function __call($name, $args)
    {
        $helper = $this->_getHelper($name);

        return call_user_func_array(
            array($helper, $name),
            $args
        );
    }

    /**
     * Retourne le Helper demandé
     */
    protected function _getHelper($name)
    {
        $name = ucfirst($name);
        if (isset($this->_helpers[$name])) {
            return $this->_helpers[$name];
        }

        $path = false;
        $prefix = false;
        foreach ($this->_helpersPath as $helperPath) {
            if ((@include "{$helperPath['path']}/$name.php")) {
                $path = $helperPath['path'];
                $prefix = $helperPath['prefix'];
                break;
            }
        }

        if (false === $path) {
            throw new Core_Exception("Helper '$name' introuvable.");
        }

        require_once $path . DIRECTORY_SEPARATOR . "$name.php";

        $class = $prefix . $name;
        $this->_helpers[$name] = new $class();
        return $this->_helpers[$name];
    }

    /**
     * Affichage
     */
    public function toString()
    {
        /// On récupère la requête
        $request = Core_Request::getInstance();
        $viewFile = APPLICATION_PATH . $request->getActionViewFilename(
            $request->getActionName(),
            $request->getControllerName(),
            $request->getModuleName()
        );
        //if (!is_file($viewFile)) {
            //throw new Core_Exception("Fichier de vue inexistant.", 500);
        //}

        ob_start();
        include $viewFile;
        $content = ob_get_contents();
        ob_end_clean();

        if ($this->getLayout() instanceof Core_Layout) {
            $this->getLayout()->content = $content;
            return (string) $this->getLayout()->toString();
        } else {
            return $content;
        }
    }

    /**
     * Ajout d'un Path pour les helpers
     */
    public function addHelperPath($path, $prefix)
    {
        if (!isset($this->_helpersPath[$path])) {
            array_unshift($this->_helpersPath, array(
                'path' => $path,
                'prefix' => $prefix
            ));
        }
    }

}
