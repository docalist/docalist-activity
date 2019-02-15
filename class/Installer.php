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
        return $this->getCommonTables() + $this->getTablesForOrganization() + $this->getTablesForPerson();
    }

    /**
     * Tables communes à différents types d'entités.
     *
     * @return array
     */
    protected function getCommonTables()
    {
        return [];
        $dir = DOCALIST_ACTIVITY_DIR . '/tables/';

        return [
            'name-type' => [
                'path' => $dir . 'name-type.txt',
                'label' => __('Nom - Exemple de table "types de noms"', 'docalist-activity'),
                'format' => 'table',
                'type' => 'name-type',
                'creation' => '2015-12-08 17:04:05',
            ],
        ];
    }

    /**
     * Tables spécifiques à l'entité Organization.
     *
     * @return array
     */
    protected function getTablesForOrganization()
    {
        return [];
    }

    /**
     * Tables spécifiques à l'entité Person.
     *
     * @return array
     */
    protected function getTablesForPerson()
    {
        return [];
    }
}
