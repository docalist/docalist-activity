<?php
/**
 * This file is part of Docalist Activity.
 *
 * Copyright (C) 2017-2019 Daniel Ménard
 *
 * For copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Docalist\Activity;

use Docalist\Table\TableManager;
use Docalist\Table\TableInfo;

/**
 * Installation/désinstallation de docalist-activity.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class Installer
{
    /**
     * Activation : enregistre les tables prédéfinies.
     */
    public function activate()
    {
        $tableManager = docalist('table-manager'); /* @var TableManager $tableManager */

        // Enregistre les tables prédéfinies
        foreach ($this->getTables() as $name => $table) {
            $table['name'] = $name;
            $table['path'] = strtr($table['path'], '/', DIRECTORY_SEPARATOR);
            $table['lastupdate'] = date_i18n('Y-m-d H:i:s', filemtime($table['path']));
            $tableManager->register(new TableInfo($table));
        }
    }

    /**
     * Désactivation : supprime les tables prédéfinies.
     */
    public function deactivate()
    {
        $tableManager = docalist('table-manager'); /* @var TableManager $tableManager */

        // Supprime les tables prédéfinies
        foreach (array_keys($this->getTables()) as $table) {
            $tableManager->unregister($table);
        }
    }

    /**
     * Retourne la liste des tables prédéfinies.
     *
     * @return array
     */
    protected function getTables()
    {
        return $this->getTablesForEvent();
    }

    /**
     * Tables spécifiques à l'entité Event.
     *
     * @return array
     */
    protected function getTablesForEvent()
    {
        $dir = DOCALIST_ACTIVITY_DIR . '/tables/event/';

        return [
            'event-event-relation' => [
                'path' => $dir . 'event-event-relation.txt',
                'label' => __("Événement - Relations avec d'autres événements", 'docalist-activity'),
                'format' => 'table',
                'type' => 'relation-type',
                'creation' => '2016-01-12 22:51:01',
            ],
            'event-place-relation' => [
                'path' => $dir . 'event-place-relation.txt',
                'label' => __('Événement - Relations avec des lieux', 'docalist-activity'),
                'format' => 'table',
                'type' => 'relation-type',
                'creation' => '2016-01-12 22:52:06',
            ],
        ];
    }
}
