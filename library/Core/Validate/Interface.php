<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
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
