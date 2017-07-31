<?php namespace Inerba\SocialSeo\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Cms\Classes\Controller;

/**
 * Embedd Form Widget
 */
class TextareaCount extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'inerba_seo_textareacount';

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('textareacount');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['name'] = $this->formField->getName();
        $this->vars['model'] = $this->model;
        $this->vars['form'] = $this;
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addJs('/plugins/inerba/socialseo/assets/js/calculate_text_length.js');
    }

}
