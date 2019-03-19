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

namespace Docalist\Activity\Entity;

use Docalist\Activity\Entity\WorkEntity;

use Docalist\Activity\Field\Event\WorkField;
use Docalist\Activity\Field\Event\StartEndField;
use Docalist\Activity\Field\Event\PlaceField;
use Docalist\Activity\Field\Event\EventField;
use Docalist\Activity\Field\Work\NameField;

use Docalist\Type\Collection\DateTimeIntervalCollection;
use Docalist\Data\Type\Collection\TypedRelationCollection;

use Docalist\Data\GridBuilder\EditGridBuilder;
use Docalist\Search\MappingBuilder;

use Docalist\Data\Type\PostalAddress;
use Docalist\People\Entity\PlaceEntity;

use DateTime;

/**
 * Un événement, une réunion, une rencontre, une représentation d'un spectacle...
 *
 * @property WorkField                  $work           Work parent.
 * @property DateTimeIntervalCollection $startend       Date de début et date de fin de l'événement (répétable).
 * @property TypedRelationCollection    $place          Lieux liés.
 * @property TypedRelationCollection    $event          Événements liés.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class EventEntity extends WorkEntity
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema(): array
    {
        return [
            'name' => 'event',
            'label' => __('Événement', 'docalist-activity'),
            'description' => __(
                "Un événement, une réunion, une rencontre, une représentation d'un spectacle...",
                'docalist-activity'
            ),
            'fields' => [
                // Nouveaux champs
                'work' => WorkField::class,
                'startend' => StartEndField::class,
                'place' => PlaceField::class,
                'event' => EventField::class,


                // Champs hérités
                'name' => [
                    'description' => __("Nom de l'événement.", 'docalist-activity'),
                ],

                'content' => [
                    'description' => __("Présentation/description de l'événement.", 'docalist-activity'),
                ],

                'topic' => [
                    'description' => __(
                        "Mots-clés permettant de décrire et de classer l'événement.",
                        'docalist-activity'
                    ),
                ],

                'link'          => [],

                'organization'  => [
                    'description' => __("Structures et organismes liés à l'événement.", 'docalist-activity'),
                ],

                'person'        => [
                    'description' => __("Personnes liées à l'événement.", 'docalist-activity'),
                ],

                'number'        => [],

                'figure'        => [],

                // Champs hérités qui ne sont pas utilisés
                'date'          => [
                    'unused' => true,
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function initPostTitle()
    {
        // Récupère la fiche work parent
        $work = $this->work->getEntity(); /** @var WorkEntity $work */

        // Détermine le nom de l'événement (celui de la fiche event ou celui de la fiche work parent)
        $name = $this->name->first();
        empty($name) && !is_null($work) && $name = $work->name->first();
        $name && $name = $name->value->getPhpValue();
        empty($name) && $name = __('Événement sans nom', 'docalist-activity');

        // Détermine la ou les villes où se déroule l'événement
        $cities = [];
        foreach ($this->place->filterEntities() as $place) { /** @var PlaceEntity $place */
            foreach ($place->address->filterValues() as $address) { /** @var PostalAddress $address */
                $city = $address->locality->getPhpValue();
                !empty($city) && $cities[$city] = $city; // dédoublonne les villes
            }
        }
        $cities = $cities ? implode(' / ', $cities) : __('lieu inconnu', 'docalist-activity');

        // Détermine la date de début et de fin de l'événement
        $start = $this->startend->getStartDate();
        $end = $this->startend->getEndDate();

        // Formatte les dates
        $dates = [];
        !is_null($start) && $dates[] = $start->format('d/m/Y');
        !is_null($end) && ($end !== $start) && $dates[] = $end->format('d/m/Y');
        $dates = $dates ? implode(' - ', array_unique($dates)) : __('date inconnue', 'docalist-activity');

        // Construit le titre du post
        $this->posttitle = sprintf('%s (%s, %s)', $name, $cities, $dates);
    }

    /**
     * {@inheritDoc}
     */
    public static function getEditGrid()
    {
        $builder = new EditGridBuilder(self::class);

        $builder->setProperty('stylesheet', 'docalist-activity-edit-event');

        $builder->addGroups([
            __('Événement', 'docalist-activity')                        => 'work,startend,place,event',
            __('Présentation', 'docalist-activity')                     => 'name,content,topic,link',
            __('Relations', 'docalist-activity')                        => 'organization,person',
            __('Numéros et chiffres clés', 'docalist-activity')         => 'number,figure',
            __('Informations de gestion', 'docalist-activity')          => '-type,ref,source',
        ]);

        $builder->setDefaultValues([
            'work'          => null,
            'startend'      => [],
            'place'         => [],
            'event'         => [],
            'name'          => [ ['type' => 'usual'] ],
            'content'       => [ ['type' => 'overview'] ],
            'topic'         => [],
            'link'          => [ ['type' => 'mail'], ['type' => 'site'], ['type' => 'facebook'] ],
            'organization'  => [],
            'person'        => [],
            'number'        => [],
            'figure'        => [],
        ]);

        return $builder->getGrid();
    }

    /**
     * {@inheritDoc}
     */
    protected function buildMapping(MappingBuilder $mapping)
    {
        // Le mapping des champs de base est construit par la classe parent
        $mapping = parent::buildMapping($mapping);

        // Work
        $mapping->addField('work')->integer();

        // StartEnd
        $mapping->addField('startend')
            ->nested()
                ->addField('start')->dateTime()
                ->addField('end')->dateTime()
            ->done();

        // startdate = plus petite des dates de début de startend
        $mapping->addField('startdate')->dateTime();
        $mapping->addField('startdate-hierarchy')->hierarchy();

        // enddate = plus grande des dates de début de startend
        $mapping->addField('enddate')->dateTime();

        // Place
        $mapping->addField('place')->integer()
            ->addTemplate('place-*')->copyFrom('place')->copyDataTo('place');

        // Event
        $mapping->addField('event')->integer()
            ->addTemplate('event-*')->copyFrom('event')->copyDataTo('event');

        // Ok
        return $mapping;
    }

    /**
     * {@inheritDoc}
     */
    public function map()
    {
        // Le mapping des champs de base est fait par la classe parent
        $document = parent::map();

        // Work parent
        isset($this->work) && $document['work'] = $this->work->getPhpValue();

        // Champs fusionnés avec ceux du work parent
        if ($work = $this->work->getEntity()) { /** @var WorkEntity $work */
            // $work->mapMultiField($document, 'name');
            // $work->mapMultiField($document, 'content');
            $work->mapMultiField($document, 'topic', 'term');
            $work->mapMultiField($document, 'organization');
            $work->mapMultiField($document, 'person');
            // $work->mapMultiField($document, 'figures');
            // $work->mapMultiField($document, 'number');
            // $work->mapMultiField($document, 'link', 'url');

            // Indexation hiérarchique des topics du work parent avec fusion de ceux qui sont spécifiques à l'event
            foreach ($work->topic->getThesaurusTopics() as $table => $topic) {
                if (isset($work->topic[$topic])) {
                    $terms = $work->topic[$topic]->term->getPhpValue();
                    $terms = $work->getTermsPath($terms, $table);

                    if (isset($document["topic-$topic-hierarchy"])) {
                        $document["topic-$topic-hierarchy"] = array_values(array_unique(array_merge(
                            $terms,
                            $document["topic-$topic-hierarchy"]
                        )));
                        // remarque : array_values est nécessaire car array_unique conserve les clés.
                        // si on a un work avec [a] et un event avec [a,b], array_unique(array_merge(work,event))
                        // nous retourne [0 => a, 2 => b]. Comme les clés ne sont pas uniques, quand on envoie ça
                        // en JSON à elasticsearch, c'est un objet qui est généré et non pas un tableau et on
                        // obtient une erreur.
                    } else {
                        $document["topic-$topic-hierarchy"] = $terms;
                    }
                }
            }
        }

        // StartEnd
        if (isset($this->startend)) {
            foreach ($this->startend as $date) { /** @var StartEndField $date */
                if (isset($date->start)) {
                    $t = [];
                    $t['start'] = $date->start->getPhpValue();
                    $t['end'] = isset($date->end) ? $date->end->getPhpValue() : $t['start'];
                    $document['startend'][] = $t;
                }
            }
        }

        // startdate = plus petite des dates de début de startend
        $start = $this->startend->getStartDate(); /** @var DateTime|null $start (dans DateTimeIntervalCollection) */
        $start && $document['startdate'] = $start->format('Y-m-d H:i:s');

        // enddate = plus grande des dates de début de startend
        $end = $this->startend->getEndDate(); /** @var DateTime|null $start (dans DateTimeIntervalCollection) */
        $end && $document['enddate'] = $end->format('Y-m-d H:i:s');

        // startdate hiérarchique (pour la facette, cf. svb#381)
        $start && $document['startdate-hierarchy'] = $start->format('Y'); // Que l'année pour le moment

        // Place
        $this->mapMultiField($document, 'place');

        // Event
        $this->mapMultiField($document, 'event');

        // Address (geoloc) : on prend toutes les adresses qui figurent dans les fiches "place" liées à l'event
        if (isset($this->place)) {
            $geoloc = [];
            foreach ($this->place as $place) {
                if (is_null($place = $place->getEntity())) { /** @var PlaceEntity $place */
                    continue;
                }
                foreach ($place->address as $address) {
                    $geoloc[] = $address->value->getContinentAndCountry();
                }
            }
            if ($geoloc) {
                $geoloc = array_values(array_unique($geoloc)); // dédoublonne + renumérote
                $document['geoloc-hierarchy'] = $geoloc;
            }
        }

        // Ok
        return $document;
    }
}
