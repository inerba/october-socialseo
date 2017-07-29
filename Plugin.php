<?php namespace Inerba\SocialSeo;

use Backend;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use System\Classes\PluginBase;
use Inerba\SocialSeo\Classes\Seo;
use System\Classes\PluginManager;
use System\Classes\SettingsManager;

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

    public function boot()
    {
        if( PluginManager::instance()->hasPlugin('RainLab.Blog') )
        {
            
            \RainLab\Blog\Models\Post::extend(function($model){


                $model->jsonable(array_merge($model->getJsonable(), ["socialseo"]));

                $model->attachOne = array_merge( $model->attachOne, ['social_image' => 'System\Models\File'] );

            });

        }

    }

    public function register()
    {
        \Event::listen('backend.form.extendFields', function ($widget) {
            if (PluginManager::instance()->hasPlugin('RainLab.Pages') && $widget->model instanceof \RainLab\Pages\Classes\Page) {
                $widget->addFields([
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
                            'span'    => 'left'
                        ],
                        'viewBag[social_image]' => [
                            'label'   => 'inerba.socialseo::lang.editor.social.social_image',
                            'type'    => 'mediafinder',
                            'mode'    => 'image',
                            'tab'     => 'SEO e Social',
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

            $widget->addFields(
                [
                    'settings[seo_keywords]' => [
                        'label'   => 'inerba.socialseo::lang.editor.meta_keywords',
                        'type'    => 'taglist',
                        'separator'    => 'comma',
                        'tab'     => 'cms::lang.editor.meta',
                    ],
                    'settings[redirect_url]' => [
                        'label'   => 'inerba.socialseo::lang.editor.redirect_url',
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

    public function registerComponents()
    {
        return [
            'Inerba\SocialSeo\Components\BlogSeo' => 'BlogSeo',
            'Inerba\SocialSeo\Components\PageSeo' => 'PageSeo',
            'Inerba\SocialSeo\Components\CmsSeo' => 'CmsSeo',
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
                'seo_title'       => [Seo::class, 'title'],
                'seo_description' => [Seo::class, 'description'],
                'seo_canonical'   => [Seo::class, 'canonical'],
                'seo_meta'        => [Seo::class, 'meta'],
            ]
        ];
    }

}
