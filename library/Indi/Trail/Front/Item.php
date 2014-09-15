<?php
class Indi_Trail_Front_Item {

    /**
     * Store item section row object
     *
     * @var Section_Row object
     */
    public $section;

    /**
     * Store trail action row
     *
     * @var Indi_Db_Table_Row object
     */
    public $action;

    /**
     * Store trail section2action row
     *
     * @var Indi_Db_Table_Row object
     */
    public $section2action;

    /**
     * Store trail item row
     *
     * @var Indi_Db_Table_Row object
     */
    public $row;

    /**
     * Store trail item rowset
     *
     * @var Indi_Db_Table_Rowset object
     */
    public $rowset;

    /**
     * View filename, that should be rendered
     *
     * @var
     */
    protected $_view = array(
        'inner' => null,
        'outer' => null
    );

    /**
     * Getter. Currently declared only for getting 'model' property
     *
     * @param $property
     * @return Indi_Db_Table
     */
    public function __get($property) {
        if ($this->section->entityId)
            if ($property == 'model') return Indi::model($this->section->entityId);
            else if ($property == 'fields') return Indi::model($this->section->entityId)->fields();
    }

    /**
     * Set up all internal properties
     *
     * @param Indi_Db_Table_Row $sectionR
     */
    public function __construct($sectionR) {

        // Setup $this->section
        $config = array();
        $dataTypeA = array('original', 'temporary', 'compiled', 'foreign');
        foreach ($dataTypeA as $dataTypeI) $config[$dataTypeI] = $sectionR->$dataTypeI();
        $this->section = Indi::model('Fsection')->createRow($config);

        // If current trail item will be a first item
        if (count(Indi_Trail_Admin::$items) == 0) {

            // Setup a primary hash for current section
            $this->section->temporary('primaryHash', Indi::uri('ph'));

            // Setup action
            foreach ($sectionR->nested('fsection2faction') as $fsection2factionR)
                if ($fsection2factionR->foreign('factionId')->alias == Indi::uri('action')) {
                    $this->section2action = $fsection2factionR;
                    $this->action = $fsection2factionR->foreign('factionId');
                }

        } else {

            // Setup action as 'index'
            foreach ($sectionR->nested('fsection2faction') as $fsection2factionR)
                if ($fsection2factionR->foreign('factionId')->alias == 'index') {
                    $this->section2action = $fsection2factionR;
                    $this->action = $fsection2factionR->foreign('factionId');
                }
        }
    }

    /**
     * Setup rows for each trail item, if possible
     *
     * @param $index
     * @return string
     */
    public function row($index){

        // If current trail item relates to current section
        if ($index == 0) {

            // If current section is a non-single-row section, and current action's maintenance is 'row'
            // (e.g not 'none' or 'rowset'), and there was no id passed within the uri - throw 'Not Found' page
            if ($this->section->type == 'r' && $this->action->maintenance == 'r' && !Indi::uri('id'))
                Indi_Trail_Front::$controller->notFound();

            // If current action's maintenance is 'none' or 'rowset', e.g is not 'row' - return
            if ($this->action->maintenance != 'r') return;

            // If 'id' uri param exists - setup $majorWHERE clause based on it
            if (Indi::uri('id')) $majorWHERE = '`id` = "' . Indi::uri('id') . '"';

            // Else if current section's type is 'single-row', and special expression for row identification was set
            else if ($this->section->type == 's' && strlen($this->section->where))

                // Setup $majorWHERE clause based on that expression
                $majorWHERE = $this->section->compiled('where');

            // If there is an id
            if ($majorWHERE) {

                // Get primary WHERE clause
                $where = Indi_Trail_Front::$controller->primaryWHERE();

                // Prepend an additional part to WHERE clause array, so if row would be found,
                // it will mean that that row match all necessary requirements
                array_unshift($where, $majorWHERE);

                // Try to find a row by given id, that, hovewer, also match all requirements,
                // mentioned in all other WHERE clause parts
                if (!($this->row = $this->model->fetchRow($where))) {

                    // If row was not found, return an error
                    return I_ACCESS_ERROR_ROW_DOESNT_EXIST;
                }

            // Else there was no id passed within uri or special row identification expression, and action is
            // 'form', 'save' or 'create',  we assume that user it trying to add a new row within current section
            } else if (preg_match('/^form|save|create$/', Indi::uri('action'))) {

                // Create an empty row object
                $this->row = $this->model->createRow();

                // Setup several properties within the empty row, e.g if we are trying to create a 'City' row, and
                // a moment ago we were browsing cities list within Canada - we should autosetup a proper `countryId`
                // property for that empty 'City' row, for ability to save it as one of Canada's cities
                for ($i = 1; $i < count(Indi_Trail_Admin::$items) - 1; $i++) {

                    // Determine the connector field between 'country' and 'city'. Usually it is '<parent-table-name>Id'
                    // but in some custom cases, this may differ. We do custom connector field autosetup only if it was
                    // set and only in case of one-level-up parent section. This mean that if we have 'Continents' as
                    // upper level, and we are creating city, city's property name will be determined as `continentId`
                    // mean parentSectionConnector logic won't be used for that
                    $connector = $i == 1 && Indi::trail($i-1)->section->parentSectionConnector
                        ? Indi::trail($i-1)->section->foreign('parentSectionConnector')->alias
                        : Indi::trail($i)->model->table() . 'Id';

                    // Get the connector value from session special place
                    if ($this->model->fields($connector))
                        $this->row->$connector = $_SESSION['indi']['front']['trail']['parentId']
                        [Indi::trail($i)->section->id];
                }
            }

        // Else if current trail item relates to one of parent sections
        } else {

            // Declare array for WHERE clause
            $where = array();

            // Determine the connector field
            $connector = $this->model->table() . 'Id';

            // If current trail item's section is a regular section
            if ($this->section->type == 'r') {

                // Get the id
                $id = Indi::trail()->action->maintenance == 'rs' && $index == 1
                    ? Indi::uri('id')
                    : (Indi::trail($index-1)->model->fields($connector)
                        ? Indi::trail($index-1)->row->$connector
                        : $_SESSION['indi']['front']['trail']['parentId'][Indi::trail($index-1)->section->id]);

                // Add main item to WHERE clause stack
                $where[] = '`id` = "' . $id . '"';

            // Else if current trail item's section is a single-row section
            } else $where[] = $this->section->compiled('where');

            // If a special section's primary filter was defined add it to WHERE clauses stack
            if (strlen($this->section->compiled('filter'))) $where[] = $this->section->compiled('filter');

            // Try to find a row by given id, that, hovewer, also match all requirements,
            // mentioned in all other WHERE clause parts
            if (!($this->row = $this->model->fetchRow($where)))

                // If row was not found, return an error
                return I_ACCESS_ERROR_ROW_DOESNT_EXIST;
        }
    }

    /**
     * @param $name
     * @param $inner
     * @return mixed
     */
    public function view($name = '', $inner = false) {

        if ($name)
            $this->_view[$inner ? 'inner' : 'outer']
                = $this->section2action->type == 'j' || strpos($name,  '/') === false || $inner
                    ? $this->section->alias . '/' . $name . '.php'
                    : $name. '.php';

        else if ($this->_view[$inner ? 'inner' : 'outer'] === null)
            $this->_view[$inner ? 'inner' : 'outer'] = $this->section2action->type == 'j' || $inner
                ? $this->section->alias . '/' . $this->action->alias . '.php'
                : 'index.php';

        return $this->_view[$inner ? 'inner' : 'outer'];
    }

}