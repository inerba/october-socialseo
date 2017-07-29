<?php namespace Inerba\SocialSeo\Components;

use Cms\Classes\ComponentBase;

class PageSeo extends ComponentBase
{
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
}
