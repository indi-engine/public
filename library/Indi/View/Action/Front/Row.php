<?php
class Indi_View_Action_Front_Row extends Indi_View_Action_Front {
    public function render() {

        // Start output buffering
        ob_start();

        // Return buffered contents with parent's return-value
        return ob_get_clean() . parent::render();
    }
}