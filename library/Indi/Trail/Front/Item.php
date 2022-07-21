<?php
class Indi_Trail_Front_Item extends Indi_Trail_Item {

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
     * Store trail item rowset
     *
     * @var Indi_Db_Table_Rowset object
     */
    public $rowset;

    /**
     * Set up all internal properties
     *
     * @param Indi_Db_Table_Row $sectionR
     * @param $level
     */
    public function __construct($sectionR, $level) {

        // Setup $this->section
        $config = array();
        $dataTypeA = array('original', 'temporary', 'compiled', 'foreign');
        foreach ($dataTypeA as $dataTypeI) $config[$dataTypeI] = $sectionR->$dataTypeI();
        $this->section = Indi::model('Fsection')->createRow($config);

        // Setup index
        $this->level = $level;

        // If current trail item will be a first item
        if (count(Indi_Trail_Admin::$items) == 0) {

            // Setup filters
            if ($sectionR->sectionId)
                $this->filters = $sectionR->foreign('sectionId')->nested('search', array(
                    'where' => '`toggle` = "y"',
                    'order' => '`move`'
                ));

            // Setup $this->actions
            foreach ($sectionR->nested('fsection2faction') as $section2actionR)
                $actionA[] = $section2actionR->foreign('factionId');
            $this->actions = Indi::model('Action')->createRowset(array('rows' => $actionA));

            // Setup subsections
            $this->sections = $sectionR->nested('fsection');

            // Setup a primary hash for current section
            $this->section->temporary('primaryHash', Indi::uri('ph'));

            // Setup action
            foreach ($sectionR->nested('fsection2faction') as $fsection2factionR)
                if ($fsection2factionR->foreign('factionId')->alias == Indi::uri('action')) {
                    $this->section2action = $fsection2factionR;
                    $this->action = $fsection2factionR->foreign('factionId');
                }

            // Set fields, that will be used as grid columns in case if current action is 'index'
            if (Indi::uri('action') == 'index' && $sectionR->sectionId) {
                $gridFieldA = array();
                foreach ($sectionR->foreign('sectionId')->nested('grid') as $gridR) {
                    foreach ($this->fields as $fieldR) {
                        if ($gridR->fieldId == $fieldR->id) {
                            $gridFieldI = $fieldR;
                            if ($gridR->alterTitle) $gridFieldI->title = $gridR->alterTitle;
                            $gridFieldA[] = $gridFieldI;
                            $gridFieldAliasA[] = $gridFieldI->alias;
                        }
                    }
                }
                $this->gridFields = Indi::model('Field')->createRowset(array(
                    'rows' => $gridFieldA,
                    'aliases' => $gridFieldAliasA
                ));
                $this->grid = $sectionR->foreign('sectionId')->nested('grid');
            }

        // Else
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

            // If current action's maintenance is 'none' or 'rowset', e.g is not 'row' - return
            if ($this->action->maintenance != 'r') return;

            // If current section is a non-single-row section, and there was no id passed within the uri,
            // and $this->section2action->row is null (backwards compatibility)- throw 'Not Found' page
            if ($this->section->type == 'r' && !$this->section2action->row && !Indi::uri('id'))
                if (!preg_match('/^save|create$/', Indi::uri('action')))
                    Indi_Trail_Front::$controller->notFound();

            // If current section is a non-single-row section
            if ($this->section->type == 'r' && $this->section2action->row != 'new') {

                // If 'id' uri param exists - setup $majorWHERE clause based on it
                if (Indi::uri('id')) $majorWHERE = '`id` = "' . Indi::uri('id') . '"';

                // If `section2action` entry's `where` prop - is not an empty string
                else if (strlen($this->section2action->where)) $majorWHERE = $this->section2action->compiled('where');

            // Else if current section's type is 'single-row', and special expression for row identification was set
            } else if ($this->section->type == 's' && strlen($this->section->where))

                // Setup $majorWHERE clause based on that expression
                $majorWHERE = $this->section->compiled('where');

            // If we have a clause for row identification
            if ($majorWHERE) {

                // Get primary WHERE clause
                $where = Indi_Trail_Front::$controller->primaryWHERE();

                // Prepend an additional part to WHERE clause array, so if row would be found,
                // it will mean that that row match all necessary requirements
                array_unshift($where, $majorWHERE);

                // If there is no row found matching all parts of WHERE clause
                if (!($this->row = $this->model->fetchRow($where))) {

                    // If only existing rows are allowed to be operated by current action
                    if (!$this->section2action->row || $this->section2action->row == 'existing' || Indi::uri('id'))

                        // Return an error
                        return I_ACCESS_ERROR_ROW_DOESNT_EXIST;

                    // Else jump to empty row creation
                    else goto create;
                }

            // Else there was no id passed within uri or special row identification expression, and action is
            // 'form', 'save' or 'create',  we assume that user it trying to add a new row within current section
            } else if (preg_match('/^form|save|create$/', Indi::uri('action')) || in($this->section2action->row, 'new,any')) {

                // Label for 'goto'
                create:

                // Create an empty row object
                $this->row = $this->model->createRow();

                // Setup several properties within the empty row, e.g if we are trying to create a 'City' row, and
                // a moment ago we were browsing cities list within Canada - we should autosetup a proper `countryId`
                // property for that empty 'City' row, for ability to save it as one of Canada's cities
                for ($i = 1; $i < count(Indi_Trail_Front::$items); $i++) {

                    // Determine the connector field between 'country' and 'city'. Usually it is '<parent-table-name>Id'
                    // but in some custom cases, this may differ. We do custom connector field autosetup only if it was
                    // set and only in case of one-level-up parent section. This mean that if we have 'Continents' as
                    // upper level, and we are creating city, city's property name will be determined as `continentId`
                    // mean parentSectionConnector logic won't be used for that
                    $connector = $i == 1 && Indi::trail($i-1)->section->parentSectionConnector
                        ? Indi::trail($i-1)->section->foreign('parentSectionConnector')->alias
                        : Indi::trail($i)->model->table() . 'Id';

                    // Get the connector value
                    if ($this->model->fields($connector)) {

                        // If connection value is presented in session special place
                        if ($value = $_SESSION['indi']['front']['trail']['parentId'][Indi::trail($i)->section->id])

                            // Assign it
                            $this->row->$connector = $value;

                        // Else if trail item's section is a 'single-row' section - find that single row
                        else if (Indi::trail($i)->section->type == 's'
                            && strlen(Indi::trail($i)->section->where)
                            && strlen($majorWHERE = Indi::trail($i)->section->compiled('where'))
                            && $parentRow = Indi::trail($i)->model->fetchRow($majorWHERE)) {

                            // Assign found single row's id as a connector value
                            $this->row->$connector = $parentRow->id;
                        }
                    }
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
     * Get the  filename of view script, that should be rendered
     *
     * @return string
     */
    public function view() {

        return $this->section2action->type == 'j'
            ? $this->section->alias . '/' . $this->action->alias . '.php'
            : 'index.php';
    }

    /**
     * Prepare filter combo data
     *
     * @param $fields
     */
    public function filters($fields) {

        // Foreach field in comma-separated list of fields (or array)
        foreach (ar($fields) as $field) {

            // Create `search` row instance
            $filter = Indi::model('Search')->createRow([
                'fieldId' => $this->fields($field)->id,
                'consistence' => 2
            ], true);

            // Feed ``Setup combo data
            view()->filterCombo($filter, true);
        }
    }
}