<?php


defined('_JEXEC') or die('Restricted access');

/*
	Class: ElementSeparator
		The Separator element class
*/
class ElementSeparator extends ElementPro implements iSubmittable {

	/*
		Function: render
			Override. Renders the element.

	   Parameters:
			$params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {
		return null;
	}

	/*
	   Function: edit
		   Renders the edit form field.

	   Returns:
		   String - html
	*/
	public function edit()
	{
		if ($layout = $this->getLayout('edit/'.$this->config->find('layout._layout', 'default.php'))) {
			return $this->renderLayout($layout);
		}
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
			$params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
		return $this->edit();
	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
			$value  - AppData value
			$params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params){
		return array();
	}

}
