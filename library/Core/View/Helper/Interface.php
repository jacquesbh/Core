<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-23
 */

interface Core_View_Helper_Interface
{
    //public $view;
    public function setView(Core_View $view);
    public function direct();
}
