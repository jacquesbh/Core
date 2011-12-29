<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-22
 */

interface Core_Validate_Interface
{
    /**
     * Détermine si la valeur est valide
     */
    public function isValid($value);
}
