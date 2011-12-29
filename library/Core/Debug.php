<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-20
 */

class Core_Debug
{
    public static function dump($var, $name = false)
    {
        echo "<pre>";
        if ($name) {
            echo $name . " ";
        }
        echo var_dump($var);
        echo "</pre>";
    }
}
