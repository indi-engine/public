<?php
class Indi_View_Helper_FormNumber extends Indi_View_Helper_FormElement
{
    public function formNumber($name, $value = null, $attribs = null)
    {
		if (!$value) $value = '0';
        $filtered = $attribs;
		unset($filtered['onchange']);
		$xhtml = '<input type="text"'
			   . ' name="' . Indi::view()->escape($name) . '"'
			   . ' id="' . Indi::view()->escape($name) . '"'
			   . ' value="' . Indi::view()->escape($value) . '"'
			   . $this->_htmlAttribs($filtered)
			   . ' style="width: ' . ($attribs['maxlength']*10) . 'px; text-align: right;" maxlength="' . $attribs['maxlength'] . '" onchange="this.value=number(this.value);' . $attribs['onchange'] . '" /> ' . $attribs['measure'];
        
        return $xhtml;
    }    
}