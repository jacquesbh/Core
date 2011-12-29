<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-22
 */

class Core_Layout extends Core_I18n
{
    /**
     * Variables du layout
     */
    protected $_vars;

    /**
     * Dossier contenant les layouts
     */
    protected $_dir;

    /**
     * Nom du layout principal
     */
    protected $_layout;

    /**
     * Si le layout est activé
     */
    protected $_enabled = true;

    /***
     * Construction du layout
     */
    public function __construct($directory, $name = 'layout')
    {
        if (!is_dir($directory)) {
            throw new Core_Layout_Exception("Dossier des layouts introuvable.");
        }
        $this->_dir = $directory;
        $this->_layout = $name;
    }

    /**
     * Activation du layout
     */
    public function enableLayout()
    {
        $this->_enabled = true;
    }

    /**
     * Désactivation du layout
     */
    public function disableLayout()
    {
        $this->_enabled = false;
    }

    /**
     * Retourne si le layout est actif ou non
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Set de la view
     */
    public function setView(Core_View $view)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Get de la view
     */
    public function getView()
    {
        return $this->_view;
    }


    /**
     * Setter
     */
    public function __set($name, $value)
    {
        $this->_vars[$name] = $value;
    }

    /**
     * Getter
     */
    public function __get($name)
    {
        if (isset($this->_vars[$name])) {
            return $this->_vars[$name];
        }
        return null;
    }

    /**
     * Afficher un autre layout
     */
    public function render($name)
    {
    }

    /**
     * Affichage
     */
    public function toString()
    {
        if ($this->isEnabled()) {
            ob_start();
            include $this->_dir . '/' . $this->_layout . '.phtml';
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        } else {
            return $this->content;
        }
    }

    /**
     * Caller
     */
    public function __call($name, $args)
    {
        return $this->getView()->__call($name, $args);
    }

}
