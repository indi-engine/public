<?php
class Menu extends Indi_Db_Table {

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Menu_Rowset';

    /**
     * Get nesting rowset of menu items, with auto-built `href` props and auto-marked as `active` where need
     *
     * @return Indi_Db_Table_Rowset
     */
    public function init()	{

        // Fetch menu items
        $rowset = $this->fetchTree('`toggle` = "y"', 'move')->foreign('staticpageId');

        // Collect indexes by ids
        foreach ($rowset as $idx => $row) $idA[$row->id] = $idx;

        // Shortcut to request uri
        $ruri = $_SERVER['REQUEST_URI'];

        // Foreach menu item
        foreach ($rowset as $row) {

            // Build href
            if ($row->linked == 'n') {
                $row->href = $row->url;
            } else if ($row->foreign('staticpageId')->alias == 'index') {
                $row->href = '/';
            } else if ($row->foreign('staticpageId')->alias) {
                $row->href = '/' . $row->foreign('staticpageId')->alias . '/';
            }

            // Mark as active where need
            if ($row->href == '/') {
                if ($ruri == $row->href) $row->active = true;
            } else if (preg_match('~^' . preg_quote($row->href, '~') . '~', $_SERVER['REQUEST_URI'])) {
                $row->active = true;
                $upperID = $row->menuId;
            }
        }

        // Mark parent menu items as active
        while (array_key_exists($upperID, $idA))
           $upperID = $rowset->at($idA[$upperID])->assign(array('active' => true))->menuId;

        // Return nesting tree
        return $rowset->toNestingTree();
    }
}
