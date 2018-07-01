<?php
class Menu extends Indi_Db_Table {
	protected $_rowsetClass = 'Menu_Rowset';
	public function init()	{
        $rowset = $this->fetchTree('`toggle` = "y"', 'move')->foreign('staticpageId');
        foreach ($rowset as $row) {
			if ($row->linked == 'n') {
				$row->href = $row->url;
			} else if ($row->foreign('staticpageId')->alias == 'index') {
				$row->href = '/';
			} else if ($row->foreign('staticpageId')->alias) {
				$row->href = '/' . $row->foreign('staticpageId')->alias . '/';
			}
			$rows[] = $row;
		}
        $menuRs = $rowset->toNestingTree();
        return $menuRs;
	}
}
