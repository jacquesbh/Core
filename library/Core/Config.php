<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-17
 */

/**
 * Gestion d'une configuration
 *
 * Chaque type de configuration, par exemple Core_Config_Ini étend Core_Config.
 */
class Core_Config extends ArrayIterator
{
    /**
     * Construction de la configuration
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Getter
     */
    public function __get($name)
    {
        return $this[$name];
    }

    /**
     * Isset
     */
    public function __isset($name)
    {
        return isset($this[$name]);
    }

}
