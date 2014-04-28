<?php
class Indi_View_Helper_IndexOrder extends Indi_View_Helper_Abstract{
	public function indexOrder(){
		return Indi::view()->formSelect('order', Indi::view()->section->getOrder(), Indi::view()->indexParams['order'], array(
				'class' => 'saas-select', 
				'onchange' => 'document.getElementById(\'indexOrder\').value=this.value;document.getElementById(\'indexParams\').submit()'
			)
		);
	}
}