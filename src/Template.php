<?php

namespace Outl1ne\PageManager;

use Illuminate\Http\Request;

class Template
{
    const TYPE_PAGE = 'page';
    const TYPE_REGION = 'region';

    protected $type = null; // 'page' or 'region'
    protected $templateSlug = null;
    protected $resource = null;

    public function __construct($resource = null)
    {
        $this->resource = $resource;
        $this->type = NPM::getTemplateClassType(static::class);
        $this->templateSlug = null;
    }

    public function name(Request $request)
    {
        return class_basename(static::class);
    }

    public function fields(Request $request)
    {
        return [];
    }

    public function seoFields(Request $request)
    {
        return true;
    }
}
