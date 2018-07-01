<?php
class Fsection2faction extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    public $_rowClass = 'Fsection2faction_Row';

    /**
     * Array of fields, which contents will be evaluated with php's eval() function
     * @var array
     */
    protected $_evalFields = array('where');
}