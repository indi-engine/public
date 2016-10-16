<?php
class Indi_Controller_Front extends Indi_Controller {

    /**
     * Array of section ids, starting from current section and up to the top.
     *
     * @var array
     */
    private $_routeA = array();

    /**
     * Check is user has access to perform $action action within $section section. Checks include:
     * 1. Section exists
     * 2. Section is switched on
     * 3. Action exists
     * 4. Action exists within section
     * 5. Section is a child of a section that is switched on, and all parents up to the top are switched on too
     *
     * @param $section
     * @param $action
     * @return array|mixed|string
     */
    public function auth($section, $action) {

        // Get the info
        $data = Indi::db()->query($sql = '
            SELECT
                `s`.`id`,
                `s`.`toggle` = "y" AS `sectionToggle`,
                `a`.`id` > 0 AS `actionExists`,
                `sa`.`id` > 0 AS `section2actionExists`,
                `s`.`fsectionId` AS `sectionId`,
                `a`.`alias` AS `actionAlias`
            FROM `fsection` `s`
               LEFT JOIN `faction` `a` ON (`a`.`alias` = IF(`s`.`type` = "s" AND "' . $action . '" = "index", `s`.`index`, "' . $action . '"))
               LEFT JOIN `fsection2faction` `sa` ON (`sa`.`factionId` = `a`.`id` AND `sa`.`fsectionId` = `s`.`id`)
            WHERE 1
                AND `s`.`alias` = "' . $section . '"
        ')->fetch();

        // Set appropriate error messages if:
        // 1. Section was not found
        if (!$data) $error = I_ACCESS_ERROR_NO_SUCH_SECTION;

        // 2. Section is switched off
        else if (!$data['sectionToggle']) $error = I_ACCESS_ERROR_SECTION_IS_OFF;

        // 3. Action does not exist at all
        else if (!$data['actionExists']) $error = I_ACCESS_ERROR_NO_SUCH_ACTION;

        // 4. Action does not exits in that section
        else if (!$data['section2actionExists']) $error = I_ACCESS_ERROR_NO_SUCH_ACTION_IN_SUCH_SECTION;

        // 5. One of parent sections for current section - is switched off
        else {

            // Start fulfil section id stack
            $this->_routeA = array($data['id'], $data['sectionId']);

            // Setup initial id of parent section
            $parent = array('fsectionId' => $data['sectionId']);

            // Navigate through parent sections up to the root
            while ($parent = Indi::db()->query('
                SELECT `fsectionId`, `toggle` FROM `fsection` WHERE `id` = "' . $parent['fsectionId'] . '" LIMIT 1
            ')->fetch()) {

                // If any of parent sections if switched off - setup an error and break the loop
                if ($parent['toggle'] == 'n') {
                    $error = I_ACCESS_ERROR_ONE_OF_PARENT_SECTIONS_IS_OFF;
                    break;

                    // Else push new item in $this->_routeA stack
                } else if ($parent['fsectionId']) $this->_routeA[] = $parent['fsectionId'];

                // Else stop loop, as $parent['sectionId'] = 0, so there is no sense to find a section with such an `id`
                else break;
            }
        }

        // If $error was set - return error, else
        if ($error) return $error; else {

            // Update uri's action
            Indi::uri()->action = $data['actionAlias'];

            // Return $data
            return $data;
        }
    }

    /**
     * Throw the 404 page
     */
    public function notFound() {

        // Setup a new uri params, related to '404 Not Found' page
        Indi::uri()->section = 'static';
        Indi::uri()->action = 'details';
        Indi::uri()->id = Indi::model('Staticpage')->fetchRow('`alias` = "404"')->id;
        Indi::uri()->build();

        // Setup 404 response header
        header('HTTP/1.1 404 Not Found');

        // Dispatch these params
        Indi::uri()->dispatch();
    }

	public function preDispatch(){

        // Set locale
        if (Indi::ini()->lang->front == 'ru')
            setlocale(LC_TIME, 'ru_RU.UTF-8', 'ru_utf8', 'Russian_Russia.UTF8', 'ru_RU', 'Russian');

        // Allow accept XHR requests from other hosts
        header('Access-Control-Allow-Origin: *');

        // Do access check
        $data = $this->auth(Indi::uri('section'), Indi::uri('action'));

        // If requested section or action are not registered in the system's database
        if ($data == I_ACCESS_ERROR_NO_SUCH_SECTION || $data == I_ACCESS_ERROR_NO_SUCH_ACTION) {

            // Build the controller class name and action method name
            $controllerClass = ucfirst(Indi::uri()->section) . 'Controller';
            $actionMethod = Indi::uri()->action . 'Action';

            // Check if section and action are physically represented by a certain controller class and action method
            // within it, despite on some/all of them are not registered in the system's database, and if so -
            // skip all further operations of current preDispatch() method, to provide an ability for action method
            // to be called on controller class instance directly, e.g without system additional features, available
            // for sections and actions, registered in the system's database
            if (class_exists($controllerClass) && method_exists($controllerClass, $actionMethod))
                return;
        }

        // If $data is not an array, e.g some error was there, prepare the 404 system state
        if (!is_array($data)) $this->notFound();

        // Setup the Indi_Trail_Front object instance
        Indi::trail($this->_routeA)->authLevel2($this);

        // Static blocks
        Indi::view()->blocks = Indi::blocks();

        // Fetch rowset if need
        $this->rowset();
    }

    /**
     * Builds the ORDER clause
     *
     * @param $finalWHERE
     * @param string $json
     * @return null|string
     */
    public function finalORDER($finalWHERE, $json = '') {

        // If $json argument was not given, or is empty - setup $useDefault flag to true. Else
        if (!$json) $useDefault = true; else {

            // Extract column name and direction from $json argument
            list($column, $direction) = array_values(current(json_decode($json, 1)));

            // If there was no column name extracted, or column name is not a string - setup $useDefault flag to true. Else
            if (!$column || !is_string($column)) $useDefault = true; else

                // If there is actually no such a field - setup $useDefault flag to true
                if (!Indi::trail()->model->fields($column)) $useDefault = true;
        }

        // If $useDefault flag is set to true
        if ($useDefault) {

            // If rowset should be ordered by an sql expression
            if (Indi::trail()->section->orderBy == 'e') {

                // If that expression is not empty
                if (strlen(Indi::trail()->section->orderExpression))

                    // Return compiled version of that expression, as php may be used in
                    return Indi::trail()->section->compiled('orderExpression');

            // Else if rowset should be ordered by a certain column
            } else if (Indi::trail()->section->orderBy == 'c') {

                // If that column is set, an it is an existing column
                if (Indi::trail()->section->foreign('orderColumn'))

                    // Build and return a json-encoded string, containing column/property and direction definitions
                    return parent::finalOrder($finalWHERE, json_encode(array(
                        array(
                            'property' => Indi::trail()->section->foreign('orderColumn')->alias,
                            'direction' => Indi::trail()->section->orderDirection
                        )
                    )));
            }

        // Else use parent::finalORDER()
        } else return parent::finalORDER($finalWHERE, $json);
    }

    /**
     * Build and return a final WHERE clause, that will be passed to fetchAll() method, for fetching section's main
     * rowset. Function use a $primaryWHERE, merge it with $this->filtersWHERE() and append to it $this->keywordWHERE()
     * if return values of these function are not null
     *
     * @param array|string $primaryWHERE
     * @param null $customWHERE
     * @param bool $merge
     * @return array|null|string
     */
    public function finalWHERE($primaryWHERE, $customWHERE = null, $merge = true) {

        // Get ordinary finalWHERE
        $finalWHERE = parent::finalWHERE($primaryWHERE, $customWHERE, false);

        // If current section is a single-row section, and where-get-the-id clause was specified
        if (Indi::trail()->section->type == 's' && strlen(Indi::trail()->section->where))

            // Append that clause to $finalWHERE
            $finalWHERE['singlerow'] = Indi::trail()->section->compiled('where');

        // If WHERE clause should be a string
        if ($merge) {

            // Force $finalWHERE to be single-dimension array
            foreach ($finalWHERE as $part => $where) if (is_array($where)) $finalWHERE[$part] = im($where, ' AND ');

            // Stringify
            $finalWHERE = implode(' AND ', $finalWHERE);
        }

        // Return
        return $finalWHERE;
    }

    /**
     * Builds an array of WHERE clauses, that will be imploded with AND, and used to determine a possible border limits
     * of scope of rows that section will be dealing with, and that are allowed for section to deal with them.
     * While building, it handles:
     *
     * 1. Childs-by-parent logic
     * 2. Custom additional adjustments (adjustments of WHERE clauses stack)
     *
     * After an array is built, function calcs a hash for imploded array, and assigns that hash as a temporary property
     * of current section, for hash to be accessbile within View object.
     *
     * primaryWHERE = parentWHERE + section static filter WHERE, - all adjusted with adjustPrimaryWHERE()
     *
     * @return array
     */
    public function primaryWHERE() {

        // Define an array for WHERE clauses
        $where = array();

        // Append a childs-by-parent clause to primaryWHERE stack
        if ($parentWHERE = $this->parentWHERE()) $where['parent'] = $parentWHERE;

        // If a special section's primary filter was defined, add it to primary WHERE clauses stack
        if (strlen(Indi::trail()->section->filter)) $where['static'] = Indi::trail()->section->compiled('filter');

        // Adjust primary WHERE clauses stack - apply some custom adjustments
        $where = $this->adjustPrimaryWHERE($where);

        if (Indi::uri('action') == 'index') {

            // Get a string version of WHERE stack
            $whereS = count($where) ? implode(' AND ', $where) : null;

            // Set a hash
            Indi::trail()->section->primaryHash = substr(md5($whereS), 0, 10);
        }

        // Return primary WHERE clauses stack
        return $where;
    }

    public function postDispatch($die = true){

        // Setup the menu
        if (Indi::model('Entity')->fetchRow('`table` = "menu"')) Indi::view()->menu = Indi::model('Menu')->init();

        // Get the  filename of view script, that should be rendered
        $view = Indi::trail()->view();

        // Render it
        $out = Indi::view()->render($view);

        // If seo url mode is turned on - convert the urls
		if (Indi::ini()->general->seoUri) $out = Indi_Uri::sys2seo($out);

        // Nest static page uris
        $out = Indi_Uri::nspu($out);

        // If project runs not from document root, but from some subfolder within document root
        if (STD) {

            // Append STD to all urls, specified in 'href' and 'src' attributes within 'link', 'script' and 'img' tags
            $out = preg_replace('/(<(link|a)[^>]*\shref\s*=\s*["\'])\/([^\/])/', '$1' . STD . '/$3', $out);
            $out = preg_replace('/(<script[^>]*\ssrc\s*=\s*["\'])\/([^\/])/', '$1' . STD . '/$2', $out);
            $out = preg_replace('/(<(img|iframe)[^>]*\ssrc\s*=\s*["\'])\/([^\/])/', '$1' . STD . '/$3', $out);
            $out = preg_replace('/(url\s*\(\s*)(\/[^\/])/', '$1' . STD . '$2', $out);
            $out = preg_replace('/(<form[^>]*\saction\s*=\s*["\'])\//', '$1' . STD . '/', $out);
            $out = preg_replace('/(href\s*=\s*(["\']))http:\/\/' . preg_quote($_SERVER['HTTP_HOST'], '/') . '\/?\2/', '$1' . STD . '/$2', $out);
        }

        // Replace all non-https links to https if https protocol is in use
        $out = $this->httpsMaintenance($out);

        // If $this->encoding is not 'utf-8', convert output to needed encoding
        if (strtolower($this->encoding) != 'utf-8') $out = iconv('utf-8', $this->encoding, $out);

        // Flush $out and die, or return $out
        if ($die) die($out); else return $out;
	}

	public function __call($action, $argumentts) {

	}

    /**
     * Replace all non-https links to https
     *
     * @param $html
     * @return string
     */
    public function httpsMaintenance($html) {

        // If https protocol is in use
		if ($_SERVER['SERVER_PORT'] == 443) {

            // Append 'https://' to all urls, specified in 'href' and 'src' attributes within 'link', 'script' and 'img' tags
			$html = preg_replace('/(<link[^>]+href\s*=\s*["\'])\//ui', '$1https://' . Indi::ini()->general->domain . '/', $html);
            $html = preg_replace('/(<script[^>]+src\s*=\s*["\'])\//', '$1https://' . Indi::ini()->general->domain . '/', $html);
            $html = preg_replace('/(<img[^>]+src\s*=\s*"["\'])/ui', '$1https://' . Indi::ini()->general->domain . '/', $html);
		}

        // Return
		return $html;
	}

    /**
     * Function return a sql-string containing a WHERE clause, that do especially provide an ability to deal with
     * childs-by-parent logic, mean that if current section have parent section, we should fetch only records,
     * related to parent row, for example if we want to see cities, we must define in WHAT country these cities
     * are located
     *
     * @return string|null
     */
    public function parentWHERE() {

        // If current action does not deal with rowset - return
        if (!Indi::trail(1) || Indi::trail()->action->maintenance != 'rs') return;

        // We check if a non-standard parent connector field name should be used to fetch childs
        // For example, if we have 'Countries' section (displayed rows a fetched from 'country' db table)
        // and 'Cities' section (displayed rows a fetched from 'city' db table) and 'city' table have a column
        // where country identifier of each city is specified, but this column is not named (for some reason)
        // as 'countryId', and we need it to have some another name - so in that cases we use parentSectionConnector
        // logic.
        $connectorAlias = Indi::trail()->section->parentSectionConnector
            ? Indi::trail()->section->foreign('parentSectionConnector')->alias
            : Indi::trail(1)->model->table() . 'Id';

        // If parent section's type is 'regular'
        if (Indi::trail(1)->section->type == 'r') {

            // Get the connector value
            $connectorValue = Indi::uri('action') == 'index'
                ? Indi::uri('id')
                : $_SESSION['indi']['front']['trail']['parentId'][Indi::trail(1)->section->id];

            if (Indi::trail()->section->parentSectionConnector)
                $connectorValue = Indi::trail(1)->model->fetchRow('`id` = "' . $connectorValue . '"')->$connectorAlias;

        // Else if parent section's type is 'single-row'
        } else {

            // Get the id of that single row
            $connectorValue = Indi::trail(1)->model->fetchRow(Indi::trail(1)->section->compiled('where'))->id;
        }

        // Return clause
        return Indi::trail()->model->fields($connectorAlias)->storeRelationAbility == 'many'
            ? 'FIND_IN_SET("' . $connectorValue . '", `' . $connectorAlias . '`)'
            : '`' . $connectorAlias . '` = "' . $connectorValue . '"';
    }

    /**
     * Getter
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property) {
        if (preg_match('/^user$/i', $property)) return Indi::user();
        else return parent::__get($property);
    }


    /**
     * Fetch a rowset
     */
    public function rowset() {

        // If current section is associated with some entity
        if (Indi::trail()->model) {

            // If current section's type is 'regular', so section is not a single-row section
            if (Indi::trail()->section->type == 'r' && Indi::trail()->action->maintenance == 'rs') {

                // Get the primary WHERE clause
                $primaryWHERE = $this->primaryWHERE();

                // Get final WHERE clause, that will implode primaryWHERE, filterWHERE and keywordWHERE
                $finalWHERE = $this->finalWHERE($primaryWHERE);

                // Setup parentId in session
                if (Indi::uri('id'))
                    $_SESSION['indi']['front']['trail']['parentId'][Indi::trail()->section->id] = Indi::uri('id');

                // Setup 'limit' argument for use in fetchAll/fetchTree
                if (isset(Indi::get()->limit) == false || ! (int) Indi::get()->limit)
                    Indi::get()->limit = Indi::trail()->section->defaultLimit
                        ? Indi::trail()->section->defaultLimit
                        : null;

                // Setup 'page' argument for use in fetchAll/fetchTree
                if (isset(Indi::get()->page) == false || ! (int) Indi::get()->page) Indi::get()->page = 1;

                // Get final ORDER clause, built regarding column name and sorting direction
                $finalORDER = $this->finalORDER($finalWHERE, Indi::get()->sort);

                // Get the rowset, fetched using WHERE and ORDER clauses, and with built LIMIT clause,
                // constructed with usage of Indi::get('limit') and Indi::get('page') params
                $this->rowset = Indi::trail()->model->{
                'fetch'. (Indi::trail()->model->treeColumn() ? 'Tree' : 'All')
                }($finalWHERE, $finalORDER, (int) Indi::get('limit'), (int) Indi::get('page'));
            }
        }
    }

    /**
     * Prepare arguments for $this->_odata() function call, and call that function for fetching combo options data.
     * This function handles all cases, related to combo options data fetch, such as
     * page-by-page appending/prepending, combo-keyword lookup, fetch satellited data (for example fetch cities for second
     * combo when country was selected in first combo), and all this for form and sibling combos
     *
     * We do all these things by passing this call to formActionOdata() function
     *
     * @param string $for A name of field, that combo data should be fetched for
     * @param array $post Request params, required to make a proper fetch (page number, keyword, value of satellite)
     */
    public function createActionOdata($for, $post) {
        $this->formActionOdata($for, $post);
    }

    /**
     * Include additional model's properties into response json, representing rowset data
     *
     * @param $propS string|array Comma-separated prop names (e.g. field aliases)
     */
    public function inclGridProp($propS) {

        // Convert $propS arg to array and collect fieldR instances array,
        // with values of `alias` prop, containing in $propS arg
        $addGridFieldOriginalA = array();
        foreach (ar($propS) as $propI)
            $addGridFieldOriginal[] = Indi::trail()->model->fields($propI);

        // Create rowset
        $addGridFieldRs = Indi::model('Field')->createRowset(array(
            'rows' => $addGridFieldOriginal,
            'aliases' => $propS
        ));

        // Merge existing grid fields with additional
        Indi::trail()->gridFields->merge($addGridFieldRs);
    }

    /**
     * Include additional model's properties into response json, representing rowset data
     *
     * @param $propS string|array Comma-separated prop names (e.g. field aliases)
     */
    public function exclGridProp($propS) {

        // Merge existing grid fields with additional
        Indi::trail()->gridFields->exclude($propS, 'alias');
    }

    public function saveAction($return = false) {

        // Get array of aliases of fields, that are actually represented in database table
        $possibleA = Indi::trail()->model->fields(null, 'columns');

        // Pick values from Indi::post()
        $data = array();
        foreach ($possibleA as $possibleI)
            if (array_key_exists($possibleI, Indi::post()))
                $data[$possibleI] = Indi::post($possibleI);

        // Unset 'move' key from data, because 'move' is a system field, and it's value will be set up automatically
        unset($data['move']);

        // If there was disabled fields defined for current section, we check if default value was additionally set up
        // and if so - assign that default value under that disabled field alias in $data array, or, if default value
        // was not set - drop corresponding key from $data array
        foreach (Indi::trail()->disabledFields as $disabledFieldR)
            foreach (Indi::trail()->fields as $fieldR)
                if ($fieldR->id == $disabledFieldR->fieldId)
                    if (!strlen($disabledFieldR->defaultValue)) unset($data[$fieldR->alias]);
                    else $data[$fieldR->alias] = $disabledFieldR->compiled('defaultValue');

        // If current cms user is an alternate, and if there is corresponding field within current entity structure
        if ($this->alternateWHERE() && Indi::admin()->alternate && in($aid = Indi::admin()->alternate . 'Id', $possibleA))

            // Prevent alternate field to be set via POST, as it was already (properly)
            // set at the stage of trail item row initialization
            unset($data[$aid]);

        // Update current row properties with values from $data array
        $this->row->assign($data);

        // If some of the fields are CKEditor-fields, we shoudl check whether they contain '<img>' and other tags
        // having STD injections at the beginning of 'src' or other same-aim html attributes, and if found - trim
        // it, for avoid problems while possible move from STD to non-STD, or other-STD directories
        $this->row->trimSTDfromCKEvalues();

        // Get the list of ids of fields, that are disabled
        $disabledA = Indi::trail()->disabledFields->column('fieldId');

        // Get the aliases of fields, that are file upload fields, and that are not disabled,
        // and are to be some changes applied on
        $filefields = array();
        foreach (Indi::trail()->fields as $fieldR)
            if ($fieldR->foreign('elementId')->alias == 'upload' && !in_array($fieldR->id, $disabledA) && $_ = Indi::post($fieldR->alias)) {
                if (!Indi::rexm('url', $_) && $_ != 'd') $_ = Indi::post()->{$fieldR->alias} = 'm';
                if (preg_match('/^m|d$/', $_) || Indi::rexm('url', $_)) $filefields[] = $fieldR->alias;
            }

        // Prepare metadata, related to fileupload fields contents modifications
        $this->row->files($filefields);

        // Do pre-save operations
        $this->preSave();

        // Setup 'zeroValue'-mismatches
        foreach (Indi::trail()->fields as $fieldR)
            if ($fieldR->mode == 'required' && $this->row->fieldIsZero($fieldR->alias))
                $this->row->mismatch($fieldR->alias, sprintf(I_ROWSAVE_ERROR_VALUE_REQUIRED, $fieldR->title));

        // Flush 'zeroValue'-mismatches
        $this->row->mflush();

        // Save the row
        $this->row->save();

        // Do post-save operations
        $this->postSave();

        // Prepare response. Here we mention a number of properties, related to saved row, as a proof that row saved ok
        $response = array('title' => $this->row->title(), 'id' => $this->row->id);

        // Wrap row in a rowset, process it by $this->adjustGridDataRowset(), and unwrap back
        $this->rowset = Indi::trail()->model->createRowset(array('rows' => array($this->row)));
        //$this->adjustGridDataRowset();
        $this->row = $this->rowset->at(0);

        // Wrap data entry in an array, process it by $this->adjustGridData(), and uwrap back
        $data = array($this->row->toGridData($this->row->affected()));
        //$this->adjustGridData($data);
        $data = array_shift($data);

        // Assign row's grid data into 'affected' key within $response
        $response['affected'] = $data;
        $response['success'] = true;

        // Flush response
        if ($return) return $response; else jflush($response);
    }
}