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

namespace Docalist\Activity\Field\Work;

use Docalist\Data\Field\TopicField as BaseTopicField;

/**
 * Champ "topic" pour les entités "work".
 *
 * Ce champ permet de saisir des mots-clés provenant de différents vocabulaires pour décrire et classer la structure.
 *
 * Chaque indexation comporte deux sous-champs :
 * - `type` : vocabulaire,
 * - `value` : mots-clés.
 *
 * Le sous-champ type est associé à une table d'autorité qui liste les types d'indexation disponibles
 * ("table:organization-topic" par défaut).
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class TopicField extends BaseTopicField
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema(): array
    {
        return [
            'description' => __("Mots-clés permettant de décrire et de classer l'activité.", 'docalist-activity'),
        ];
    }
}
