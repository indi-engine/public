<?php
class Admin_MigrationController extends Indi_Controller {
    public function syncSectionsAction() {
        section('front', ['title' => 'Фронтенд', 'move' => '', 'type' => 'o']);
        section('fsections', [
            'sectionId' => 'front',
            'entityId' => 'fsection',
            'title' => 'Разделы',
            'move' => '',
            'defaultSortField' => 'move',
            'type' => 'o',
            'roleIds' => '1',
        ])->nested('grid')->delete();
        section2action('fsections','index', ['move' => '', 'profileIds' => '1']);
        section2action('fsections','form', ['move' => 'index', 'profileIds' => '1']);
        section2action('fsections','save', ['move' => 'form', 'profileIds' => '1']);
        section2action('fsections','delete', ['move' => 'save', 'profileIds' => '1']);
        section2action('fsections','toggle', ['move' => 'delete', 'profileIds' => '1']);
        section2action('fsections','up', ['move' => 'toggle', 'profileIds' => '1']);
        section2action('fsections','down', ['move' => 'up', 'profileIds' => '1']);
        grid('fsections', 'title', ['move' => '']);
        grid('fsections', 'alias', ['move' => 'title']);
        grid('fsections', 'entityId', ['move' => 'alias']);
        grid('fsections', 'type', ['move' => 'entityId']);
        grid('fsections', 'toggle', ['move' => 'type']);
        grid('fsections', 'move', ['move' => 'toggle']);
        section('fsection2factions', [
            'sectionId' => 'fsections',
            'entityId' => 'fsection2faction',
            'title' => 'Действия',
            'move' => '',
            'defaultSortField' => 'factionId',
            'type' => 'o',
            'roleIds' => '1',
        ])->nested('grid')->delete();
        section2action('fsection2factions','index', ['move' => '', 'profileIds' => '1']);
        section2action('fsection2factions','form', ['move' => 'index', 'profileIds' => '1']);
        section2action('fsection2factions','save', ['move' => 'form', 'profileIds' => '1']);
        section2action('fsection2factions','delete', ['move' => 'save', 'profileIds' => '1']);
        grid('fsection2factions', 'factionId', ['move' => '']);
        grid('fsection2factions', 'type', ['move' => 'factionId']);
        section('seoUrl', [
            'sectionId' => 'fsection2factions',
            'entityId' => 'url',
            'title' => 'Компоненты SEO-урла',
            'move' => '',
            'defaultSortField' => 'move',
            'type' => 'o',
            'roleIds' => '1',
        ])->nested('grid')->delete();
        section2action('seoUrl','index', ['move' => '', 'profileIds' => '1']);
        section2action('seoUrl','form', ['move' => 'index', 'profileIds' => '1']);
        section2action('seoUrl','save', ['move' => 'form', 'profileIds' => '1']);
        section2action('seoUrl','delete', ['move' => 'save', 'profileIds' => '1']);
        section2action('seoUrl','up', ['move' => 'delete', 'profileIds' => '1']);
        section2action('seoUrl','down', ['move' => 'up', 'profileIds' => '1']);
        grid('seoUrl', 'entityId', ['move' => '']);
        grid('seoUrl', 'move', ['move' => 'entityId']);
        grid('seoUrl', 'prefix', ['move' => 'move']);
        section('metatitles', [
            'sectionId' => 'fsection2factions',
            'entityId' => 'metatag',
            'title' => 'Компоненты мета-тегов',
            'move' => 'seoUrl',
            'extendsPhp' => 'Indi_Controller_Admin_Meta',
            'defaultSortField' => 'move',
            'type' => 'o',
            'groupBy' => 'tag',
            'rowsetSeparate' => 'yes',
            'roleIds' => '1',
        ])->nested('grid')->delete();
        section2action('metatitles','index', ['move' => '', 'profileIds' => '1']);
        section2action('metatitles','form', ['move' => 'index', 'profileIds' => '1']);
        section2action('metatitles','save', ['move' => 'form', 'profileIds' => '1']);
        section2action('metatitles','delete', ['move' => 'save', 'profileIds' => '1']);
        section2action('metatitles','up', ['move' => 'delete', 'profileIds' => '1']);
        section2action('metatitles','down', ['move' => 'up', 'profileIds' => '1']);
        grid('metatitles', 'type', ['move' => '']);
        grid('metatitles', 'move', ['move' => 'type']);
        grid('metatitles', 'tag', ['move' => 'move']);
        grid('metatitles', 'component', ['move' => 'tag']);
        grid('metatitles', 'prefix', ['move' => '', 'gridId' => 'component', 'editor' => 1]);
        grid('metatitles', 'content', ['move' => 'prefix', 'alterTitle' => 'Указанный вручную', 'gridId' => 'component']);
        grid('metatitles', 'context', ['move' => 'content', 'gridId' => 'component']);
        grid('metatitles', 'up', ['move' => '', 'alterTitle' => 'Уровень', 'gridId' => 'context']);
        grid('metatitles', 'source', ['move' => 'up', 'gridId' => 'context']);
        grid('metatitles', 'fieldId', ['move' => 'source', 'gridId' => 'context']);
        grid('metatitles', 'postfix', ['move' => 'context', 'gridId' => 'component', 'editor' => 1]);
        alteredField('metatitles', 'move', ['mode' => 'hidden']);
        alteredField('metatitles', 'fsectionId', ['mode' => 'readonly']);
        alteredField('metatitles', 'fsection2factionId', ['mode' => 'readonly']);
        section('factions', [
            'sectionId' => 'front',
            'entityId' => 'faction',
            'title' => 'Действия',
            'move' => 'fsections',
            'defaultSortField' => 'title',
            'type' => 'o',
            'roleIds' => '1',
        ])->nested('grid')->delete();
        section2action('factions','index', ['move' => '', 'profileIds' => '1']);
        section2action('factions','form', ['move' => 'index', 'profileIds' => '1']);
        section2action('factions','save', ['move' => 'form', 'profileIds' => '1']);
        section2action('factions','delete', ['move' => 'save', 'profileIds' => '1']);
        grid('factions', 'title', ['move' => '']);
        grid('factions', 'alias', ['move' => 'title']);
        grid('factions', 'maintenance', ['move' => 'alias']);
        grid('factions', 'type', ['move' => 'maintenance']);
        die('ok');
    }
    public function syncEntitiesAction() {
        entity('fsection', ['title' => 'Раздел фронтенда', 'system' => 'o', 'useCache' => '1']);
        field('fsection', 'fsectionId', [
            'title' => 'Вышестоящий раздел',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'radio',
            'defaultValue' => '0',
            'move' => '',
            'relation' => 'fsection',
            'storeRelationAbility' => 'one',
        ]);
        field('fsection', 'title', [
            'title' => 'Наименование',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'fsectionId',
        ]);
        field('fsection', 'alias', [
            'title' => 'Псевдоним',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'title',
        ]);
        field('fsection', 'entityId', [
            'title' => 'Прикрепленная сущность',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'alias',
            'relation' => 'entity',
            'storeRelationAbility' => 'one',
        ]);
        field('fsection', 'sectionId', [
            'title' => 'Соответствующий раздел бэкенда',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'entityId',
            'relation' => 'section',
            'storeRelationAbility' => 'one',
        ]);
        field('fsection', 'type', [
            'title' => 'Тип',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'r',
            'move' => 'sectionId',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('fsection', 'type', 'r', ['title' => 'Обычный', 'move' => '']);
        enumset('fsection', 'type', 's', ['title' => 'Одностроковый', 'move' => 'r']);
        field('fsection', 'filter', [
            'title' => 'Статическая фильтрация',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'type',
        ]);
        field('fsection', 'parentSectionConnector', [
            'title' => 'Связь с вышестоящим разделом по полю',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'filter',
            'relation' => 'field',
            'storeRelationAbility' => 'one',
        ]);
        consider('fsection', 'parentSectionConnector', 'fsectionId', ['foreign' => 'entityId', 'required' => 'y']);
        field('fsection', 'move', [
            'title' => 'Порядок отображения соответствующего пункта в меню',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'move',
            'defaultValue' => '0',
            'move' => 'parentSectionConnector',
        ]);
        field('fsection', 'defaultLimit', [
            'title' => 'Количество строк для отображения по умолчанию',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '20',
            'move' => 'move',
        ]);
        field('fsection', 'orderBy', [
            'title' => 'По умолчанию сортировка по',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'c',
            'move' => 'defaultLimit',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('fsection', 'orderBy', 'c', ['title' => 'Одному из имеющихся столбцов', 'move' => '']);
        enumset('fsection', 'orderBy', 'e', ['title' => 'SQL-выражению', 'move' => 'c']);
        field('fsection', 'orderColumn', [
            'title' => 'Столбец сортировки',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'orderBy',
            'relation' => 'field',
            'storeRelationAbility' => 'one',
        ]);
        consider('fsection', 'orderColumn', 'entityId', ['required' => 'y']);
        field('fsection', 'orderDirection', [
            'title' => 'Направление сортировки',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'ASC',
            'move' => 'orderColumn',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('fsection', 'orderDirection', 'ASC', ['title' => 'По возрастанию', 'move' => '']);
        enumset('fsection', 'orderDirection', 'DESC', ['title' => 'По убыванию', 'move' => 'ASC']);
        field('fsection', 'orderExpression', [
            'title' => 'SQL-выражение',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'orderDirection',
        ]);
        field('fsection', 'where', [
            'title' => 'Где брать идентификатор',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'orderExpression',
        ]);
        field('fsection', 'index', [
            'title' => 'Действие по умолчанию',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'where',
        ]);
        field('fsection', 'toggle', [
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'y',
            'move' => 'index',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('fsection', 'toggle', 'y', ['title' => '<span class="i-color-box" style="background: lime;"></span>Включен', 'move' => '']);
        enumset('fsection', 'toggle', 'n', ['title' => '<span class="i-color-box" style="background: red;"></span>Выключен', 'move' => 'y']);
        field('fsection', 'extends', [
            'title' => 'От какого класса наследовать класс контроллера',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'toggle',
        ]);
        entity('fsection', ['titleFieldId' => 'title']);

        entity('faction', ['title' => 'Действие, возможное для использования в разделе фронтенда', 'system' => 'o', 'useCache' => '1']);
        field('faction', 'title', [
            'title' => 'Наименование',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => '',
        ]);
        field('faction', 'alias', [
            'title' => 'Псевдоним',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'title',
        ]);
        field('faction', 'maintenance', [
            'title' => 'Выполнять maintenance()',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'r',
            'move' => 'alias',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('faction', 'maintenance', 'r', ['title' => 'Над записью', 'move' => '']);
        enumset('faction', 'maintenance', 'rs', ['title' => 'Над набором записей', 'move' => 'r']);
        enumset('faction', 'maintenance', 'n', ['title' => 'Только независимые множества, если нужно', 'move' => 'rs']);
        field('faction', 'type', [
            'title' => 'Тип',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'p',
            'move' => 'maintenance',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('faction', 'type', 'p', ['title' => 'Проектное', 'move' => '']);
        enumset('faction', 'type', 's', ['title' => '<font color=red>Системное</font>', 'move' => 'p']);
        enumset('faction', 'type', 'o', ['title' => '<font color=lime>Типовое</font>', 'move' => 's']);
        entity('faction', ['titleFieldId' => 'title']);

        entity('fsection2faction', ['title' => 'Действие в разделе фронтенда', 'system' => 'o', 'useCache' => '1']);
        field('fsection2faction', 'fsectionId', [
            'title' => 'Раздел фронтенда',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => '',
            'relation' => 'fsection',
            'storeRelationAbility' => 'one',
        ]);
        field('fsection2faction', 'factionId', [
            'title' => 'Действие',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'fsectionId',
            'relation' => 'faction',
            'storeRelationAbility' => 'one',
        ]);
        field('fsection2faction', 'type', [
            'title' => 'Тип',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'r',
            'move' => 'factionId',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('fsection2faction', 'type', 'r', ['title' => 'Обычное', 'move' => '']);
        enumset('fsection2faction', 'type', 'j', ['title' => 'Для jQuery.post()', 'move' => 'r']);
        field('fsection2faction', 'seoSettings', ['title' => 'Настройки SEO', 'elementId' => 'span', 'move' => 'type']);
        field('fsection2faction', 'blink', [
            'title' => 'Не указывать действие при создании seo-урлов из системных',
            'columnTypeId' => 'BOOLEAN',
            'elementId' => 'check',
            'defaultValue' => '0',
            'move' => 'seoSettings',
        ]);
        field('fsection2faction', 'rename', [
            'title' => 'Переименовать действие при генерации seo-урла',
            'columnTypeId' => 'BOOLEAN',
            'elementId' => 'check',
            'defaultValue' => '0',
            'move' => 'blink',
        ]);
        field('fsection2faction', 'alias', [
            'title' => 'Псевдоним',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'rename',
        ]);
        field('fsection2faction', 'title', [
            'title' => 'Auto title',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'alias',
            'mode' => 'hidden',
        ]);
        consider('fsection2faction', 'title', 'factionId', ['foreign' => 'title', 'required' => 'y']);
        field('fsection2faction', 'allowNoid', [
            'title' => 'Разрешено не передавать id в uri',
            'columnTypeId' => 'BOOLEAN',
            'elementId' => 'check',
            'defaultValue' => '0',
            'move' => 'title',
        ]);
        field('fsection2faction', 'row', [
            'title' => 'Над записью',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'existing',
            'move' => 'allowNoid',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('fsection2faction', 'row', 'existing', ['title' => 'Существующей', 'move' => '']);
        enumset('fsection2faction', 'row', 'new', ['title' => 'Новой', 'move' => 'existing']);
        enumset('fsection2faction', 'row', 'any', ['title' => 'Любой', 'move' => 'new']);
        field('fsection2faction', 'where', [
            'title' => 'Где брать идентификатор',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'row',
        ]);
        entity('fsection2faction', ['titleFieldId' => 'factionId']);

        entity('url', ['title' => 'Компонент SEO-урла', 'system' => 'o']);
        field('url', 'fsectionId', [
            'title' => 'Раздел фронтенда',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => '',
            'relation' => 'fsection',
            'storeRelationAbility' => 'one',
        ]);
        field('url', 'fsection2factionId', [
            'title' => 'Действие в разделе фронтенда',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'fsectionId',
            'relation' => 'fsection2faction',
            'storeRelationAbility' => 'one',
        ]);
        consider('url', 'fsection2factionId', 'fsectionId', ['required' => 'y']);
        field('url', 'entityId', [
            'title' => 'Компонент',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'fsection2factionId',
            'relation' => 'entity',
            'storeRelationAbility' => 'one',
        ]);
        field('url', 'move', [
            'title' => 'Очередность',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'move',
            'defaultValue' => '0',
            'move' => 'entityId',
        ]);
        field('url', 'prefix', [
            'title' => 'Префикс',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'move',
        ]);
        entity('metatag', ['title' => 'Компонент содержимого meta-тега', 'system' => 'o']);
        field('metatag', 'fsectionId', [
            'title' => 'Раздел',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => '',
            'relation' => 'fsection',
            'storeRelationAbility' => 'one',
        ]);
        field('metatag', 'fsection2factionId', [
            'title' => 'Действие',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'fsectionId',
            'relation' => 'fsection2faction',
            'storeRelationAbility' => 'one',
        ]);
        consider('metatag', 'fsection2factionId', 'fsectionId', ['required' => 'y']);
        field('metatag', 'tag', [
            'title' => 'Тэг',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'title',
            'move' => 'fsection2factionId',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('metatag', 'tag', 'title', ['title' => 'Title', 'move' => '']);
        enumset('metatag', 'tag', 'keywords', ['title' => 'Keywords', 'move' => 'title']);
        enumset('metatag', 'tag', 'description', ['title' => 'Description', 'move' => 'keywords']);
        field('metatag', 'type', [
            'title' => 'Тип компонента',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'static',
            'move' => 'tag',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('metatag', 'type', 'static', ['title' => 'Статический', 'move' => '']);
        enumset('metatag', 'type', 'dynamic', ['title' => 'Динамический', 'move' => 'static']);
        field('metatag', 'content', [
            'title' => 'Указанный вручную',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'type',
        ]);
        field('metatag', 'up', [
            'title' => 'Шагов вверх',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'move' => 'content',
        ]);
        field('metatag', 'source', [
            'title' => 'Источник',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'section',
            'move' => 'up',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ]);
        enumset('metatag', 'source', 'section', ['title' => 'Раздел', 'move' => '']);
        enumset('metatag', 'source', 'action', ['title' => 'Действие', 'move' => 'section']);
        enumset('metatag', 'source', 'row', ['title' => 'Запись', 'move' => 'action']);
        field('metatag', 'entityId', [
            'title' => 'Сущность',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'source',
            'relation' => 'entity',
            'storeRelationAbility' => 'one',
            'filter' => '`id` IN (<?=$this->foreign(\'fsectionId\')->entityRoute(true)?>)',
        ]);
        field('metatag', 'fieldId', [
            'title' => 'Поле',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'move' => 'entityId',
            'relation' => 'field',
            'storeRelationAbility' => 'one',
        ]);
        consider('metatag', 'fieldId', 'entityId', ['required' => 'y']);
        field('metatag', 'prefix', [
            'title' => 'Префикс',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'fieldId',
        ]);
        field('metatag', 'postfix', [
            'title' => 'Постфикс',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'move' => 'prefix',
        ]);
        field('metatag', 'move', [
            'title' => 'Порядок отображения',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'move',
            'defaultValue' => '0',
            'move' => 'postfix',
        ]);
        field('metatag', 'context', [
            'title' => 'Взятый из контекста',
            'elementId' => 'span',
            'move' => 'move',
            'mode' => 'hidden',
        ]);
        field('metatag', 'component', [
            'title' => 'Компонент',
            'elementId' => 'span',
            'move' => 'context',
            'mode' => 'hidden',
        ]);
        die('ok');
    }
    public function metatagAction() {

        if (section('metakeywords')) section('metakeywords')->delete();
        if (section('metadescription')) section('metadescription')->delete();
        field('grid', 'alias', array('title' => 'Ключ', 'elementId' => 'string', 'columnTypeId' => 'VARCHAR(255)'))->move(15)->move(-3);
        if (!entity('metatag') || field('metatag', 'fieldId')->title == 'Свойство') {

            if (!entity('metatag')) {
                entity('metatag', array (
                    'title' => 'Компонент содержимого meta-тега',
                    'system' => 'o',
                ));
                field('metatag', 'fsectionId', array (
                    'title' => 'Раздел',
                    'columnTypeId' => 'INT(11)',
                    'elementId' => 'combo',
                    'relation' => 'fsection',
                    'storeRelationAbility' => 'one',
                    'mode' => 'required',
                ));
                field('metatag', 'fsection2factionId', array (
                    'title' => 'Действие',
                    'columnTypeId' => 'INT(11)',
                    'elementId' => 'combo',
                    'relation' => 'fsection2faction',
                    'satellite' => 'fsectionId',
                    'dependency' => 'с',
                    'storeRelationAbility' => 'one',
                    'mode' => 'required',
                ));
                field('metatag', 'tag', array (
                    'title' => 'Тэг',
                    'columnTypeId' => 'ENUM',
                    'elementId' => 'combo',
                    'defaultValue' => 'title',
                    'relation' => 'enumset',
                    'storeRelationAbility' => 'one',
                ));
                enumset('metatag', 'tag', 'title', array('title' => 'Title'));
                enumset('metatag', 'tag', 'keywords', array('title' => 'Keywords'));
                enumset('metatag', 'tag', 'description', array('title' => 'Description'));
                field('metatag', 'type', array (
                    'title' => 'Тип компонента',
                    'columnTypeId' => 'ENUM',
                    'elementId' => 'combo',
                    'defaultValue' => 'static',
                    'relation' => 'enumset',
                    'storeRelationAbility' => 'one',
                ));
                enumset('metatag', 'type', 'static', array('title' => 'Указанный вручную'));
                enumset('metatag', 'type', 'dynamic', array('title' => 'Взятый из контекста'));
                field('metatag', 'content', array (
                    'title' => 'Указанный вручную',
                    'columnTypeId' => 'VARCHAR(255)',
                    'elementId' => 'string',
                ));
                field('metatag', 'up', array (
                    'title' => 'Шагов вверх',
                    'columnTypeId' => 'INT(11)',
                    'elementId' => 'number',
                ));
                field('metatag', 'source', array (
                    'title' => 'Источник',
                    'columnTypeId' => 'ENUM',
                    'elementId' => 'combo',
                    'defaultValue' => 'section',
                    'relation' => 'enumset',
                    'storeRelationAbility' => 'one',
                ));
                enumset('metatag', 'source', 'section', array('title' => 'Раздел'));
                enumset('metatag', 'source', 'action', array('title' => 'Действие'));
                enumset('metatag', 'source', 'row', array('title' => 'Запись'));
                field('metatag', 'entityId', array (
                    'title' => 'Сущность',
                    'columnTypeId' => 'INT(11)',
                    'elementId' => 'combo',
                    'relation' => 'entity',
                    'storeRelationAbility' => 'one',
                    'filter' => '`id` IN (<?=$this->foreign(\'fsectionId\')->entityRoute(true)?>)',
                ));
                field('metatag', 'fieldId', array (
                    'title' => 'Поле',
                    'columnTypeId' => 'INT(11)',
                    'elementId' => 'combo',
                    'relation' => 'field',
                    'satellite' => 'entityId',
                    'dependency' => 'с',
                    'storeRelationAbility' => 'one',
                ));
                field('metatag', 'prefix', array (
                    'title' => 'Префикс',
                    'columnTypeId' => 'VARCHAR(255)',
                    'elementId' => 'string',
                ));
                field('metatag', 'postfix', array (
                    'title' => 'Постфикс',
                    'columnTypeId' => 'VARCHAR(255)',
                    'elementId' => 'string',
                ));
                field('metatag', 'move', array (
                    'title' => 'Порядок отображения',
                    'columnTypeId' => 'INT(11)',
                    'elementId' => 'move',
                ));
            } else {
                field('metatag', 'fsectionId', array('mode' => 'required'));
                field('metatag', 'fsection2factionId', array('mode' => 'required'));
                field('metatag', 'content', array('title' => 'Указанный вручную'));
                field('metatag', 'fieldId', array('title' => 'Поле'));
                field('metatag', 'move', array('elementId' => 'move'));
                enumset('metatag', 'tag', 'title', array('title' => 'Title'));
                enumset('metatag', 'tag', 'keywords', array('title' => 'Keywords'));
                enumset('metatag', 'tag', 'description', array('title' => 'Description'));
            }

            if (section('metatitles')) {
                section('metatitles', array(
                    'title'=> 'Компоненты meta-тегов',
                    'filter' => '',
                    'groupBy' => 'tag',
                    'rowsetSeparate' => 'yes'
                ));
                grid('grid', 'title', array('toggle' => 'y'));
                section2action('metatitles', 'index', array('fitWindow' => 'n'));
                section('metatitles')->nested('grid')->delete();
                section('metatitles')->nested('alteredField')->delete();
                grid('metatitles', 'type', true);
                grid('metatitles', 'move', true);
                grid('metatitles', 'tag', true);
                grid('metatitles', 'component', array('alterTitle' => 'Компонент'));
                    grid('metatitles', 'prefix', array('editor' => '1', 'gridId' => 'component'));
                    grid('metatitles', 'content', array('gridId' => 'component'));
                    grid('metatitles', 'context', array('alterTitle' => 'Взятый из контекста', 'gridId' => 'component'));
                        grid('metatitles', 'up', array('alterTitle' => 'Уровень', 'gridId' => 'context'));
                        grid('metatitles', 'source', array('gridId' => 'context'));
                        grid('metatitles', 'fieldId', array('gridId' => 'context'));
                    grid('metatitles', 'postfix', array('editor' => '1', 'gridId' => 'component'));
            } else {
                section('metatitles', array (
                    'sectionId' => 'fsection2factions',
                    'entityId' => 'metatag',
                    'title' => 'Компоненты мета-тегов',
                    'extends' => 'Indi_Controller_Admin_Meta',
                    'defaultSortField' => 'move',
                    'type' => 'o',
                    'groupBy' => 'tag',
                    'rowsetSeparate' => 'yes',
                    'expand' => 'all',
                ));
                section('metatitles')->nested('grid')->delete();
                section2action('metatitles','index', array (
                    'profileIds' => '1',
                    'fitWindow' => 'n',
                ));
                section2action('metatitles','form', array('profileIds' => '1'));
                section2action('metatitles','save', array('profileIds' => '1'));
                section2action('metatitles','delete', array('profileIds' => '1'));
                section2action('metatitles','up', array('profileIds' => '1'));
                section2action('metatitles','down', array('profileIds' => '1'));
                grid('metatitles','type', true);
                grid('metatitles','move', true);
                grid('metatitles','tag', true);
                grid('metatitles','component', array('alterTitle' => 'Компонент'));
                grid('metatitles','prefix', array (
                    'gridId' => 'component',
                    'editor' => 1,
                ));
                grid('metatitles','content', array('gridId' => 'component'));
                grid('metatitles','context', array (
                    'alterTitle' => 'Взятый из контекста',
                    'gridId' => 'component',
                ));
                grid('metatitles','up', array (
                    'alterTitle' => 'Уровень',
                    'tooltip' => 'Количество шагов вверх по иерархии разделов',
                    'gridId' => 'context',
                ));
                grid('metatitles','source', array('gridId' => 'context'));
                grid('metatitles','fieldId', array('gridId' => 'context'));
                grid('metatitles','postfix', array (
                    'gridId' => 'component',
                    'editor' => 1,
                ));
                alteredField('metatitles', 'move', array('mode' => 'hidden'));
                alteredField('metatitles', 'fsectionId', array('mode' => 'readonly'));
                alteredField('metatitles', 'fsection2factionId', array('mode' => 'readonly'));
            }
        }
        die('ok');
    }
    public function othersAction() {

        // Other things
        entity('faction', array('system' => 'o'));
        entity('url', array('system' => 'o'));
        entity('metatag', array('system' => 'o'));
        entity('fsection', array('system' => 'o'));
        entity('fsection2faction', array('system' => 'o'));
        field('fsection2faction', 'allowNoid', array('title' => 'Разрешено не передавать id в uri', 'columnTypeId' => 'BOOLEAN', 'elementId' => 'check'));
        field('fsection2faction', 'row', array('title' => 'Над записью', 'columnTypeId' => 'ENUM', 'elementId' => 'radio',
            'defaultValue' => 'existing', 'relation' => 'enumset', 'storeRelationAbility' => 'one'));
        enumset('fsection2faction', 'row', 'existing', array('title' => 'Существующей'));
        enumset('fsection2faction', 'row', 'new', array('title' => 'Новой'));
        enumset('fsection2faction', 'row', 'any', array('title' => 'Любой'));
        field('fsection2faction', 'where', array('title' => 'Где брать идентификатор', 'columnTypeId' => 'VARCHAR(255)', 'elementId' => 'string'));

        // Exit
        die('ok');
    }
}