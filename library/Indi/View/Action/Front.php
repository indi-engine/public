<?php
class Indi_View_Action_Front extends Indi_View_Action_Admin {
    public function render(){
        return '<script>var json=' . parent::render() . '</script>';
    }
}