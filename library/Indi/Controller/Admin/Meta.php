<?php
class Indi_Controller_Admin_Meta extends Indi_Controller_Admin {

    /**
     *
     */
    public function adjustGridDataRowset() {
        foreach ($this->rowset as $row)
            if ($row->type != 'dynamic' || $row->source != 'row') {
                $row->entityId = 0;
                $row->fieldId = 0;
                if ($row->type == 'static') {
                    $row->up = '';
                    $row->source = '';
                }
            }
    }

    /**
     *
     */
    public function formAction() {

        // Get current $fsectionR
        $fsectionR = $this->row->foreign('fsectionId'); $maxUp = 0; $limit = 100;

        // Find max value for 'up' prop
        while (($fsectionR = $fsectionR->foreign('fsectionId')) && ($i = $i + 1) && $i < $limit) $maxUp++;

        // Set up max value for 'up' prop into the view
        $this->row->view('up', array('maxValue' => $maxUp));

        // Clear deprecated `javascript` props values
        foreach (ar('source,type') as $p)
            foreach(Indi::trail()->model->fields($p)->nested('enumset') as $enumsetR)
                $enumsetR->javascript = '';

        // Call parent
        $this->callParent();
    }

    /**
     * @param $data
     */
    public function formActionIEntityId($data) {

        // Check that 'up' param exists within $data
        if (!array_key_exists('up', $data)) jflush(false, 'Отсутствует параметр "Шагов вверх"');

        // Check that 'up' param is integer
        if (!Indi::rexm('int11', $data['up']))
            jflush(false, 'Значение "' . $data['up'] . '" параметра "Шагов вверх" должно быть целым числом');

        // Check that 'up' param exists within $data
        if (!array_key_exists('fsectionId', $data)) jflush(false, 'Отсутствует параметр "Раздел"');

        // Check that 'fsectionId' param is integer
        if (!Indi::rexm('int11', $data['fsectionId']))
            jflush(false, 'Значение "' . $data['fsectionId'] . '" параметра "Раздел" должно быть целым числом');

        // Get `id` of `fsection` entry, that will be the start point for stepping upper
        if (!$fsectionR = Indi::model('Fsection')->fetchRow('`id` = "' . $data['fsectionId'] . '"'))
            jflush(false, 'Раздел с идентификатором "' . $data['fsectionId'] . '" не найден');

        // Remember start section
        $start = $fsectionR;

        // Make required count of steps up
        for ($i = 0; $i < $data['up']; $i++)
            if (!$fsectionR = $fsectionR->foreign('fsectionId'))
                jflush(false, 'Раздел, вышестоящий на ' . tbq($i + 1, 'уровней,уровень,уровня')
                    . ' относительно раздела "' . $start->title . '" - не существует');

        // Flush response
        jflush(true, array('entityId' => $fsectionR->entityId));
    }
}
