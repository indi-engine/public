<?php
class Indi_Trail_Front {

    /**
     * Array of Indi_Trail_Admin_Item items
     *
     * @var array
     */
    public static $items = array();

    /**
     * Indi_Controller_Admin object, by reference
     *
     * @var array
     */
    public static $controller = null;

    /**
     * Constructor
     *
     * @param array $routeA Array of section ids, starting from current section and up to the top
     */
    public function __construct($routeA) {

        // Reset items
        self::$items = array();

        // Get all sections, starting from current and up to the most top
        $sectionRs = Indi::model('Fsection')->fetchAll(
            '`id` IN (' . $route = implode(',', $routeA) . ')',
            'FIND_IN_SET(`id`, "' . implode(',', $routeA) . '")'
        )->foreign('parentSectionConnector,orderColumn');

        // Setup accessible actions
        $sectionRs->nested('fsection2faction', array('foreign' => 'factionId'));

        // Setup initial set of properties
        $i = 0;
        foreach ($sectionRs as $sectionR)
            self::$items[] = new Indi_Trail_Front_Item($sectionR, $sectionRs->count() - ++$i);

        // If
        if (Indi::uri('action') == 'index') {

            // If current section is not a one of the root-sections,
            // and parent section's type is 'single-row section'
            // and parent section has non-empty `where` prop
            // and compiled version of that prop is not an empty string
            // and row was found using that compiled version as WHERE clause
            if (self::$items[0]->section->fsectionId
                && self::$items[1]->section->type == 's'
                && strlen(self::$items[1]->section->where)
                && strlen($majorWHERE = self::$items[1]->section->compiled('where'))
                && $parentRow = self::$items[1]->model->fetchRow($majorWHERE)) {

                // Get the parent id
                $id = $parentRow->id;

            // Else if parent section's row's id is given directly via uri
            } else if (Indi::rexm('int11', Indi::uri('id'))) {

                // Get the parent id
                $id = Indi::uri('id');
            }

            // If $id was defined using any of both methods
            if ($id ?? 0) {

                // If there is no info about nesting yet, we create an array, where it will be stored
                if (!is_array($_SESSION['indi']['front']['trail']['parentId'] ?? 0))
                    $_SESSION['indi']['front']['trail']['parentId'] = array();

                // Save id in session
                $_SESSION['indi']['front']['trail']['parentId'][self::$items[0]->section->fsectionId] = $id;
            }
        }

        // Reverse items
        self::$items = array_reverse(self::$items);
    }

    /**
     * Second level access check:
     * 1. If an 'id' uri param was passed - check it's format
     * 2. Setup *_Row objects for all trail items, where them should be fetched. But if some *_Row object for some
     *    trail items was not found, despite it should - stop, and force 404 error
     *
     * @param Indi_Controller_Front $controller
     */
    public function authLevel2(Indi_Controller_Front &$controller) {

        // Setup controller
        self::$controller = &$controller;

        // If 'id' param is mentioned in uri, but it's value either not specified,
        // or does not match allowed format - setup an error
        if (array_key_exists('id', (array) Indi::uri()) && !preg_match('/^[1-9][0-9]*$/', Indi::uri()->id))
            $error = I_URI_ERROR_ID_FORMAT;

        // Else setup row for each trail item, or setup an access error
        else
            for ($i = 0; $i < count(self::$items); $i++)
                if ($error = Indi::trail($i)->row($i))
                    break;

//        d($_SESSION['indi']['front']['trail']['parentId']);
//        d($i);
//        d($error);

        // Flush an error, if error was met
        if ($error) $controller->notFound();

        // Setup blank scope object for each trail item
        for ($i = 0; $i < count(self::$items); $i++) {
            Indi::trail($i)->scope = new Indi_Trail_Admin_Item_Scope($i);
            Indi::trail($i)->filtersSharedRow($i);
        }
    }

    /**
     * Get trail item
     *
     * @param int $stepsUp
     * @return mixed
     */
    public function item($stepsUp = 0) {
        return self::$items[count(self::$items) - 1 - $stepsUp] ?? null;
    }

    /**
     * Get an array version of trail. Method is used to pass trail data to javascript as json
     *
     * @uses Indi_Trail_Item::toArray()
     * @return array
     */
    public function toArray() {
        $array = array();
        foreach (self::$items as $item) {
            $array[] = $item->toArray();
        }
        end(self::$items);
        return $array;
    }
}