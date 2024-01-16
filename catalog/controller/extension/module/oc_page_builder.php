<?php
class ControllerExtensionModuleOcPageBuilder extends Controller
{
    public function index($setting) {
        $this->load->model('extension/module');

        if(!empty($setting['widget'])) {
            $widgets = $setting['widget'];
        } else {
            $widgets = array();
        }

        $data['widgets'] = array();

        foreach($widgets as $rows) {
            $row_info = array();

            foreach($rows['cols'] as $cols) {
                $col_info = array();

                if(isset($cols['info']) && $cols['info']) {
                    foreach($cols['info'] as $modules) {
                        $module_in_col = array();
                        foreach ($modules as $module) {
                            $part = explode('.', $module['code']);

                            if (isset($part[0]) && $this->config->get($part[0] . '_status')) {
                                $module_data = $this->load->controller('extension/module/' . $part[0]);

                                if ($module_data) {
                                    $module_in_col[] = $module_data;
                                }
                            }

                            if (isset($part[1])) {
                                $setting_info = $this->model_extension_module->getModule($part[1]);

                                if ($setting_info && $setting_info['status']) {
                                    $module_data = $this->load->controller('extension/module/' . $part[0], $setting_info);

                                    if ($module_data) {
                                        $module_in_col[] = $module_data;
                                    }
                                }
                            }
                            $col_info['info'] = $module_in_col;
                            $col_info['format'] = $cols['format'];
                        }
                    }
                }
                $row_info['cols'][] = $col_info;
            }
            $row_info['class'] = $rows['class'];

            $data['widgets'][] = $row_info;
        }
		
		if (file_exists('catalog/view/theme/' . $this->config->get($this->config->get('config_theme') . '_directory') . '/stylesheet/opentheme/ocpagebuilder.css')) {
			$this->document->addStyle('catalog/view/theme/' . $this->config->get($this->config->get('config_theme') . '_directory') . '/stylesheet/opentheme/ocpagebuilder.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/opentheme/ocpagebuilder.css');
		}

        return $this->load->view('common/layout_content_built', $data);
    }
}