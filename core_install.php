<?php
/**
 * @author          Jacques BODIN-HULLIN <jacques@bodin-hullin.net>
 * @copyright       (C) Jacques BODIN-HULLIN
 * @license         Tous droits réservés
 * @since           2010-09-29
 */

define('PATH', './');

/**
 * Couleurs
 */
function red()      { return "\033[01;31m"; }
function blue()     { return "\033[01;34m"; }
function green()    { return "\033[01;32m"; }
function white()    { return "\033[00m"; }

/**
 * Affichage du message d'accueil
 */
function startMessage()
{
    $intro = <<<INTRO
Installation d'une application Core - ATAFOTO.studio - (c) 2010

INTRO;
    return green() . $intro . white();
}

echo startMessage();


/**
 * Récupération d'une commande
 */
function readCommandLine($path = true)
{
    echo white();
    $in = fopen('php://stdin', 'w');
    if ($path) echo red(), '> ', white();
    $line = trim(fgets($in));
    fclose($in);
    return $line;
}


/***
 * Lancement de la configuration
 */
$config = (object) array(
    'modules'   => false,
    'layout'    => false,
    'i18n'      => (object) array(
        'use'           => false,
        'locales'       => array(),
        'default'       => ''
    ),
    'db'        => (object) array(
        'use'           => false,
        'host'          => '',
        'user'          => '',
        'pass'          => '',
        'name'          => ''
    ),
    'route'     => (object) array(
        'module'        => 'default',
        'controller'    => 'index',
        'action'        => 'index'
    )
);

/// Faut-il charger les modules ?
do {
    echo "Utilisation des modules ? (y/n)\n";
    $line = readCommandLine();
} while (!in_array($line, array('y', 'n')));
$config->modules = ($line == 'y');

/// Faut-il utiliser un layout ?
do {
    echo "Utilisation des layouts ? (y/n)\n";
    $line = readCommandLine();
} while (!in_array($line, array('y', 'n')));
$config->layout = ($line == 'y');

/// Faut-il utiliser l'internationalisation
do {
    echo "Utilisation l'internationalisation ? (y/n)\n";
    $line = readCommandLine();
} while (!in_array($line, array('y', 'n')));
$config->i18n->use = ($line == 'y');

if ($config->i18n->use) {
    echo "Quelles langues (séparées par une virgule) ?\n", blue(), "> ";
    $config->i18n->locales = explode(',', readCommandLine(false));
    array_map('trim', $config->i18n->locales);

    do {
        echo "Par défaut ? (", implode('/', $config->i18n->locales), ") ", blue(), "> ";
        $line = readCommandLine(false);
    } while (!in_array($line, $config->i18n->locales));
    $config->i18n->default = $line;
}

/// Faut-il utiliser une base de données
do {
    echo "Utilisation d'une base de données MySQL ? (y/n)\n";
    $line = readCommandLine();
} while (!in_array($line, array('y', 'n')));
$config->db->use = ($line == 'y');

if ($config->db->use) {
    /// Quel host ?
    echo "Quel host ?\n", blue(), "> ";
    $config->db->host = readCommandLine(false);

    /// Quel user ?
    echo "Quel user ?\n", blue(), "> ";
    $config->db->user = readCommandLine(false);

    /// Quel pass ?
    echo "Quel pass ?\n", blue(), "> ";
    $config->db->pass = readCommandLine(false);

    /// Quelle base ?
    echo "Quel base ?\n", blue(), "> ";
    $config->db->name = readCommandLine(false);
}

/// Quelle est la route par défaut ?
echo red(), "\nConfiguration de la route par défaut :\n", white();

if ($config->modules) {
    echo "Module ? (laisser vide pour le choix par défaut)\n", blue(), "> ";
    $line = readCommandLine(false);
    if (!empty($line)) {
        $config->route->module = $line;
    }
}

echo "Contrôleur ? (laisser vide pour le choix par défaut)\n", blue(), "> ";
$line = readCommandLine(false);
if (!empty($line)) {
    $config->route->controller = $line;
}

echo "Action ? (laisser vide pour le choix par défaut)\n", blue(), "> ";
$line = readCommandLine(false);
if (!empty($line)) {
    $config->route->action = $line;
}



/// Construction de l'arborescence
echo green(), "\nConstruction de l'arborescence...", white();
mkdir($path = $app = PATH . 'application', 0777);
@mkdir(PATH . 'library', 0777);
if ($config->modules) {
    mkdir($path = $path . '/modules');
    mkdir($path = $path . '/' . strtolower($config->route->module));
}

mkdir($tmp = $path . '/controllers');
touch($tmp2 = $tmp . '/ErrorController.php');
chmod($tmp2, 0777);
touch($tmp = $tmp . '/' . ucfirst($config->route->controller) . 'Controller.php');
chmod($tmp, 0777);
$controllerName = (($config->modules) ? ucfirst($config->route->module) . '_' : '' ) . ucfirst($config->route->controller);
$errorControllerName = (($config->modules) ? ucfirst($config->route->module) . '_' : '' ) . 'Error';
ob_start();
?>
    public function <?php echo lcfirst($config->route->action); ?>Action()
    {
        // action's body
        return true;
    }
<?php
$action = ob_get_contents();
ob_end_clean();
file_put_contents($tmp, sprintf("<?php\n\nclass %sController extends Core_Controller_Action\n{\n%s\n}\n",
    $controllerName, $action));

ob_start();
?>
    public function errorAction()
    {
        // Traitement de l'exception
        $this->view->exception = Core_Registry::get('response');

        return true;
    }
<?php
$content = ob_get_contents();
ob_end_clean();
file_put_contents($tmp2, sprintf("<?php\n\nclass %sController extends Core_Controller_Action\n{\n%s\n}\n",
    $errorControllerName, $content));

mkdir($tmp = $path . '/views');
mkdir($tmp = $tmp . '/scripts');
mkdir($tmp2 = $tmp . '/error');
mkdir($tmp = $tmp . '/' . strtolower($config->route->controller));
file_put_contents($tmp . '/' . strtolower($config->route->action) . '.phtml', 'Hello World !');
file_put_contents($tmp2 . '/error.phtml', '<pre><?php echo $this->exception; ?></pre>');

if ($config->layout) {
    mkdir($tmp = $app . '/layouts');
    mkdir($tmp = $tmp . '/scripts');
    touch($tmp = $tmp . '/layout.phtml');
    chmod($tmp, 0777);
    file_put_contents($tmp, '<?php echo $this->content; ?>');
}

echo blue(), " [", green(), "OK", blue(), "]\n", white();

/// Construction du fichier config
echo green(), "\nConstruction du fichier de configuration...", white();

ob_start();
?>
[production]

; Chargement du Bootstrap
bootstrap.filename = :APPLICATION_PATH/Bootstrap.php
bootstrap.class = Bootstrap

<?php if ($config->modules): ?>
; On utilise les modules
modules[] =
<?php endif; ?>

; Route par défaut
<?php if ($config->modules): ?>
request.default.module = <?php echo $config->route->module; ?> 
<?php endif; ?>
request.default.controller = <?php echo $config->route->controller; ?> 
request.default.action = <?php echo $config->route->action; ?> 

<?php if ($config->layout): ?>
; Layout
layout.dir = :APPLICATION_PATH/layouts/scripts/
layout.file = layout
<?php endif; ?>

<?php if ($config->db->use): ?>
; Database
db.host = "<?php echo $config->db->host; ?>"
db.user = "<?php echo $config->db->user; ?>"
db.pass = "<?php echo $config->db->pass; ?>"
db.name = "<?php echo $config->db->name; ?>"
<?php endif; ?>

<?php if ($config->i18n->use): ?>
; I18N - Internationalisation
i18n.default = <?php echo $config->i18n->default; ?> 
<?php foreach ($config->i18n->locales as $hl): ?>
i18n.available[] = <?php echo $hl; ?> 
<?php endforeach; ?>
<?php endif; ?>

[testing]

[development]

<?php
$conf = ob_get_contents();
ob_end_clean();

mkdir($tmp = $app . '/configs');
file_put_contents($tmp = $tmp . '/application.ini', $conf);
chmod($tmp, 0777);

echo blue(), " [", green(), "OK", blue(), "]\n", white();


/// Construction du fichier index.php
echo green(), "\nConstruction du fichier index.php...", white();

ob_start();
?>
/**
 * Variables d'environnement
 */
// Chemin d'accès à l'application
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

<?php if (!$config->modules): ?>/*<?php endif; ?> 
// Chemin d'accès aux modules
defined('MODULE_PATH')
    || define('MODULE_PATH', '/modules');
<?php if (!$config->modules): ?>//*/<?php endif; ?> 

// Chemin d'accès à la racine du projet
defined('PROJECT_PATH')
    || define('PROJECT_PATH', realpath(dirname(__FILE__) . '/..'));

// Chemin d'accès au dossier public
defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', PROJECT_PATH . '/public_game');

// Environnement du projet (production, development, testing...)
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

<?php if (!$config->i18n->use): ?>/*<?php endif; ?> 
// Langue utilisée
defined('APPLICATION_LOCALE')
    || define('APPLICATION_LOCALE', (getenv('APPLICATION_LOCALE') ? getenv('APPLICATION_LOCALE') : 'fr'));

// Chemin d'accès aux fichiers de traductions
defined('I18N_PATH') || define('I18N_PATH', APPLICATION_PATH . '/i18n');
<?php if (!$config->i18n->use): ?>//*/<?php endif; ?> 

/**
 * Modification de l'include_path
 */
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(PROJECT_PATH . '/library'),
    get_include_path(),
)));


/**
 * On charge l'application
 */
require 'Core/Application.php';

/**
 * Go !
 */
$application = new Core_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
            ->run();
<?php
$content = ob_get_contents();
ob_end_clean();

mkdir($tmp = PATH . 'public', 0777);
file_put_contents($tmp2 = $tmp . '/index.php', sprintf("<?php\n\n%s\n", $content));
chmod($tmp2, 0777);
touch($tmp2 = $tmp . '/.htaccess');
chmod($tmp2, 0777);

echo blue(), " [", green(), "OK", blue(), "]\n", white();


/// Construction du fichier bootstrap
echo green(), "\nConstruction du fichier Bootstrap.php...", white();

ob_start();
?>
class Bootstrap extends Core_Application_Bootstrap
{
<?php if ($config->layout): ?>
    public function _initLayout()
    {
        return new Core_Layout(
            $this->config->layout['dir'],
            $this->config->layout['file']
        );
    }
<?php endif; ?>

<?php if ($config->i18n->use): ?>
    public function _initI18n()
    {
        $hl = $this->config->i18n['default'];
        if (isset($_GET['hl'])) {
            if (in_array($_GET['hl'], $this->config->i18n['available'])) {
                $hl = $_GET['hl'];
            }
        }

        $i18n = (object) include I18N_PATH . "/$hl.lang.php";

        Core_Registry::set('hl', $hl);
        Core_Registry::set('i18n', $i18n);

        unset($hl);

        return $i18n;
    }
<?php endif; ?>
}
<?php
$content = ob_get_contents();
ob_end_clean();

file_put_contents($tmp2 = $app . '/Bootstrap.php', sprintf("<?php\n\n%s\n", $content));
chmod($tmp2, 0777);

echo blue(), " [", green(), "OK", blue(), "]\n", white();


if ($config->i18n->use) {
    /// Construction de l'internationalisation
    echo green(), "\nConstruction de l'internationalisation...", white();

    mkdir($path = "$app/i18n");
    foreach ($config->i18n->locales as $hl) {
        file_put_contents($tmp = "$path/$hl.lang.php", "<?php return array(\n\n);");
        chmod($tmp, 0777);
    }

    echo blue(), " [", green(), "OK", blue(), "]\n", white();
}

echo "\n\n";
