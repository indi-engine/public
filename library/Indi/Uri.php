<?php
class Indi_Uri extends Indi_Uri_Base {

    /**
     * Array of additional WHERE clauses for static pages fetch
     *
     * @var array
     */
    public $staticpageAdditionalWHERE = array();

    /**
     * Dispatch the uri
     */
    public function dispatch(){

        // Do pre-dispatch operations
        $this->preDispatch();

        // Build the controller class name
        $controllerClass = ucfirst(Indi::uri('section')) . 'Controller';

        // If there is no such a controller
        if (!class_exists($controllerClass)) {

            // Get the Fsection_Row object, related either to appropriate frontend section, or 'static'
            // frontend section, which will be used in case if appropriate section won't be found
            $fsectionR = Indi::model('Fsection')->fetchRow(
                '`alias` IN ("' . Indi::uri('section') . '", "static")',
                'FIND_IN_SET(`alias`, "' . Indi::uri('section') . ',static")'
            );

            // If found Fsection_Row represents 'static' frontend section
            if ($fsectionR->alias == 'static') {

                // Build the array of WHERE clauses, for try to find a appropriate static page
                $where = array_merge(
                    array('`alias` IN ("' . Indi::uri('section') . '", "404")', '`toggle` = "y"'),
                    $this->staticpageAdditionalWHERE
                );

                // Get the Staticpage_Row object, related either to appropriate static page, or '404'
                // static page, which will be used in case if appropriate static page won't be found
                $staticpageR = Indi::model('Staticpage')->fetchRow($where, 'FIND_IN_SET(`alias`, "' . Indi::uri('section') . ',404")');

                // Setup new uri params
                Indi::uri()->section = 'static';
                Indi::uri()->action = 'details';
                Indi::uri()->id = $staticpageR->id;

                // If static page is '404' setup $notFound flag as boolean true
                if ($staticpageR->alias == '404' || !$staticpageR->id) $notFound = true;

            // Else if found Fsection_Row represents some another frontend section, but the action, specified within
            // the uri - is not a one of actions, that are allowed for use in frontend
            } else if (!Indi::model('Faction')->fetchRow('`alias` = "' . Indi::uri('action') . '"')) {

                // Build the array of WHERE clauses, for try to find a appropriate '404' static page
                $where = array_merge(
                    array('`alias` = "404"', '`toggle` = "y"'),
                    $this->staticpageAdditionalWHERE
                );

                // Get the Staticpage_Row object, related either to appropriate '404' static page
                $staticpageR = Indi::model('Staticpage')->fetchRow($where);

                // Setup new uri params
                Indi::uri()->section = 'static';
                Indi::uri()->action = 'details';
                Indi::uri()->id = $staticpageR->id;

                // Setup $notFound flag as boolean true
                $notFound = true;
            }
        }

        if ($notFound) {
            header('HTTP/1.1 404 Not Found');
            if (!$staticpageR->id) die(I_NO404_FOUND);
        } else {
            $this->trailingSlash();
        }

        // Build the controller class name
        $controllerClass = ucfirst(Indi::uri('section')) . 'Controller';

        // If there is no such a controller
        if (!class_exists($controllerClass)) {

            // Setup the default parent class for controller auto-creation
            $controllerParentClass = 'Project_Controller_Front';

            // If controller parent class does not exist - switch $controllerParentClass to 'Indi_Controller_Front'
            if (!class_exists($controllerParentClass)) $controllerParentClass = 'Indi_Controller_Front';

            // If parent class is explicitly specified as one of frontend section's property -
            // adjust controller parent class name by appending that property's value to
            // current value of $controllerParentClass, as system assume that `extends` property
            // contains controller parent class name specification, relative to current
            // $controllerParentClass value
            if ($fsectionR->extends) $controllerParentClass .= '_' . $fsectionR->extends;

            // If controller parent class does not exist
            if (!class_exists($controllerParentClass))

                // Stop and give an error
                die(sprintf(I_FSECTION_PARENT_CLASS_NOT_EXISTS, $controllerParentClass, $controllerClass));

            // Auto-declare the controller class using php 'eval' function
            eval('class ' . ucfirst($controllerClass) . ' extends ' . $controllerParentClass . '{}');
        }

        // Create the instance of $controllerClass
        $controller = new $controllerClass();

        // Call the dispatch() method on that instance
        $controller->dispatch();
    }

    /**
     * Pre-dispatch operations
     */
    public function preDispatch() {

        // Call parent's preDispatch()
        parent::preDispatch();

        // If section name is not valid - throw an 404 page
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9\-]*$/', Indi::uri('section')))

            // Setup uri's section as random value, that knowingly won't match any existing section,
            // so we can be sure that '404' page will be displayed
            Indi::uri()->section = preg_replace('/^[0-9]+/', '', grs());



        // Else check if seo uri mode is enabled, and if so - convert it back to non-seo
        // structure and provide the ability of it further use
        else $this->checkSeoUrlsMode();

    }

    /**
     * Check if seo uri mode is enabled, and if so - convert it back to non-seo
     * structure and provide the ability of it further use
     */
    public function checkSeoUrlsMode(){

        // Backup initial REQUEST_URI
        $GLOBALS['INITIAL_URI'] = $_SERVER['REQUEST_URI'];

        // If seo uri mode is enabled
        if (Indi::ini()->general->seoUri) {

            // Convert existing request uri to non-seo structure
            $_SERVER['REQUEST_URI'] = $this->seo2sys($_SERVER['REQUEST_URI']);

            // Refresh uri properties
            if ($_SERVER['REQUEST_URI'] != $GLOBALS['INITIAL_URI']) $this->parse();
        }
    }

    /**
     * Clear all current uri params
     */
    public function clear() {
        foreach ($this as $prop => $value) if ($prop != 'staticpageAdditionalWHERE') unset($this->$prop);
    }


    public function seo2sys($seo){
        $db = Indi::db();
        $url = parse_url($seo);
        $aim = explode('/', trim($url['path'], '/'));
        if ($aim[count($aim)-1] == 'noseo') return $seo;
        if (count($aim) > 1) {

            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-\.]*$/', $aim[1])) return '/' . preg_replace('/^[0-9]+/', '', grs()) . '/';

            $sql = '
			SELECT
			  `sa`.`id`
			FROM
			  `fsection` `s`,
			  `faction` `a`,
			  `fsection2faction` `sa`
			WHERE 1
			  AND `sa`.`fsectionId` = `s`.`id`
			  AND `s`.`alias` = "' . $aim[0] . '"
			  AND `sa`.`factionId` = `a`.`id`
			  AND IF(`sa`.`rename`,`sa`.`alias` = "' . $aim[1] . '", `a`.`alias` = "' . $aim[1] . '")';

            $r = $db->query($sql)->fetchAll();
            $saId = $r[0]['id'];
            $sql = '
			SELECT
			  `u`.*,
			  `sa`.`blink`,
			  `sa`.`rename`,
			  `sa`.`alias` AS `renameBy`,
			  `a`.`alias` AS `originalAlias`
			FROM
			  `url` `u`,
			  `fsection` `s`,
			  `faction` `a`,
			  `fsection2faction` `sa`
			WHERE 1
			  AND `u`.`fsection2factionId` = `sa`.`id`
			  AND `sa`.`fsectionId` = `s`.`id`
			  AND `s`.`alias` = "' . $aim[0] . '"
			  AND `sa`.`factionId` = `a`.`id`
			  AND ' . ($saId ? '`sa`.`id` = "' . $saId . '"' : 'IF(`sa`.`rename`,`sa`.`alias` = "' . $aim[1] . '", `a`.`alias` = "' . $aim[1] . '")') . '
			ORDER BY `u`.`move`';
            $parts = $db->query($sql)->fetchAll();
            if (count($parts) == 0 && !$saId) {
                $sql = '
				SELECT
				  `u`.*,
				  `sa`.`blink`,
				  `a`.`alias`
				FROM
				  `url` `u`,
				  `fsection` `s`,
				  `faction` `a`,
				  `fsection2faction` `sa`
				WHERE 1
				  AND `u`.`fsection2factionId` = `sa`.`id`
				  AND `sa`.`fsectionId` = `s`.`id`
				  AND `s`.`alias` = "' . $aim[0] . '"
				  AND `sa`.`factionId` = `a`.`id`
				  AND `sa`.`blink` = "1"
				ORDER BY `u`.`move`';
                $parts = $db->query($sql)->fetchAll();
            }
            if (count($parts) == 0) {
                $sys = $seo;
            } else {
                $models = array();
                $sys = array($aim[0]);
                $sys[] = $parts[0]['alias'] ? $parts[0]['alias'] : ($parts[0]['rename'] ? $parts[0]['originalAlias'] : $aim[1]);
                $alias = $parts[0]['alias'] ? $aim[1] : $aim[2];

                if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-\.]*$/', $aim[1])) return '/' . preg_replace('/^[0-9]+/', '', grs()) . '/';

                for ($i = 0; $i < count($parts); $i++) {
                    if (!in_array($parts[$i]['entityId'], array_keys($models))) $models[$parts[$i]['entityId']] = Indi::model($parts[$i]['entityId']);
                }
                $where = '';
                $lastId = 0;
                $shift = 0;
                for ($i = 0; $i < count($parts); $i++) {
                    if (isset($aim[$i - 1 + ($parts[0]['alias'] ? 2 : 3) - $shift]) && $component = $models[$parts[$i]['entityId']]->fetchRow('`alias` = "' . $alias . '"' . $where)) {
//					echo '`alias` = "' . $alias . '"' . $where . '<br>' . "\n";
                        $lastId = $component->id;

                        if ($i == ($parts[0]['alias'] && !$parts[0]['blink'] ? count($parts) : count($parts) - 1)) {
//					if ($i == ($parts[0]['alias'] ? count($parts) : count($parts) - 1)) {
                            $sys[] = $parts[$i]['prefix'] . '/' . $component->id;
                            break;
                        } else if ($i > 0){
                            $where = ' AND `' . $models[$parts[$i]['entityId']]->table() . 'Id` = ' . $component->id;
                            $alias = $aim[$i+($parts[0]['alias'] ? 2 : 3) - $shift];
                            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-\.]*$/', $alias)) return '/' . preg_replace('/^[0-9]+/', '', grs()) . '/';
                        }
                        $where = ' AND `' . $models[$parts[$i]['entityId']]->table() . 'Id` = ' . $component->id;
                        $alias = $aim[$i+($parts[0]['alias'] ? 2 : 3) - $shift];
                        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-\.]*$/', $alias)) return '/' . preg_replace('/^[0-9]+/', '', grs()) . '/';
                    } else if ($component = $models[$parts[$i]['entityId']]->fetchRow('`alias` = ""' . $where)) {
                        $where = ' AND `' . $models[$parts[$i]['entityId']]->table() . 'Id` = ' . $component->id;
                        $shift++;
                        $alias = $aim[$i+($parts[0]['alias'] ? 2 : 3) - $shift];
                        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-\.]*$/', $alias)) return '/' . preg_replace('/^[0-9]+/', '', grs()) . '/';
                    } else if (!$alias) {
                        $sys[] = $parts[$i-1]['prefix'] . '/' . $lastId;
                        break;
                    }

                }
                for ($i = ($parts[0]['alias'] ? 0 : 1)+ 1 + count($parts); $i < count($aim); $i++) $sys[] = $aim[$i];
                $sys = '/' . implode('/', $sys) . '/';
                if ($url['query']) $sys .= '?' . $url['query'];
                if ($url['fragment']) $sys .= '#' . $url['fragment'];
            }
        } else {
            $sys = $seo;
        }
        return $sys;
    }

    public static function sys2seo($sys, $cr = false, $reg = ''){
        preg_match_all($reg ? $reg: '/(href|url)="([0-9a-z\/#]+)"/', $sys, $matches);
        $uri = $matches[2];
        $db = Indi::db();
        $furi = array();
        for ($i = 0; $i < count($uri); $i++) if (count(explode('/', trim($uri[$i], '/'))) > 1) $furi[] = $uri[$i]; $uri = $furi; $furi = array();
        $groups = array();
        for ($i = 0; $i < count($uri); $i++) {

            if (STD) $uri[$i] = preg_replace('~^' . preg_quote(STD, '~') . '~', '', $uri[$i]);

            list($empty, $section, $action, $prefix) = explode('/', $uri[$i]);
            $group = '/' . $section . '/' . $action . '/';
            if (!in_array($group, $groups)) $groups[] = $group;
        }

        $sql = '
			SELECT
			  CONCAT("/", `s`.`alias`, "/", `a`.`alias`, "/") AS `concat`,
			  `u`.*,
			  `sa`.`blink`,
			  `sa`.`rename`,
			  `sa`.`alias`
			FROM
			  `url` `u`,
			  `fsection` `s`,
			  `faction` `a`,
			  `fsection2faction` `sa`
			WHERE 1
			  AND `u`.`fsection2factionId` = `sa`.`id`
			  AND `sa`.`fsectionId` = `s`.`id`
			  AND CONCAT("/", `s`.`alias`, "/", `a`.`alias`, "/") IN ("' . implode('","', $groups) . '")
			  AND `sa`.`factionId` = `a`.`id`
			ORDER BY `s`.`id`,`a`.`id`,`u`.`move`
			';
        $rs = $db->query($sql)->fetchAll();
        $found = array();
        $r = array();
        for ($i = 0; $i < count($rs); $i++) {
            if (!in_array($rs[$i]['concat'], $found)) $found[] = $rs[$i]['concat'];
            $r[$rs[$i]['concat']][] = $rs[$i];
        }
        for ($i = 0; $i < count($uri); $i++) {
            list($empty, $section, $action, $prefix) = explode('/', $uri[$i]);
            $group = '/' . $section . '/' . $action . '/';
            if (in_array($group, $found)) $furi[] = $uri[$i];
        }
        $uri = $furi; $furi = array();
        $groupped = array();
        for ($i = 0; $i < count($uri); $i++) {
            list($empty, $section, $action, $prefix) = explode('/', $uri[$i]);
            $group = '/' . $section . '/' . $action . '/' . $prefix . '/';
            $groupped[$group]['search'][] = $uri[$i];
            $groupped[$group]['upper'][] = $uri[$i];
        }

        foreach ($groupped as $concat => $group) {
            for ($i = 0; $i < count($group['search']); $i++) {
                list($empty, $section, $action, $prefix) = explode('/', $group['search'][$i]);
                $concat2 = '/' . $section . '/' . $action . '/';
                $groupped[$concat]['replace'][$i] = '/' . $section . '/' . ($r[$concat2][0]['blink'] ? '' : ($r[$concat2][0]['rename'] ? $r[$concat2][0]['alias'] : $action) . '/');
            }
        }

        foreach ($groupped as $concat => $group) {
            list($empty1,$section1, $action1, $prefix1) = explode('/', $concat);
            $concat1 = '/' . $section1 . '/' . $action1 . '/';
            $ids = array();
            for ($i = 0; $i < count($group['search']); $i++) {
                list($empty, $section, $action, $prefix, $id) = explode('/', $group['search'][$i]);
                $ids[] = $id;
            }
            $models = array();
            for ($i = count($r[$concat1])-1; $i >= 0; $i--) {
                if (!in_array($r[$concat1][$i]['entityId'], array_keys($models))) $models[$r[$concat1][$i]['entityId']] = Indi::model($r[$concat1][$i]['entityId']);
            }
            $continue = false;
            for ($i = count($r[$concat1])-1; $i >= 0; $i--) {
                if ($r[$concat1][$i]['prefix'] == $prefix || $continue) {
                    if ($components = $models[$r[$concat1][$i]['entityId']]->fetchAll('`id` IN ("' . implode('","', $ids) . '")')) {
                        if ($i > 0) {
                            $key = $models[$r[$concat1][$i-1]['entityId']]->table() . 'Id';
                        }
                        $ids = array();
                        foreach ($components as $component) {
                            if ($cr) return array($r[$concat1][$i]['entityId'] => $component);
                            for ($j = 0; $j < count($groupped[$concat]['upper']); $j++) {
                                $item = explode('/', $groupped[$concat]['upper'][$j]);
                                if ($item[4] == $component->id) {
                                    if ($i > 0) {
                                        $item[4] = $component->{$key};
                                        $groupped[$concat]['upper'][$j] = implode('/', $item);
                                    }
                                    if (strlen($component->alias))
                                        $groupped[$concat]['revert'][$j][] = $component->alias;
                                }
                            }
                            if ($i > 0) {
                                $ids[] = $component->{$key};
                            }
                        }
                    }
                    $continue = true;
                }
            }
            foreach($group['search'] as $key => $value) {
                $reverted = @array_reverse($groupped[$concat]['revert'][$key]);
                $item = explode('/', $group['search'][$key]);
                for ($i = 5; $i < count($item); $i++) $reverted[] = $item[$i];
                $groupped[$concat]['replace'][$key] .= implode('/', $reverted);
                //if (STD) $groupped[$concat]['replace'][$key] = STD . $groupped[$concat]['replace'][$key];
            }
            $sys = str_replace($groupped[$concat]['search'], $groupped[$concat]['replace'], $sys);
        }
        return $sys;
    }
}