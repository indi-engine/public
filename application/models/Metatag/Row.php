<?php
class Metatag_Row extends Indi_Db_Table_Row {

    /**
     * @return int
     */
    public function move($direction, $within){

        // Normalize $within arg
        $within = is_array($within) ? $within : (strlen($within) ? array($within): array());

        // Ensure move only within scope of rows having same value of `type` prop
        $within[] = '`tag` = "' . $this->tag . '"';

        // Standard save
        return parent::move($direction, $within);
    }
}