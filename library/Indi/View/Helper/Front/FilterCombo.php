<?php
class Indi_View_Helper_Front_FilterCombo extends Indi_View_Helper_FilterCombo {
    public function getController() {
        return Indi_Trail_Front::$controller;
    }
}