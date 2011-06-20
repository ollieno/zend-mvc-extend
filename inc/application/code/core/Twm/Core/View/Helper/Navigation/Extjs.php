<?php

class Twm_Core_View_Helper_Navigation_Extjs extends Zend_View_Helper_Navigation_Menu {

	public function extjs(Zend_Navigation_Container $container = null) {
		return parent::menu($container);
	}

	protected function _renderMenu(Zend_Navigation_Container $container, $ulClass, $indent, $minDepth, $maxDepth, $onlyActive) {
		$array = array();
		$menu = &$array;

		// find deepest active
		if ($found = $this->findActive($container, $minDepth, $maxDepth)) {
			$foundPage = $found['page'];
			$foundDepth = $found['depth'];
		} else {
			$foundPage = null;
		}

		// create iterator
		$iterator = new RecursiveIteratorIterator($container,
				RecursiveIteratorIterator::SELF_FIRST);
		if (is_int($maxDepth)) {
			$iterator->setMaxDepth($maxDepth);
		}

		// iterate container
		$prevDepth = 0;
		foreach ($iterator as $page) {
			$depth = $iterator->getDepth();
			$isActive = $page->isActive(true);
			if ($depth < $minDepth || !$this->accept($page)) {
				// page is below minDepth or not accepted by acl/visibilty
				continue;
			} else if ($onlyActive && !$isActive) {
				// page is not active itself, but might be in the active branch
				$accept = false;
				if ($foundPage) {
					if ($foundPage->hasPage($page)) {
						// accept if page is a direct child of the active page
						$accept = true;
					} else if ($foundPage->getParent()->hasPage($page)) {
						// page is a sibling of the active page...
						if (!$foundPage->hasPages() ||
							is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
							// accept if active page has no children, or the
							// children are too deep to be rendered
							$accept = true;
						}
					}
				}

				if (!$accept) {
					continue;
				}
			}

			// make sure indentation is correct
			$depth -= $minDepth;

			if ($depth > $prevDepth) {
				for ($i = 0; $i < $depth - $prevDepth; $i++) {
					$count = count($menu);
					$index = ($count > 0) ? $count - 1 : 0;
					$item = &$menu[$index];
					if (!isset($item['menu'])) {
						$item['menu'] = array();
					}
					$menu = &$item['menu'];
				}
			} elseif ($prevDepth > $depth) {
				$menu = &$array;
				for ($i = 0; $i < $depth; $i++) {
					$count = count($menu);
					$index = ($count > 0) ? $count - 1 : 0;
					$item = &$menu[$index];
					if (!isset($item['menu'])) {
						$item['menu'] = array();
					}
					$menu = &$item['menu'];
				}
			}

			$menu[] = $this->toXtype($page);

			$prevDepth = $depth;
		}

		return Zend_Json::encode($array);
	}

	public function toXtype(Zend_Navigation_Page $page) {
		// get label and title for translating
		$label = $page->getLabel();
		$title = $page->getTitle();

		// translate label and title?
		if ($this->getUseTranslator() && $t = $this->getTranslator()) {
			if (is_string($label) && !empty($label)) {
				$label = $t->translate($label);
			}
			if (is_string($title) && !empty($title)) {
				$title = $t->translate($title);
			}
		}

		// get attribs for element
		$attribs = array(
			'id' => $page->getId(),
			'text' => $label,
			'iconCls' => $page->get('iconClass'),
			'cls' => $page->getClass()
		);

		// does page have a href?
		if ($href = $page->getHref()) {
			$attribs['href'] = $href;
			$attribs['hrefTarget'] = $page->getTarget();
		}

		foreach ($attribs as $key => &$attr) {
			if (empty($attr)) {
				unset($attribs[$key]);
			}
		}

		return $attribs;
	}

}
