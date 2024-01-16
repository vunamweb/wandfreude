<?php
class ControllerExtensionModuleOcinstagram extends Controller {
    public function index($setting) {
        $this->load->language('extension/module/ocinstagram');

        $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');

        $data = array();

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title2'] = $this->language->get('heading_title2');
        $data['text_follow'] = $this->language->get('text_follow');
        $data['text_des'] = $this->language->get('text_des');
        $data['text_copyright'] = sprintf($this->language->get('text_copyright'), $this->config->get('config_name'));

        if (empty($setting['limit'])) {
            $setting['limit'] = 10;
        }

        $lang_code = $this->session->data['language'];

        if(isset($setting['title']) && $setting['title']) {
            $data['title'] = $setting['title'][$lang_code]['title'];
        }

        $file = "https://api.instagram.com/v1/users/".$setting['userid']."/media/recent/?access_token=".$setting['access_token'];

        $instagram_json_data = json_decode(file_get_contents($file),true);
        $instagram_arrays = $instagram_json_data['data'];

        $data['username'] = $instagram_arrays[0]['user']['username'];

        $data['instagrams'] = array();

        foreach($instagram_arrays as $key => $instagram_array) {
            $instagrams[] = array(
                'likes' => $instagram_array['likes']['count'],
                'small_image' => $instagram_array['images']['thumbnail']['url'], // 150x150
                'medium_image' => $instagram_array['images']['low_resolution']['url'], // 320x320
                'image' => $instagram_array['images']['standard_resolution']['url'], // 640x640
                'link'  => $instagram_array['link'],
                'created_time' => date('m/d/Y', $instagram_array['created_time']),
                'comment' => $instagram_array['comments']['count'],
                'caption' => $instagram_array['caption']['text'],
            );
            if ($key == $setting['limit'] - 1) break;
        };

        $data['instagrams'] = $instagrams;

        $data['config_slide'] = array(
            'items' => $setting['item'],
            'autoplay' => $setting['autoplay'],
            'f_show_nextback' => $setting['shownextback'],
            'f_show_ctr' => $setting['shownav'],
            'f_speed' => $setting['speed'],
            'f_rows' => $setting['rows'],
            'f_view_mode' => $setting['view_mode']
        );

        return $this->load->view('extension/module/ocinstagram', $data);
    }
}