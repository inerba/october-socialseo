<?php namespace Inerba\SocialSeo\Components;

use Cms\Classes\ComponentBase;

class BlogSeo extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'inerba.socialseo::lang.component.blog.name',
            'description' => 'inerba.socialseo::lang.component.blog.description'
        ];
    }

    public function defineProperties()
    {
        return [];
    }
}
