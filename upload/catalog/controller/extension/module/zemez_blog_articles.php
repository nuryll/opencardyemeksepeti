<?php

class ControllerExtensionModuleZemezBlogArticles extends Controller
{

	public function index($setting) {
		static $module = 0;
		$this->load->language('extension/module/zemez_blog_articles');
		$this->load->model('simple_blog/article');
		$data = array();

		$data['articles']             = array();
		$data['heading_title']        = $this->language->get('zemez_heading_title');

		if ($setting['category_id'] == 'all') {
			$data['heading_title'] = $this->language->get('text_latest_all');
			$data['article_link']  = $this->url->link('simple_blog/article');
		} elseif ($setting['category_id'] == 'popular') {
			$data['heading_title'] = $this->language->get('text_popular_all');
			$data['article_link']  = $this->url->link('simple_blog/article');
		} else {
			$category_info         = $this->model_simple_blog_article->getCategory($setting['category_id']);
			$data['heading_title'] = $category_info['name'];
			$data['article_link']  = $this->url->link('simple_blog/simple_category', 'simple_blog_category_id=' . $setting['category_id']);
		}

		if ($setting['category_id'] == 'all') {
			$filter_data = array(
				'start' => 0,
				'limit' => $setting['article_limit']
			);

			$results = $this->model_simple_blog_article->getArticleModuleWise($filter_data);

		} else if ($setting['category_id'] == 'popular') {
			$filter_data = array(
				'start' => 0,
				'limit' => $setting['article_limit']
			);

			$results = $this->model_simple_blog_article->getPopularArticlesModuleWise($filter_data);

		} else {
			$filter_data = array(
				'filter_category_id' => $setting['category_id'],
				'start' => 0,
				'limit' => $setting['article_limit']
			);

			$results = $this->model_simple_blog_article->getArticleModuleWise($filter_data);
		}

		$data['show_image']       = $setting['show_image'];
		$data['show_readmore']    = $setting['show_readmore'];
		$data['show_date']        = $setting['show_date'];
		$data['show_author']      = $setting['show_author'];
		$data['show_description'] = $setting['show_description'];
		$data['show_comments']    = $setting['show_comments'];
		$data['image_width']      = $setting['image_width'];
		$data['image_height']     = $setting['image_height'];

		foreach ($results as $result) {

			if ($setting['description_limit']) {
				$description = utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $setting['description_limit']) . '...';
			} else {
				$description = utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '...';
			}

			if ($setting['show_image'] == 1) {
				$this->load->model('tool/image');
				if ($result['featured_image']) {
					$image = $this->model_tool_image->resize($result['featured_image'], $setting['image_width'], $setting['image_height']);
				} else if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
				}
			}else{
				$image = '';
			}

			$total_comments = $this->model_simple_blog_article->getTotalComments($result['simple_blog_article_id']);

			if ($total_comments != 1) {
				$total_comments .= $this->language->get('text_comments');
			} else {
				$total_comments .= $this->language->get('text_comment');
			}

			$data['articles'][] = array(
				'simple_blog_article_id' => $result['simple_blog_article_id'],
				'article_title'          => $result['article_title'],
				'author_name'            => $result['author_name'],
				'image'                  => $image,
				'image_width'            => $setting['image_width'],
				'image_height'           => $setting['image_height'],
				'featured_found'         => '',
				'date_added'             => date($this->language->get('text_date_format'), strtotime($result['date_modified'])),
				'description'            => $description,
				'allow_comment'          => $result['allow_comment'],
				'total_comment'          => $total_comments,
				'href'                   => $this->url->link('simple_blog/article/view', 'simple_blog_article_id=' . $result['simple_blog_article_id'], true),
				'author_href'            => $this->url->link('simple_blog/author', 'simple_blog_author_id=' . $result['simple_blog_author_id'], true),
				'comment_href'           => $this->url->link('simple_blog/article/view', 'simple_blog_article_id=' . $result['simple_blog_article_id'] . '#comment-section', true)
			);
		}

		$data['text_no_found'] = $this->language->get('text_no_result');

		$data['module'] = $module++;

		return $this->load->view('extension/module/zemez_blog_articles', $data);
	}
}

?>