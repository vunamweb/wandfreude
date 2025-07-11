<?php  
class ControllerExtensionModuleOcblog extends Controller
{
	public function index($setting) {
		$this->load->model('blog/article');
        $this->load->language('blog/blog');
		
		$data['text_showmore'] = $this->language->get('text_showmore');
		$data['text_post_by'] = $this->language->get('text_post_by');
		
		$data['blog'] = $this->url->link('blog/blog');

		//$this->document->setTitle($this->config->get('ocblog_meta_title'));
		$this->document->setDescription($this->config->get('ocblog_meta_description'));
		$this->document->setKeywords($this->config->get('ocblog_meta_keyword'));
		$this->document->addLink($this->url->link('blog/blog'),'');

		if (isset($setting['name'])) {
			$data['text_headingtitle'] = $setting['name'];
		} else {
			$data['text_headingtitle'] = $this->language->get('text_headingtitle');
		}

		$lang_code = $this->session->data['language'];

		if(isset($setting['title']) && $setting['title']) {
			$data['title'] = $setting['title'][$lang_code]['title'];
		} else {
			$data['title'] = $this->language->get('text_headingtitle');
		}

		if (isset($setting['limit'])) {
			$limit = $setting['limit'];
		} else {
			$limit = 10;
		}

		if (isset($setting['rows'])) {
			$rows = $setting['rows'];
		} else {
			$rows = 1;
		}

		if (isset($setting['items'])) {
			$items = $setting['items'];
		} else {
			$items = 4;
		}

		if (isset($setting['speed'])) {
			$speed = $setting['speed'];
		} else {
			$speed = 3000;
		}

		if (isset($setting['auto']) && $setting['auto']) {
			$auto = true;
		} else {
			$auto = false;
		}

		if (isset($setting['navigation']) && $setting['navigation']) {
			$navigation = true;
		} else {
			$navigation = false;
		}

		if (isset($setting['pagination']) && $setting['pagination']) {
			$pagination = true;
		} else {
			$pagination = false;
		}

		$data['articles'] = array();

		$filter_data = array(
			'start'              => 0,
			'limit'              => $limit
		);

		$results = $this->model_blog_article->getArticlesByList($filter_data, $setting['list']);

		$this->load->model('tool/image');

		foreach ($results as $result) {
			$data['articles'][] = array(
				'article_id'  => $result['article_id'],
				'name'        => $result['name'],
				'author'	  => $result['author'],
				'image'		  => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
				'date_added'  => date('d', strtotime($result['date_added'])),
                'date_added2'  => date('M', strtotime($result['date_added'])),
                'date_added4'  => date('M d, Y', strtotime($result['date_added'])),
                'date_added3'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'intro_text' => html_entity_decode($result['intro_text'], ENT_QUOTES, 'UTF-8'),
				'href'        => $this->url->link('blog/article', 'article_id=' . $result['article_id'])
			);
		}

		$data['slide'] = array(
			'auto' => $auto,
			'rows' => $rows,
			'navigation' => $navigation,
			'pagination' => $pagination,
			'speed' => $speed,
			'items' => $items
		);

		$data['button_read_more'] = $this->language->get('button_read_more');
		$data['text_empty'] = $this->language->get('text_empty');

		return $this->load->view('blog/blog_home.tpl', $data);
    }
}