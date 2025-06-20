<?php
class Document
{
    private $title;
    private $description;
    private $keywords;
    private $links = array();
    private $styles = array();
    private $scripts = array();

    public function displayOrder(&$totals, $country_id = null)
    {
        //echo count($totals); die();
        //print_r($country_id);
        if (count($totals) == 3) {
            //print_r($totals);
            if ((int) $totals[0]['value'] <= 45) {
                $totals[1]['value'] = 0;

                $totals[2]['value'] = $totals[0]['value'] + $totals[1]['value'];
            }
        }
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function addLink($href, $rel)
    {
        $this->links[$href] = array(
            'href' => $href,
            'rel' => $rel,
        );
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function addStyle($href, $rel = 'stylesheet', $media = 'screen')
    {
        $this->styles[$href] = array(
            'href' => $href,
            'rel' => $rel,
            'media' => $media,
        );
    }

    public function getStyles()
    {
        return $this->styles;
    }

    public function addScript($href, $postion = 'header')
    {
        $this->scripts[$postion][$href] = $href;
    }

    public function getScripts($postion = 'header')
    {
        if (isset($this->scripts[$postion])) {
            return $this->scripts[$postion];
        } else {
            return array();
        }
    }
}
