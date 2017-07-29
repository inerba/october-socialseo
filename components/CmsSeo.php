<?php namespace Inerba\SocialSeo\Components;

use Cms\Classes\ComponentBase;

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
}
