<?php namespace Inerba\SocialSeo\Components;

use Cms\Classes\ComponentBase;

class CustomSeo extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'customSeo Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            "component_data" => [
                "title" => "Data from",
                "default" => "post"
            ],
            "debug_item" => [
                "title" => "Debug",
                "default" => 0,
                "type" => "checkbox",
            ],
            "seo_title" => [
                "title" => "Title data from",
                "group" => "Mapping",
            ],
            "seo_description" => [
                "title" => "Description data from",
                "group" => "Mapping",
            ],
            "seo_image" => [
                "title" => "Image data from",
                "group" => "Mapping",
            ]
        ];
    }

    public function onRun()
    {
        $p = $this->getProperties();

        $cd = $this->page->{$p['component_data']};

        $seo = [];

        if(isset($p['seo_title']) && isset($cd[$p['seo_title']])){
            $seo['title'] = $this->page["seo_title"] = $cd->{$p['seo_title']};
        }

        if(isset($p['seo_description']) && isset($cd[$p['seo_description']])){
            $seo['description'] = $this->page["seo_description"] = $cd->{$p['seo_description']};
        }

        if(isset($p['seo_image']) && isset($cd[$p['seo_image']])){
            $seo['og_image'] = $this->page["seo_image"] = $cd->{$p['seo_image']};
        }

        if($p['debug_item']) {
            dump($cd->{$p['seo_image']});
            dump(
                $seo
            );
        }
    }
}
