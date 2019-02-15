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

use Docalist\Data\Entity\ContentEntity;
use Docalist\Activity\Field\Work\NameField;
use Docalist\Activity\Field\Work\ContentField;
use Docalist\Activity\Field\Work\TopicField;
use Docalist\Activity\Field\Work\OrganizationField;
use Docalist\Activity\Field\Work\PersonField;
use Docalist\Activity\Field\Work\NumberField;
use Docalist\Activity\Field\Work\FigureField;
use Docalist\Activity\Field\Work\LinkField;
use Docalist\Data\GridBuilder\EditGridBuilder;
use Docalist\Search\MappingBuilder;

/**
 * Une activité, une mission, un projet, un travail, une production, une œuvre artistique...
 *
 * @property NameField[]            $name           Noms.
 * @property ContentField[]         $content        Présentation.
 * @property TopicField[]           $topic          Mots-clés.
 * @property LinkField[]            $link           Liens
 * @property OrganizationField[]    $organization   Organismes liés.
 * @property PersonField[]          $person         Personnes liées.
 * @property NumberField[]          $number         Numéros officiels.
 * @property FigureField[]          $figure         Chiffres clés.
 *
 * @author Daniel Ménard <daniel.menard@laposte.net>
 */
class WorkEntity extends ContentEntity
{
    /**
     * {@inheritDoc}
     */
    public static function loadSchema()
    {
        return [
            'name' => 'work',
            'label' => __('Activité', 'docalist-activity'),
            'description' => __(
                'Une activité, une mission, un projet, un travail, une production, une œuvre artistique...',
                'docalist-activity'
            ),
            'fields' => [
                'name'          => NameField::class,
                'content'       => ContentField::class,
                'topic'         => TopicField::class,
                'link'          => LinkField::class,
                'organization'  => OrganizationField::class,
                'person'        => PersonField::class,
                'number'        => NumberField::class,
                'figure'        => FigureField::class,
             // 'date'          => DateField::class, pas de champ date ?
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function assign($value)
    {
        // 06/02/19 - gère la compatibilité ascendante avec le site svb
        // dans svb, le champ "figure" s'appellait "figures"
        if (is_array($value) && isset($value['figures'])) {
            $value['figure'] = $value['figures'];
            unset($value['figures']);
        }

        return parent::assign($value);
    }

    /**
     * {@inheritDoc}
     */
    protected function initPostTitle()
    {
        $this->posttitle =
            isset($this->name) && !empty($firstName = $this->name->first()) /** @var NameField $firstName */
            ? $firstName->getFormattedValue(['format' => 'v'])
            : __('(work sans nom)', 'docalist-activity');
    }

    /**
     * {@inheritDoc}
     */
    public static function getEditGrid()
    {
        $builder = new EditGridBuilder(self::class);

        $builder->setProperty('stylesheet', 'docalist-activity-edit-work');

        $builder->addGroups([
            __('Présentation', 'docalist-activity')             => 'name,content,topic,link',
            __('Relations', 'docalist-activity')                => 'organization,person',
            __('Numéros et chiffres clés', 'docalist-activity') => 'number,figure',
            __('Informations de gestion', 'docalist-activity')  => '-type,ref,source',
        ]);

        $builder->setDefaultValues([
            'name'          => [ ['type' => 'usual'], ['type' => 'acronym'] ],
            'content'       => [ ['type' => 'overview'] ],
            'topic'         => [  ],
            'link'          => [ ['type' => 'mail'], ['type' => 'site'], ['type' => 'facebook'] ],
            'organization'  => [ ['type' => 'affiliation'], ['type' => 'member-of'], ['type' => 'partner'] ],
            'person'        => [ ['type' => 'management'], ['type' => 'webmaster'], ['type' => 'contact'] ],
            'number'        => [  ],
            'figure'        => [ ['type' => 'staff'] ],
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

        // Name
        $mapping->addField('name')->text()->suggest()
                ->addTemplate('name-*')->copyFrom('name')->copyDataTo('name');

        // Content
        $mapping->addField('content')->text()
                ->addTemplate('content-*')->copyFrom('content')->copyDataTo('content');

        // Topic
        $mapping->addField('topic')->text()->filter()->suggest()
                ->addTemplate('topic-*')->copyFrom('topic')->copyDataTo('topic');

        // Crée un champ 'hierarchy' pour tous les topics qui sont associés à une table de type thesaurus
        foreach ($this->topic->getThesaurusTopics() as $topic) {
            $mapping->addField("topic-$topic-hierarchy")->text('hierarchy')->setProperty('search_analyzer', 'keyword');
        }

        // Link
        $mapping->addField('link')->url()
                ->addTemplate('link-*')->copyFrom('link')->copyDataTo('link');

        // Organization
        $mapping->addField('organization')->integer()
                ->addTemplate('organization-*')->copyFrom('organization')->copyDataTo('organization');

        // Person
        $mapping->addField('person')->integer()
                ->addTemplate('person-*')->copyFrom('person')->copyDataTo('person');

        // Number
        $mapping->addField('number')->literal()
                ->addTemplate('number-*')->copyFrom('number')->copyDataTo('number');

        // Figures
        $mapping->addField('figure')->decimal()
                ->addTemplate('figure-*')->copyFrom('figure')->copyDataTo('figure');

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

        // Mapping standard pour tous les champs multifield
        $this->mapMultiField($document, 'name');
        $this->mapMultiField($document, 'content');
        $this->mapMultiField($document, 'topic', 'term');
        $this->mapMultiField($document, 'link', 'url');
        $this->mapMultiField($document, 'organization');
        $this->mapMultiField($document, 'person');
        $this->mapMultiField($document, 'number');
        $this->mapMultiField($document, 'figure');

        // Initialise le champ 'hierarchy' pour tous les topics qui sont associés à une table de type thesaurus
        foreach ($this->topic->getThesaurusTopics() as $table => $topic) {
            if (isset($this->topic[$topic])) {
                $terms = $this->topic[$topic]->term->getPhpValue();
                $document["topic-$topic-hierarchy"] = $this->getTermsPath($terms, $table);
            }
        }

        // Ok
        return $document;
    }
}
