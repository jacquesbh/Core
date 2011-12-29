<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-22
 */

/**
 * Contrôleur de base
 */
class Core_Controller_Action extends Core_I18n
{
    /**
     * InvokeArgs
     */
    private $_invokeArgs;

    /**
     * Requête
     */
    protected $_request;

    /**
     * La vue
     */
    public $view;

    /***
     * Construction
     */
    public function __construct(Core_Request $request, array $invokeArgs)
    {
        $this->_invokeArgs = $invokeArgs;
        $this->_request = $request;

        $this->init();
    }

    /**
     * Initialisation
     */
    public function init()
    {
    }

    /**
     * Pre dispatch
     */
    public function preDispatch()
    {
    }

    /**
     * Post dispatch
     */
    public function postDispatch()
    {
    }

    /**
     * Récupération d'un InvokeArg
     */
    protected function _getInvokeArg($name)
    {
        if (isset($this->_invokeArgs[$name])) {
            return $this->_invokeArgs[$name];
        }
        return null;
    }

    /**
     * Récupération de la requête
     */
    protected function _getRequest()
    {
        return $this->_request;
    }

    /**
     * Initialisation de la vue
     */
    public function setView(Core_View $view)
    {
        $this->view = $view;
    }

    /**
     * Affichage - Méthode publique
     */
    public function dispatch($display = true)
    {
        if ($display === null) {
            $display = true;
        }
        if ((bool) $display) {
            /// On affiche la vue
            ob_start();
            $this->preDispatch();
            echo $this->view->toString();
            $this->postDispatch();
            ob_end_flush();
        }
        return;
    }
}
