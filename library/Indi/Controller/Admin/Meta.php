<?php
class Indi_Controller_Admin_Meta extends Indi_Controller_Admin {

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

    public function formAction() {
        if (isset(Indi::post()->up) && isset(Indi::post()->fsectionId)) {

            $fsectionR = Indi::model('Fsection')->fetchRow('`id` = "' . (int) Indi::post()->fsectionId . '"');

            for ($i = 0; $i < Indi::post()->up; $i++) $fsectionR = $fsectionR->foreign('fsectionId');

            die(json_encode(array('state' => 'ok', 'entityId' => $fsectionR->entityId)));
        } else parent::formAction();
    }
}
