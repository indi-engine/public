<?php
class Indi_View_Helper_IndexParams extends Indi_View_Helper_Abstract{
	public function indexParams($withDir = false){
		$xhtml = '
<form action="" method="post" id="indexParams" name="indexParams">
	<input type="hidden" id="indexPage" name="indexPage" value="' . Indi::view()->indexParams['page'] . '"/>
	<input type="hidden" id="indexLimit" name="indexLimit" value="' . Indi::view()->indexParams['limit'] . '"/>
	<input type="hidden" id="indexOrder" name="indexOrder" value="' . Indi::view()->indexParams['order'] . '"/>
';
		if ($withDir) $xhtml .= '<input type="hidden" id="indexDir" name="indexDir" value="' . Indi::view()->indexParams['dir'] . '"/>';
		$filters = Indi::view()->section->getFilters();
		foreach ($filters as $filter) {
			$column = $filter->foreign('fieldId')->alias;
			if ($filter->type == 'b') {
				$xhtml .= '<input type="hidden" id="indexWhere[' . $column . 'From]" name="indexWhere[' . $column . 'From]" value="' . Indi::view()->indexParams['where'][$column. 'From'] . '"/>';
				$xhtml .= '<input type="hidden" id="indexWhere[' . $column . 'To]" name="indexWhere[' . $column . 'To]" value="' . Indi::view()->indexParams['where'][$column. 'To'] . '"/>';
			} else {
				$xhtml .= '<input type="hidden" id="indexWhere[' . $column . ']" name="indexWhere[' . $column . ']" value="' . Indi::view()->indexParams['where'][$column] . '"/>';
			}
		}
		$xhtml .= '</form>';
		return $xhtml;
	}
}