<?php namespace Inerba\SocialSeo\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Request;
use Url;

use Inerba\SocialSeo\Classes\Seo;

class CmsSeo extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'inerba.socialseo::lang.component.cms.name',
            'description' => 'inerba.socialseo::lang.component.cms.description'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $theme = Theme::getActiveTheme();
        $page = Page::load($theme,$this->page->baseFileName);
        $this->page["isCms"] = false;
        $this->is_home = $this->page["is_home"] = false;
        
        if(is_null($page)) {
            $this->isCms = $this->page["isCms"] = true;          
            return;
        }

        if($page->hasComponent("blogPost")) {
            $this->isCms = $this->page["isCms"] = true;          
        } elseif($page->hasComponent("customSeo")) {
            $this->isCms = $this->page["isCms"] = true;
        } else {
            if($this->page->url == "/") {
                $this->is_home = $this->page["is_home"] = true;
            }
            $this->seo_title = $this->page["seo_title"]             = empty($this->page->meta_title) ? $this->page->title : $this->page->meta_title;
            $this->seo_description = $this->page["seo_description"] = Seo::render($this->page->meta_description);
            $this->seo_canonical = $this->page["seo_canonical"] = $this->page->seo_canonical;

            $this->seo_fb_description = $this->page["seo_fb_description"] = Seo::render($this->page->fb_description);
        }

    }
}
