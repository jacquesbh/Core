<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-23
 */

class Core_View_Helper_Abstract implements Core_View_Helper_Interface
{
    /**
     * La vue
     */
    public $view;

    /**
     * Initialisation de la vue
     */
    public function setView(Core_View $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Appel de l'aide
     */
    public function direct()
    {
    }
}
