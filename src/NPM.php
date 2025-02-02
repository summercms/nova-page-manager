<?php

namespace Outl1ne\PageManager;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class NPM
{
    // Table names
    public static function getPagesTableName(): string
    {
        return config('nova-page-manager.pages_table', 'pages');
    }

    public static function getRegionsTableName(): string
    {
        return config('nova-page-manager.regions_table', 'regions');
    }



    // Models
    public static function getPageModel(): string
    {
        return config('nova-page-manager.page_model', \Outl1ne\PageManager\Models\Page::class);
    }

    public static function getRegionModel(): string
    {
        return config('nova-page-manager.region_model', \Outl1ne\PageManager\Models\Region::class);
    }


    // Resources
    public static function getPageResource(): string
    {
        return config('nova-page-manager.page_resource', \Outl1ne\PageManager\Nova\Resources\Page::class);
    }

    public static function getRegionResource(): string
    {
        return config('nova-page-manager.region_resource', \Outl1ne\PageManager\Nova\Resources\Region::class);
    }



    // Templates
    public static function getTemplates(): array
    {
        $pageTemplates = config('nova-page-manager.templates.pages', []);
        $regionTemplates = config('nova-page-manager.templates.regions', []);

        $filterTemplates = function ($templates) {
            // Move 'key' aka 'slug' into the array itself
            $mappedTemplates = Arr::map($templates, fn ($t, $s) => [...$t, 'slug' => $s]);

            return array_filter($mappedTemplates, function ($template) {
                if (isset($template['class']) && class_exists($template['class'])) return true;

                Log::warning("[Nova Page Manager] Received invalid configuration for template entry: " . json_encode($template));
                return false;
            });
        };

        return [
            'pages' => $filterTemplates($pageTemplates),
            'regions' => $filterTemplates($regionTemplates),
        ];
    }

    public static function getPageTemplates(): array
    {
        return static::getTemplates()['pages'];
    }

    public static function getRegionTemplates(): array
    {
        return static::getTemplates()['regions'];
    }

    public static function getPageTemplateByClass($className)
    {
        return Arr::first(static::getPageTemplates(), fn ($template) => $template['class'] === $className);
    }

    public static function getRegionTemplateByClass($className)
    {
        return Arr::first(static::getRegionTemplates(), fn ($template) => $template['class'] === $className);
    }

    public static function getTemplateClassType($templateClass)
    {
        $isPage = !!static::getPageTemplateByClass($templateClass);
        $isRegion = !!static::getRegionTemplateByClass($templateClass);
        return $isPage ? static::TYPE_PAGE : ($isRegion ? static::TYPE_REGION : null);
    }

    public static function getPageTemplateSlug($className)
    {
        $template = static::getPageTemplateByClass($className);
    }



    // Enabled states
    public static function pagesEnabled(): bool
    {
        return config('nova-page-manager.page_resource') !== false;
    }

    public static function regionsEnabled(): bool
    {
        return config('nova-page-manager.region_resource') !== false;
    }



    // Page URL generation
    public static function getPageUrl($page)
    {
        $getPageUrl = config('nova-page-manager.page_url');
        return isset($getPageUrl) ? call_user_func($getPageUrl, $page) : null;
    }

    public static function getPagePath($page, $path)
    {
        $getPagePath = config('nova-page-manager.page_path');
        return isset($getPagePath) ? call_user_func($getPagePath, $page, $path) : $path;
    }



    // Others
    public static function getLocales(): array
    {
        $localesConfig = config('nova-page-manager.locales', ['en' => 'English']);
        if (is_callable($localesConfig)) return call_user_func($localesConfig);
        if (is_array($localesConfig)) return $localesConfig;
        return ['en' => 'English'];
    }

    public static function getCustomSeoFields(): array
    {
        $seoFields = config('nova-page-manager.seo_fields', null);
        if (is_callable($seoFields)) return call_user_func($seoFields);
        if (is_array($seoFields)) return $seoFields;
        return [];
    }
}
