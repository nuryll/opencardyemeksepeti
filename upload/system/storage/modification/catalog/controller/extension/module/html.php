<?php
class ControllerExtensionModuleHTML extends Controller {
	public function index($setting) {
 static $module = 0; $data['module'] = $module++; 
		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
 $data['class'] = $setting['class']; 
			$data['html'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');

			return $this->load->view('extension/module/html', $data);
		}
	}
}