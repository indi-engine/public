<?php
class Indi_View_Helper_FormNumber {

    public function formNumber($name, $value = null, $attribs = null) {
		if (!$value) $value = '0';
        $filtered = $attribs;
		unset($filtered['onchange']);
		$xhtml = '<input type="text"'
			   . ' name="' . view()->escape($name) . '"'
			   . ' id="' . view()->escape($name) . '"'
			   . ' value="' . view()->escape($value) . '"'
			   . ' style="width: ' . ($attribs['maxlength']*10) . 'px; text-align: right;" maxlength="' . $attribs['maxlength'] . '" onchange="this.value=number(this.value);' . $attribs['onchange'] . '" /> ' . $attribs['measure'];
        
        return $xhtml;
    }    
}