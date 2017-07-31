<?php namespace Inerba\SocialSeo\Components;

use Cms\Classes\ComponentBase;
use RainLab\Pages\Classes\Router;
use Cms\Classes\Theme;
use Inerba\SocialSeo\models\Settings;
use Inerba\SocialSeo\Classes\Seo;
use Request;

class PageSeo extends ComponentBase
{
    public $seo_title;
    public $seo_description;
    public $seo_canonical;
    public $seo_fb_description;

    public function componentDetails()
    {
        return [
            'name'        => 'inerba.socialseo::lang.component.static.name',
            'description' => 'inerba.socialseo::lang.component.static.description'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $url = Request::path();
        // Remove language prefix in case it exists (e.g. from "/en/my-page" to "/my-page")
        if (class_exists('RainLab\Translate\Behaviors\TranslatableModel'))
            $url = substr($url, 3);
        if (!strlen($url))
            $url = '/';

        $router = new Router(Theme::getActiveTheme());
        
        $this->page = $this->page['page'] = $router->findByUrl($url);

        if ($this->page) {

            $p = (object) $this->page->getViewBag()->getProperties();
           // d($p);

            $this->seo_title = $this->page["seo_title"]             = empty($p->meta_title) ? $p->title : $p->meta_title;
            $this->seo_description = $this->page["seo_description"] = empty($p->meta_description) ? null : Seo::render($p->meta_description);
            $this->seo_canonical = $this->page["seo_canonical"]     = $p->socialseo['canonical_url'];

            //$this->seo_fb_description = $this->page["seo_fb_description"] = Seo::render($this->page->fb_description);

            //dump($p->meta_title, $p->title, $this->page["seo_title"]);
        }
    }

}
