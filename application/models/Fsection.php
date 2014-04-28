<?php
class Fsection extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Fsection_Row';

    /**
     * Array of fields, which contents will be evaluated with php's eval() function
     * @var array
     */
    protected $_evalFields = array('filter', 'where', 'orderExpression');
}