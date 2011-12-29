<?php
/**
 * @author          ATAFOTO.studio (Jacques BODIN-HULLIN) <jacques@atafotostudio.com>
 * @copyright       (C) ATAFOTO.studio (Jacques BODIN-HULLIN)
 * @license         Tous droits réservés
 * @since           2010-09-17
 */

class Core_Exception extends Exception
{
    public static function handler(Exception $e)
    {
        $msg = $e->getMessage();
        $trace = nl2br($e->getTraceAsString());
        echo <<<TXT
<h3>Exception</h3>
<div><strong>Message : </strong> $msg</div>
<div><strong>Stack Trace : </strong><br/>$trace</div>
TXT;
    }
}
