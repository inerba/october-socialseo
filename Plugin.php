<?php namespace Inerba\SocialSeo;

use Backend;

use Cms\Classes\Page;
use Cms\Classes\Theme;

use System\Classes\PluginBase;
use System\Classes\PluginManager;
use System\Classes\SettingsManager;

use Inerba\SocialSeo\Classes\Seo;
use Inerba\SocialSeo\Models\Settings;

//use RainLab\Blog\Models\Post;

/**
 * Seo Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'inerba.socialseo::lang.plugin.name',
            'description' => 'inerba.socialseo::lang.plugin.description',
            'author'      => 'Inerba',
            'icon'        => 'icon-leaf'
        ];
    }

    public $require = ['RainLab.Blog','RainLab.Pages','Inerba.Geolocation'];

    public function boot()
    {

        // INJECT TEXTCOUNT JS
        \Event::listen('backend.page.beforeDisplay', function($controller, $action, $params) {
            $controller->addJs('/plugins/inerba/socialseo/assets/js/calculate_text_length.js');
        });
    
        if( PluginManager::instance()->hasPlugin('RainLab.Blog') )
        {
            
            \RainLab\Blog\Models\Post::extend(function($model){

                //$model->jsonable(array_merge($model->getJsonable(), ["socialseo"]));
                $model->addJsonable(["socialseo"]);
                //$model->attachOne = array_merge( $model->attachOne, ['social_image' => 'System\Models\File'] );
                $model->attachOne['social_image'] = 'System\Models\File';

            });

        }

    }

    public function register()
    {
        \Event::listen('backend.form.extendFields', function ($widget) {
            if (PluginManager::instance()->hasPlugin('RainLab.Pages') && $widget->model instanceof \RainLab\Pages\Classes\Page) {

                $widget->removeField('viewBag[meta_title]');
                $widget->removeField('viewBag[meta_description]');

                $widget->addFields([
                    'viewBag[meta_title]' => [
                        "label" => "rainlab.pages::lang.editor.title",
                        'type'    => 'textcount',
                        'maxlen'  => Settings::get('seo_title_maxlength'),
                        'tab'     => 'cms::lang.editor.meta',
                        'span'    => 'full'
                    ],
                    'viewBag[meta_description]' => [
                        "label" => "cms::lang.editor.meta_description",
                        'type'    => 'textareacount',
                        'maxlen'  => Settings::get('seo_meta_description_maxlength'),
                        'tab'     => 'cms::lang.editor.meta',
                        'span'    => 'full'
                    ],
                    'viewBag[socialseo][keywords]' => [
                        'label'   => 'inerba.socialseo::lang.editor.meta_keywords',
                        'type'    => 'taglist',
                        'separator'    => 'comma',
                        'tab'     => 'cms::lang.editor.meta'
                    ],
                    'viewBag[socialseo][canonical_url]' => [
                        'label'   => 'inerba.socialseo::lang.editor.canonical_url',
                        'type'    => 'text',
                        'tab'     => 'cms::lang.editor.meta',
                        'span'    => 'left'
                    ],
                    'viewBag[socialseo][robot]' => [
                        'label'   => 'inerba.socialseo::lang.editor.robot',
                        'type'    => 'dropdown',
                        'tab'     => 'cms::lang.editor.meta',
                        'options' => $this->getRobotOptions(),
                        'default' => 'index',
                        'span'    => 'right'
                    ],
                    'settings[fb_description]' => [
                        'label'   => 'inerba.socialseo::lang.editor.social.fb_description',
                        'type'    => 'textareacount',
                        'maxlen'  => Settings::get('seo_facebook_maxlength'),
                        'tab'     => 'Social',
                        'span'    => 'full'
                    ],
                    'settings[tw_description]' => [
                        'label'   => 'inerba.socialseo::lang.editor.social.tw_description',
                        'type'    => 'textareacount',
                        'maxlen'  => Settings::get('seo_twitter_maxlength'),
                        'tab'     => 'Social',
                        'span'    => 'full'
                    ],
                    'settings[social_image]' => [
                        'label'   => 'inerba.socialseo::lang.editor.social.social_image',
                        'type'    => 'mediafinder',
                        'mode'    => 'image',
                        'tab'     => 'Social',
                    ],
                ],
                'primary');
            }
            if (PluginManager::instance()->hasPlugin('RainLab.Blog') && $widget->model instanceof \RainLab\Blog\Models\Post) {
                $widget->addFields([
                        'socialseo[title]' => [
                            'label'   => 'inerba.socialseo::lang.editor.meta_title',
                            'type'    => 'textcount',
                            'maxlen'  => 60,
                            'tab'     => 'SEO'
                        ],
                        'socialseo[description]' => [
                            'label'   => 'inerba.socialseo::lang.editor.meta_description',
                            'type'    => 'textareacount',
                            'maxlen'  => 155,
                            'tab'     => 'SEO'
                        ],
                        'socialseo[keywords]' => [
                            'label'   => 'inerba.socialseo::lang.editor.meta_keywords',
                            'type'    => 'taglist',
                            'separator'    => 'comma',
                            'tab'     => 'SEO'
                        ],
                        'socialseo[canonical_url]' => [
                            'label'   => 'inerba.socialseo::lang.editor.canonical_url',
                            'type'    => 'text',
                            'tab'     => 'SEO',
                            'span'    => 'left'
                        ],
                        'socialseo[robot]' => [
                            'label'   => 'inerba.socialseo::lang.editor.robot',
                            'type'    => 'dropdown',
                            'tab'     => 'SEO',
                            'options' => $this->getRobotOptions(),
                            'default' => 'index',
                            'span'    => 'right'
                        ],
                        'socialseo[fb_description]' => [
                            'label'   => 'inerba.socialseo::lang.editor.social.fb_description',
                            'type'    => 'textareacount',
                            'maxlen'  => 255,
                            'tab'     => 'Social',
                            'span'    => 'full'
                        ],
                        'socialseo[tw_description]' => [
                            'label'   => 'inerba.socialseo::lang.editor.social.tw_description',
                            'type'    => 'textareacount',
                            'maxlen'  => 140,
                            'tab'     => 'Social',
                            'span'    => 'full'
                        ],
                        'social_image' => [
                            'label'   => 'inerba.socialseo::lang.editor.social.social_image',
                            'type'    => 'fileupload',
                            'mode'    => 'image',
                            'tab'     => 'Social',
                        ],
                    ],
                    'secondary');
            }
            
            if (!$widget->model instanceof \Cms\Classes\Page) {
                return;
            }
            
            if (!($theme = Theme::getEditTheme())) {
                throw new ApplicationException(Lang::get('cms::lang.theme.edit.not_found'));
            }

            $widget->removeField('settings[meta_title]');
            $widget->removeField('settings[meta_description]');

            $widget->addFields(
                [
                    'settings[meta_title]' => [
                        "label" => "cms::lang.editor.meta",
                        'type'    => 'textcount',
                        'maxlen'  => 60,
                        'tab'     => 'cms::lang.editor.meta',
                        'span'    => 'full'
                    ],
                    'settings[meta_description]' => [
                        "label" => "cms::lang.editor.meta",
                        'type'    => 'textareacount',
                        'maxlen'  => 155,
                        'tab'     => 'cms::lang.editor.meta',
                        'span'    => 'full'
                    ],
                    'settings[seo_keywords]' => [
                        'label'   => 'inerba.socialseo::lang.editor.meta_keywords',
                        'type'    => 'taglist',
                        'separator'    => 'comma',
                        'tab'     => 'cms::lang.editor.meta',
                    ],
                    'settings[seo_canonical]' => [
                        'label'   => 'inerba.socialseo::lang.editor.canonical_url',
                        'type'    => 'text',
                        'tab'     => 'cms::lang.editor.meta',
                        'span'    => 'left'
                    ],
                    'settings[robot]' => [
                        'label'   => 'inerba.socialseo::lang.editor.robot',
                        'type'    => 'dropdown',
                        'tab'     => 'cms::lang.editor.meta',
                        'options' => $this->getRobotOptions(),
                        'default' => 'index',
                        'span'    => 'right'
                    ],
                    'settings[fb_description]' => [
                        'label'   => 'inerba.socialseo::lang.editor.social.fb_description',
                        'type'    => 'textareacount',
                        'maxlen'  => 255,
                        'tab'     => 'Social',
                        'span'    => 'full'
                    ],
                    'settings[tw_description]' => [
                        'label'   => 'inerba.socialseo::lang.editor.social.tw_description',
                        'type'    => 'textareacount',
                        'maxlen'  => 140,
                        'tab'     => 'Social',
                        'span'    => 'full'
                    ],
                    'settings[social_image]' => [
                        'label'   => 'inerba.socialseo::lang.editor.social.social_image',
                        'type'    => 'mediafinder',
                        'mode'    => 'image',
                        'tab'     => 'Social',
                    ],
                ],
                'primary'
            );
        });
    }

    private function getRobotOptions()
    {
        return [
            'index,follow' => 'Indicizza e segui i link diretti (index,follow)',
            'noindex,follow' => 'Non indicizzare ma segui i link diretti (noindex,follow)',
            'index,nofollow' => 'Indicizza ma non seguire i link diretti (index,nofollow)',
            'noindex,nofollow' => 'Non indicizzare e non seguire i link diretti (noindex,nofollow)'
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'inerba.socialseo::lang.settings.label',
                'description' => 'inerba.socialseo::lang.settings.description',
                'icon'        => 'icon-search',
                'category'    =>  SettingsManager::CATEGORY_MYSETTINGS,
                'permissions' => ['inerba.socialseo.settings.edit'],
                'class'       => 'Inerba\SocialSeo\Models\Settings',
                'order'       => 100
            ]
        ];
    }

    public function registerComponents()
    {
        return [
            'Inerba\SocialSeo\Components\BlogSeo' => 'BlogSeo',
            'Inerba\SocialSeo\Components\PageSeo' => 'PageSeo',
            'Inerba\SocialSeo\Components\CmsSeo' => 'CmsSeo',
            'Inerba\SocialSeo\Components\CustomSeo' => 'customSeo',
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'Inerba\SocialSeo\FormWidgets\TextCount' => 'textcount',
            'Inerba\SocialSeo\FormWidgets\TextareaCount' => 'textareacount'
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'seo_title'         => [Seo::class, 'title'],
                'seo_description'   => [Seo::class, 'description'],
                'seo_canonical'     => [Seo::class, 'canonical'],
                'seo_meta'          => [Seo::class, 'meta'],
                'seo_og'            => [Seo::class, 'og_meta'],
                'seo_twitter_card'  => [Seo::class, 'twitter_card'],
                'seo_referrer'      => [Seo::class, 'referrerMeta'],
                'seo_geotag'        => [Seo::class, 'geoTag'],
                'seo_other'         => [Seo::class, 'otherMeta'],
                'seo_short'         => [Seo::class, 'short'],
            ]
        ];
    }

}
