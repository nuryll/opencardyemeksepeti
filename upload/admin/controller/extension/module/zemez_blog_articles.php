<?php
class ControllerExtensionModuleZemezBlogArticles extends Controller {

	private $error = array();

	public function index() {
		$url = $this->request->get['route'];
		if($this->checkDatabase()) {

			$this->load->language('simple_blog/install');

			$this->document->setTitle($this->language->get('error_database'));

			$data['install_database']     = $this->url->link('simple_blog/install/installDatabase', 'user_token=' . $this->session->data['user_token'] . '&url=' . $url, true);
			$data['breadcrumbs']          = array();

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
				'separator' => false
			);

			$data['header']      = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer']      = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('simple_blog/notification', $data));

		} else {
			$this->getData();
		}
	}

	public function checkDatabase() {
		$database_not_found = $this->load->controller('simple_blog/install/validateTable');

		if(!$database_not_found) {
			return true;
		}

		return false;
	}

	public function getData() {
		$this->load->language('extension/module/zemez_blog_articles');

		$data['heading_title'] = strip_tags($this->language->get('heading_title'));
		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('zemez_blog_articles', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['image_width'])) {
			$data['error_image_width'] = $this->error['image_width'];
		} else {
			$data['error_image_width'] = '';
		}

		if (isset($this->error['image_height'])) {
			$data['error_image_height'] = $this->error['image_height'];
		} else {
			$data['error_image_height'] = '';
		}

		if (isset($this->error['description_limit'])) {
			$data['error_description_limit'] = $this->error['description_limit'];
		} else {
			$data['error_description_limit'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => strip_tags($this->language->get('heading_title')),
			'href'      => $this->url->link('extension/module/zemez_blog_articles', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => ' :: '
		);

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/zemez_blog_articles', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/zemez_blog_articles', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}


		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$this->load->model('simple_blog/category');

		$data['categories'] = $this->model_simple_blog_category->getCategories(0);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}


		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['article_limit'])) {
			$data['article_limit'] = $this->request->post['article_limit'];
		} elseif (!empty($module_info)) {
			$data['article_limit'] = $module_info['article_limit'];
		} else {
			$data['article_limit'] = '3';
		}

		if (isset($this->request->post['category_id'])) {
			$data['category_id'] = $this->request->post['category_id'];
		} elseif (!empty($module_info)) {
			$data['category_id'] = $module_info['category_id'];
		} else {
			$data['category_id'] = '';
		}

		if (isset($this->request->post['show_description'])) {
			$data['show_description'] = $this->request->post['show_description'];
		} elseif (!empty($module_info)) {
			$data['show_description'] = $module_info['show_description'];
		} else {
			$data['show_description'] = '';
		}

		if (isset($this->request->post['description_limit'])) {
			$data['description_limit'] = $this->request->post['description_limit'];
		} elseif (!empty($module_info)) {
			$data['description_limit'] = $module_info['description_limit'];
		} else {
			$data['description_limit'] = '100';
		}

		if (isset($this->request->post['image_width'])) {
			$data['image_width'] = $this->request->post['image_width'];
		} elseif (!empty($module_info)) {
			$data['image_width'] = $module_info['image_width'];
		} else {
			$data['image_width'] = '0';
		}

		if (isset($this->request->post['image_height'])) {
			$data['image_height'] = $this->request->post['image_height'];
		} elseif (!empty($module_info)) {
			$data['image_height'] = $module_info['image_height'];
		} else {
			$data['image_height'] = '0';
		}

		if (isset($this->request->post['show_image'])) {
			$data['show_image'] = $this->request->post['show_image'];
		} elseif (!empty($module_info)) {
			$data['show_image'] = $module_info['show_image'];
		} else {
			$data['show_image'] = '';
		}

		if (isset($this->request->post['show_readmore'])) {
			$data['show_readmore'] = $this->request->post['show_readmore'];
		} elseif (!empty($module_info)) {
			$data['show_readmore'] = $module_info['show_readmore'];
		} else {
			$data['show_readmore'] = '';
		}

		if (isset($this->request->post['show_date'])) {
			$data['show_date'] = $this->request->post['show_date'];
		} elseif (!empty($module_info)) {
			$data['show_date'] = $module_info['show_date'];
		} else {
			$data['show_date'] = '';
		}

		if (isset($this->request->post['show_author'])) {
			$data['show_author'] = $this->request->post['show_author'];
		} elseif (!empty($module_info)) {
			$data['show_author'] = $module_info['show_author'];
		} else {
			$data['show_author'] = '';
		}

		if (isset($this->request->post['show_comments'])) {
			$data['show_comments'] = $this->request->post['show_comments'];
		} elseif (!empty($module_info)) {
			$data['show_comments'] = $module_info['show_comments'];
		} else {
			$data['show_comments'] = '';
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/zemez_blog_articles', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/zemez_blog_articles')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!is_numeric($this->request->post['image_width'])) {
			$this->error['image_width'] = $this->language->get('error_width');
		}
		if (!is_numeric($this->request->post['image_height'])) {
			$this->error['image_height'] = $this->language->get('error_height');
		}
		if (!is_numeric($this->request->post['description_limit'])) {
			$this->error['description_limit'] = $this->language->get('error_description_limit');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>