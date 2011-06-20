<?php

class Twm_Core_Block_Template extends Twm_Core_Block_Abstract {

	public function beforeToHtml() {}
	public function afterToHtml() {}

	function toHtml() {
		$view = $this->getView();
		$this->beforeToHtml();
		if ($this->hasTemplate()) {
			$old = $view->block;
			$view->block = $this;
			$html = $view->render($this->getTemplate());
			$view->block = $old;
			$this->afterToHtml();
			return $html;
		}
	}
}