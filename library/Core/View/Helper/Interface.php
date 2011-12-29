<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-23
 */

interface Core_View_Helper_Interface
{
    //public $view;
    public function setView(Core_View $view);
    public function direct();
}
