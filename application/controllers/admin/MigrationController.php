<?php
class Admin_MigrationController extends Indi_Controller {
    public function metatagAction() {

        if (section('metakeywords')) section('metakeywords')->delete();
        if (section('metadescription')) section('metadescription')->delete();
        field('grid', 'alias', array('title' => 'Ключ', 'elementId' => 'string', 'columnTypeId' => 'VARCHAR(255)'))->move(15)->move(-3);
        if (field('metatag', 'fieldId')->title == 'Свойство') {
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