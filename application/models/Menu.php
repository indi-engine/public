<?php
class Menu extends Indi_Db_Table {
	protected $_rowsetClass = 'Menu_Rowset';
	public function init($uri = URI, $parentId = 0, $onlyToggledOn = true, $recursive = true, $level = 0, $order = '`move`', $WHERE = null)
	{
        $where = array('`' . $this->treeColumn() . '` = "' . (int) $parentId . '"');
        if ($onlyToggledOn) $where[] = '`toggle`="y"';
        if (strlen($WHERE)) $where[] = $WHERE;
		$rowset = $this->fetchAll($where, $order)->foreign('staticpageId');
		$i = 0;
        $dec = 0;
        foreach ($rowset as $row) {
            if($row->foreign('staticpageId')->toggle == 'n') {
                $dec++;
                continue;
            };
			$row->indent = indent($level);

			if ($recursive)
				$row->children = $this->init($uri, $row->id, $onlyToggledOn, $recursive, $level+1, $order, $WHERE);

			if ($row->linked == 'n') {
				$row->href = $row->url;
			} else if ($row->foreign('staticpageId')->alias == 'index') {
				$row->href = '/';
			} else {
				$row->href = '/' . $row->foreign('staticpageId')->alias . '/';
			}

			if (trim($row->href, '/') == trim($uri, '/') || $row->children->activeBranch) {
				$row->active = true;
				$activeItemHere = true;
			}
			$rows[] = $row;
			$i++;
		}
        $rows[$rowset->count() - $dec - 1]['last'] = true;
		$data = array ('table' => $this->_table, 'rows' => $rows, 'rowClass' => $this->_rowClass, 'found' => count($rows) - $dec);
		if ($activeItemHere) $data['activeBranch'] = true;
		return new $this->_rowsetClass($data);
	}

}
