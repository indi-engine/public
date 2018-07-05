<?php
class Admin_MigrationController extends Indi_Controller {
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
            }

            field('metatag', 'fsectionId', array('mode' => 'required'));
            field('metatag', 'fsection2factionId', array('mode' => 'required'));
            field('metatag', 'content', array('title' => 'Указанный вручную'));
            field('metatag', 'fieldId', array('title' => 'Поле'));
            field('metatag', 'move', array('elementId' => 'move'));
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
            enumset('metatag', 'tag', 'title', array('title' => 'Title'));
            enumset('metatag', 'tag', 'keywords', array('title' => 'Keywords'));
            enumset('metatag', 'tag', 'description', array('title' => 'Description'));
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
        }
        die('ok');
    }
}