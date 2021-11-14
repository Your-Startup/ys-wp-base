<?php

define('HOME_URL', get_home_url());
define('THEME_DIR_URI', get_template_directory_uri());
define('CURRENT_USER_ID', get_current_user_id());

const THEME_DIR   = __DIR__;
const THEME_INC   = THEME_DIR . '/inc';
const TEMPLATE    = THEME_DIR . '/template-parts';
const DIST        = THEME_DIR_URI . '/dist';
const BASE_DIR    = THEME_DIR . '/base';
const PLUGINS_DIR = THEME_DIR . '/plugins';

header('Content-Type: text/html; charset=utf-8');

// Автоподгрузка классов
require_once __DIR__ . '/vendor/autoload.php';

require_once THEME_DIR . '/core/bootstrap.php';
//require_once THEME_DIR . '/inc/bootstrap.php';

//require_once PLUGINS_DIR . '/loader.php';

spl_autoload_register(function ($class) {
    $prefix = 'YS\\';

    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = str_replace('\\', '/', substr($class, $len));
    $file          = THEME_INC . '/classes/' . $relativeClass . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

//require_once BASE_DIR . '/inc.php';

loadFiles(THEME_INC . '/hooks');
loadFiles(THEME_INC . '/admin-hooks');

/**
 * Автоматическое подключение файлов с функциями из директории.
 *
 * @param $path
 * @param bool $recursive
 * @param array $exclude
 */
function loadFiles($path, bool $recursive = false, array $exclude = [])
{
    $path = glob($path . '/*' . ($recursive ? '' : '.php'));

    if (!is_array($path)) {
        return;
    }

    foreach ($path as $file) {
        if (is_dir($file) && $recursive) {
            loadFiles($file, $recursive);
            continue;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
            continue;
        }

        if (in_array(pathinfo($file, PATHINFO_FILENAME), $exclude, true)) {
            continue;
        }

        require_once($file);
    }
}
