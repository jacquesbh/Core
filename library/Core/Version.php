<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-23
 */

final class Core_Version
{
    const VERSION = '0.1dev';

    public static function compareVersion($version)
    {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        return version_compare($version, strtolower(self::VERSION));
    }
}
