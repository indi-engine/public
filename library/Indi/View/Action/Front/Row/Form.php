<?php
class Indi_View_Action_Front_Row_Form extends Indi_View_Action_Front_Row {
    public function render() {

        // Start output buffering
        ob_start();

        // Echo a <tr> for each form's field, but only if field's control element's 'hidden' checkbox is not checked
        foreach (Indi::trail()->fields as $fieldR)
            if ($fieldR->foreign('elementId')->hidden != 1)
                if (preg_match('/combo|radio|multicheck/', $fieldR->foreign('elementId')->alias))
                    view()->formCombo($fieldR->alias, null, 'extjs');

        // Return buffered output with parent's return-value
        return ob_get_clean() . parent::render();
    }
}