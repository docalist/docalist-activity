<?php
/**
 * This file is part of Docalist Activity.
 *
 * Copyright (C) 2017-2019 Daniel Ménard
 *
 * For copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 *
 * Plugin Name: Docalist Activity
 * Plugin URI:  http://docalist.org/
 * Description: Entités "work" et "event" pour docalist.
 * Version:     2.0.0
 * Author:      Daniel Ménard
 * Author URI:  http://docalist.org/
 * Text Domain: docalist-activity
 * Domain Path: /languages
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
declare(strict_types=1);

namespace Docalist\Activity;

use Docalist\Activity\Plugin;
use Docalist\Activity\Installer;

/**
 * Version du plugin.
 */
define('DOCALIST_ACTIVITY_VERSION', '2.0.0'); // Garder synchro avec la version indiquée dans l'entête

/**
 * Path absolu du répertoire dans lequel le plugin est installé.
 *
 * Par défaut, on utilise la constante magique __DIR__ qui retourne le path réel du répertoire et résoud les liens
 * symboliques.
 *
 * Si le répertoire du plugin est un lien symbolique, la constante doit être définie manuellement dans le fichier
 * wp_config.php et pointer sur le lien symbolique et non sur le répertoire réel.
 */
!defined('DOCALIST_ACTIVITY_DIR') && define('DOCALIST_ACTIVITY_DIR', __DIR__);

/**
 * Path absolu du fichier principal du plugin.
 */
define('DOCALIST_ACTIVITY', DOCALIST_ACTIVITY_DIR . DIRECTORY_SEPARATOR . basename(__FILE__));

/**
 * Url de base du plugin.
 */
define('DOCALIST_ACTIVITY_URL', plugins_url('', DOCALIST_ACTIVITY));

/**
 * Initialise le plugin.
 */
add_action('plugins_loaded', function () {
    // Auto désactivation si les plugins dont on a besoin ne sont pas activés
    $dependencies = ['DOCALIST_PEOPLE'];
    foreach ($dependencies as $dependency) {
        if (! defined($dependency)) {
            return add_action('admin_notices', function () use ($dependency) {
                deactivate_plugins(DOCALIST_ACTIVITY);
                unset($_GET['activate']); // empêche wp d'afficher "extension activée"
                printf(
                    '<div class="%s"><p><b>%s</b> has been deactivated because it requires <b>%s</b>.</p></div>',
                    'notice notice-error is-dismissible',
                    'Docalist Activity',
                    ucwords(strtolower(strtr($dependency, '_', ' ')))
                );
            });
        }
    }

    // Ok
    docalist('autoloader')
        ->add(__NAMESPACE__, __DIR__ . '/class')
        ->add(__NAMESPACE__ . '\Tests', __DIR__ . '/tests');

    docalist('services')->add('docalist-activity', new Plugin());
});

/*
 * Activation du plugin.
 */
register_activation_hook(DOCALIST_ACTIVITY, function () {
    // Si docalist-core n'est pas dispo, on ne peut rien faire
    if (defined('DOCALIST_CORE')) {
        // plugins_loaded n'a pas encore été lancé, donc il faut initialiser notre autoloader
        docalist('autoloader')->add(__NAMESPACE__, __DIR__ . '/class');
        (new Installer())->activate();
    }
});

/*
 * Désactivation du plugin.
*/
register_deactivation_hook(DOCALIST_ACTIVITY, function () {
    // Si docalist-core n'est pas dispo, on ne peut rien faire
    if (defined('DOCALIST_CORE')) {
        docalist('autoloader')->add(__NAMESPACE__, __DIR__ . '/class');
        (new Installer())->deactivate();
    }
});
