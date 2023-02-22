<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Team64j\LaravelEvolution\Models\SiteTemplate;
use DateTimeImmutable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class ConfigurationLayout
{
    /**
     * @return array
     */
    public function default(): array
    {
        $actions = [
            'component' => 'ActionsButtons',
            'attrs' => [
                'data' => ['cancel', 'save'],
            ],
        ];

        $title = [
            'component' => 'Title',
        ];

        $tabs = [
            'component' => 'Tabs',
            'attrs' => [
                'id' => 'configuration',
                'data' => [
                    [
                        'id' => 'tab1',
                        'name' => Lang::get('global.settings_site'),
                        'class' => 'flex flex-wrap py-4 mb-4',
                        'active' => true,
                    ],
                    [
                        'id' => 'tab2',
                        'name' => Lang::get('global.settings_furls'),
                        'class' => 'flex flex-wrap py-4 mb-4',
                    ],
                    [
                        'id' => 'tab3',
                        'name' => Lang::get('global.settings_ui'),
                        'class' => 'flex flex-wrap py-4 mb-4',
                    ],
                    [
                        'id' => 'tab4',
                        'name' => Lang::get('global.settings_security'),
                        'class' => 'flex flex-wrap py-4 mb-4',
                    ],
                    [
                        'id' => 'tab5',
                        'name' => Lang::get('global.settings_misc'),
                        'class' => 'flex flex-wrap py-4 mb-4',
                    ],
                    [
                        'id' => 'tab6',
                        'name' => Lang::get('global.settings_KC'),
                        'class' => 'flex flex-wrap py-4 mb-4',
                    ],
                    [
                        'id' => 'tab7',
                        'name' => Lang::get('global.settings_email_templates'),
                        'class' => 'flex flex-wrap py-4 mb-4',
                    ],
                ],
            ],
            'slots' => [
                'tab1' => $this->tab1(),
                'tab2' => $this->tab2(),
                'tab3' => $this->tab3(),
                'tab4' => $this->tab4(),
                'tab5' => $this->tab5(),
                'tab6' => $this->tab6(),
                'tab7' => $this->tab7(),
            ],
        ];

        return [
            $actions,
            $title,
            $tabs,
        ];
    }

    /**
     * @return array
     */
    protected function columns(): array
    {
        return [
            [
                'name' => 'name',
                'label' => Lang::get('global.description'),
                'width' => '25rem',
                'style' => [
                    'minWidth' => '15rem',
                ],
            ],
            [
                'name' => 'key',
                'label' => Lang::get('global.name'),
                'style' => [
                    'fontWeight' => 500,
                ],
            ],
            [
                'name' => 'value',
                'label' => Lang::get('global.value'),
                'width' => '35rem',
                'style' => [
                    'minWidth' => '15rem',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function tab1(): array
    {
        /** @var SiteTemplate $template */
        $template = SiteTemplate::query()->findOrNew(Config::get('global.default_template'));
        $defaultTemplate = [
            'key' => $template->id ?: 0,
            'value' => $template->templatename ?: 'blank',
            'selected' => true,
        ];

        $serverTimes = [];
        $serverTimesRange = range(-24, 24);

        for ($i = 0; $i < count($serverTimesRange); $i++) {
            $serverTimes[] = [
                'key' => $serverTimesRange[$i],
                'value' => $serverTimesRange[$i],
            ];
        }

        $auto_template_logic = [
            'system' => Lang::get('global.defaulttemplate_logic_system_message'),
            'parent' => Lang::get('global.defaulttemplate_logic_parent_message'),
            'sibling' => Lang::get('global.defaulttemplate_logic_sibling_message'),
        ];

        $auto_template_logic_values = [];

        foreach ($auto_template_logic as $key => $value) {
            $auto_template_logic_values[] = [
                'key' => $key,
                'value' => explode(':', strip_tags($value))[0],
            ];
        }

        return [
            [
                'component' => 'Panel',
                'attrs' => [
                    'id' => 'tab1',
                    'class' => 'grow',
                    'filter' => false,
                    'columns' => $this->columns(),
                    'data' => [
                        'data' => [
                            [
                                'name' => Lang::get('global.sitename_title'),
                                'name.help' => Lang::get('global.sitename_message'),
                                'key' => 'site_name',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'site_name',
                                ],
                            ],
                            [
                                'name' => Lang::get('global.sitestatus_title'),
                                'key' => 'site_status',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'site_status',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.online'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.offline'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.sitestart_title'),
                                'name.help' => Lang::get('global.sitestart_message'),
                                'key' => 'site_start',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'site_start',
                                    'attrs' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.errorpage_title'),
                                'name.help' => Lang::get('global.errorpage_message'),
                                'key' => 'error_page',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'error_page',
                                    'attrs' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.unauthorizedpage_title'),
                                'name.help' => Lang::get('global.unauthorizedpage_message'),
                                'key' => 'unauthorized_page',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'unauthorized_page',
                                    'attrs' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.siteunavailable_page_title'),
                                'name.help' => Lang::get('global.siteunavailable_page_message'),
                                'key' => 'site_unavailable_page',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'site_unavailable_page',
                                    'attrs' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.controller_namespace'),
                                'name.help' => Lang::get('global.controller_namespace_message'),
                                'key' => 'ControllerNamespace',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'ControllerNamespace',
                                ],
                            ],
                            [
                                'name' => Lang::get('global.update_repository'),
                                'name.help' => Lang::get('global.update_repository_message'),
                                'key' => 'UpgradeRepository',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'UpgradeRepository',
                                ],
                            ],
                            [
                                'name' => Lang::get('global.siteunavailable_title'),
                                'name.help' => Lang::get('global.siteunavailable_message'),
                                'key' => 'site_unavailable_message',
                                'value' => [
                                    'component' => 'Fields/Textarea',
                                    'model' => 'site_unavailable_message',
                                ],
                            ],
                            [
                                'name' => Lang::get('global.defaulttemplate_title'),
                                'name.help' => Lang::get('global.defaulttemplate_message'),
                                'key' => 'default_template',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'default_template',
                                    'attrs' => [
                                        'data' => [$defaultTemplate],
                                        'url' => 'api/template/select',
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.defaulttemplate_logic_title'),
                                'name.help' => implode('<br>', $auto_template_logic),
                                'key' => 'auto_template_logic',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'auto_template_logic',
                                    'attrs' => [
                                        'data' => $auto_template_logic_values,
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.chunk_processor'),
                                'key' => 'chunk_processor',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'chunk_processor',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => '',
                                                'value' => 'DocumentParser',
                                            ],
                                            [
                                                'key' => 'DLTemplate',
                                                'value' => 'DLTemplate',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.defaultpublish_title'),
                                'name.help' => Lang::get('global.defaultpublish_message'),
                                'key' => 'publish_default',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'publish_default',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.defaultcache_title'),
                                'name.help' => Lang::get('global.defaultcache_message'),
                                'key' => 'cache_default',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'cache_default',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.defaultsearch_title'),
                                'name.help' => Lang::get('global.defaultsearch_message'),
                                'key' => 'search_default',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'search_default',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.defaultmenuindex_title'),
                                'name.help' => Lang::get('global.defaultmenuindex_message'),
                                'key' => 'auto_menuindex',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'auto_menuindex',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.track_visitors_title'),
                                'name.help' => Lang::get('global.track_visitors_message'),
                                'key' => 'track_visitors',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'track_visitors',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.docid_incrmnt_method_title'),
                                'key' => 'docid_incrmnt_method',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'docid_incrmnt_method',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.docid_incrmnt_method_0'),
                                            ],
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.docid_incrmnt_method_1'),
                                            ],
                                            [
                                                'key' => 2,
                                                'value' => Lang::get('global.docid_incrmnt_method_2'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.enable_cache_title'),
                                'key' => 'enable_cache',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'enable_cache',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.enabled'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.disabled'),
                                            ],
                                            [
                                                'key' => 2,
                                                'value' => Lang::get('global.disabled_at_login'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.disable_chunk_cache_title'),
                                'key' => 'disable_chunk_cache',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'disable_chunk_cache',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.disable_snippet_cache_title'),
                                'key' => 'disable_snippet_cache',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'disable_snippet_cache',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.disable_plugins_cache_title'),
                                'key' => 'disable_plugins_cache',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'disable_plugins_cache',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.cache_type_title'),
                                'key' => 'cache_type',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'cache_type',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.cache_type_1'),
                                            ],
                                            [
                                                'key' => 2,
                                                'value' => Lang::get('global.cache_type_2'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.minifyphp_incache_title'),
                                'name.help' => Lang::get('global.minifyphp_incache_message'),
                                'key' => 'minifyphp_incache',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'minifyphp_incache',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.enabled'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.disabled'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.serveroffset_title'),
                                'name.help' => sprintf(
                                    Lang::get('global.serveroffset_message'),
                                    DateTimeImmutable::createFromFormat('U', (string) time())->format('H:i:s'),
                                    DateTimeImmutable::createFromFormat(
                                        'U',
                                        (string) (time() + Config::get('global.server_offset_time'))
                                    )->format('H:i:s')
                                ),
                                'key' => 'server_offset_time',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'server_offset_time',
                                    'attrs' => [
                                        'data' => $serverTimes,
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.server_protocol_title'),
                                'name.help' => Lang::get('global.server_protocol_message'),
                                'key' => 'server_protocol',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'server_protocol',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 'http',
                                                'value' => Lang::get('global.server_protocol_http'),
                                            ],
                                            [
                                                'key' => 'https',
                                                'value' => Lang::get('global.server_protocol_https'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.rss_url_news_title'),
                                'key' => 'rss_url_news',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'rss_url_news',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function tab2(): array
    {
        return [
            [
                'component' => 'Panel',
                'attrs' => [
                    'id' => 'tab2',
                    'class' => 'grow',
                    'filter' => false,
                    'columns' => $this->columns(),
                    'data' => [
                        'data' => [
                            [
                                'name' => Lang::get('global.friendlyurls_title'),
                                'name.help' => Lang::get('global.friendlyurls_message'),
                                'key' => 'friendly_urls',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'friendly_urls',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.xhtml_urls_title'),
                                'name.help' => Lang::get('global.xhtml_urls_message'),
                                'key' => 'xhtml_urls',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'xhtml_urls',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.friendlyurlsprefix_title'),
                                'name.help' => Lang::get('global.friendlyurlsprefix_message'),
                                'key' => 'friendly_url_prefix',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'friendly_url_prefix',
                                ],
                            ],
                            [
                                'name' => Lang::get('global.friendlyurlsuffix_title'),
                                'name.help' => Lang::get('global.friendlyurlsuffix_message'),
                                'key' => 'friendly_url_suffix',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'friendly_url_suffix',
                                ],
                            ],
                            [
                                'name' => Lang::get('global.make_folders_title'),
                                'name.help' => Lang::get('global.make_folders_message'),
                                'key' => 'make_folders',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'make_folders',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.seostrict_title'),
                                'name.help' => Lang::get('global.seostrict_message'),
                                'key' => 'seostrict',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'seostrict',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.friendly_alias_title'),
                                'name.help' => Lang::get('global.friendlyurls_message'),
                                'key' => 'friendly_alias_urls',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'friendly_alias_urls',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.use_alias_path_title'),
                                'name.help' => Lang::get('global.use_alias_path_message'),
                                'key' => 'use_alias_path',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'use_alias_path',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.alias_listing_title'),
                                'name.help' => Lang::get('global.alias_listing_message'),
                                'key' => 'alias_listing',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'alias_listing',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.alias_listing_enabled'),
                                            ],
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.alias_listing_folders'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.alias_listing_disabled'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.duplicate_alias_title'),
                                'name.help' => Lang::get('global.duplicate_alias_message'),
                                'key' => 'allow_duplicate_alias',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'allow_duplicate_alias',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.automatic_alias_title'),
                                'name.help' => Lang::get('global.automatic_alias_message'),
                                'key' => 'automatic_alias',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'automatic_alias',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function tab3(): array
    {
        $manager_language = array_map(fn($dir) => [
            'key' => basename($dir),
            'value' => Str::upper(basename($dir)),
        ], File::directories(App::langPath()));

        $modx_charset = [
            [
                'key' => 'UTF-8',
                'value' => 'Unicode (UTF-8) - utf-8',
            ],
        ];

        $manager_theme = [
            [
                'key' => 'default',
                'value' => 'Default',
            ],
        ];

        $settings_group_tv_options = explode(',', Lang::get('global.settings_group_tv_options'));

        return [
            [
                'component' => 'Panel',
                'attrs' => [
                    'id' => 'tab3',
                    'class' => 'grow',
                    'filter' => false,
                    'columns' => $this->columns(),
                    'data' => [
                        'data' => [
                            [
                                'name' => Lang::get('global.language_title'),
                                'name.help' => Lang::get('global.language_message'),
                                'key' => 'manager_language',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'manager_language',
                                    'attrs' => [
                                        'data' => $manager_language,
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.charset_title'),
                                'name.help' => Lang::get('global.charset_message'),
                                'key' => 'modx_charset',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'modx_charset',
                                    'attrs' => [
                                        'data' => $modx_charset,
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.manager_theme'),
                                'name.help' => Lang::get('global.manager_theme'),
                                'key' => 'manager_theme',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'manager_theme',
                                    'attrs' => [
                                        'data' => $manager_theme,
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.manager_theme_mode'),
                                'name.help' => Lang::get('global.manager_theme_mode_message'),
                                'key' => 'manager_theme_mode',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'manager_theme_mode',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.manager_theme_mode1'),
                                            ],
                                            [
                                                'key' => 2,
                                                'value' => Lang::get('global.manager_theme_mode2'),
                                            ],
                                            [
                                                'key' => 3,
                                                'value' => Lang::get('global.manager_theme_mode3'),
                                            ],
                                            [
                                                'key' => 4,
                                                'value' => Lang::get('global.manager_theme_mode4'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.login_logo_title'),
                                'name.help' => Lang::get('global.login_logo_message'),
                                'key' => 'login_logo',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'login_logo',
                                ],
                            ],
                            [
                                'name' => Lang::get('global.login_bg_title'),
                                'name.help' => Lang::get('global.login_bg_message'),
                                'key' => 'login_bg',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'login_bg',
                                ],
                            ],
                            [
                                'name' => Lang::get('global.login_form_position_title'),
                                'key' => 'login_form_position',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'login_form_position',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 'left',
                                                'value' => Lang::get('global.login_form_position_left'),
                                            ],
                                            [
                                                'key' => 'center',
                                                'value' => Lang::get('global.login_form_position_center'),
                                            ],
                                            [
                                                'key' => 'right',
                                                'value' => Lang::get('global.login_form_position_right'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.login_form_style'),
                                'key' => 'login_form_style',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'login_form_style',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 'dark',
                                                'value' => Lang::get('global.login_form_style_dark'),
                                            ],
                                            [
                                                'key' => 'light',
                                                'value' => Lang::get('global.login_form_style_light'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.manager_menu_position_title'),
                                'key' => 'manager_menu_position',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'manager_menu_position',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 'top',
                                                'value' => Lang::get('global.manager_menu_position_top'),
                                            ],
                                            [
                                                'key' => 'left',
                                                'value' => Lang::get('global.manager_menu_position_left'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.show_picker'),
                                'name.help' => Lang::get('global.settings_show_picker_message'),
                                'key' => 'show_picker',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'show_picker',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.warning_visibility'),
                                'name.help' => Lang::get('global.warning_visibility_message'),
                                'key' => 'warning_visibility',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'warning_visibility',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.everybody'),
                                            ],
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.administrators'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.tree_page_click'),
                                'name.help' => Lang::get('global.tree_page_click_message'),
                                'key' => 'tree_page_click',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'tree_page_click',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 27,
                                                'value' => Lang::get('global.edit'),
                                            ],
                                            [
                                                'key' => 3,
                                                'value' => Lang::get('global.resource_overview'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.use_breadcrumbs'),
                                'name.help' => Lang::get('global.use_breadcrumbs_message'),
                                'key' => 'use_breadcrumbs',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'use_breadcrumbs',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.remember_last_tab'),
                                'name.help' => Lang::get('global.remember_last_tab_message'),
                                'key' => 'remember_last_tab',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'remember_last_tab',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.use_global_tabs'),
                                'key' => 'global_tabs',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'global_tabs',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.group_tvs'),
                                'name.help' => Lang::get('global.settings_group_tv_message'),
                                'key' => 'group_tvs',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'group_tvs',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 0,
                                                'value' => $settings_group_tv_options[0],
                                            ],
                                            [
                                                'key' => 1,
                                                'value' => $settings_group_tv_options[1],
                                            ],
                                            [
                                                'key' => 2,
                                                'value' => $settings_group_tv_options[2],
                                            ],
                                            [
                                                'key' => 3,
                                                'value' => $settings_group_tv_options[3],
                                            ],
                                            [
                                                'key' => 4,
                                                'value' => $settings_group_tv_options[4],
                                            ],
                                            [
                                                'key' => 5,
                                                'value' => $settings_group_tv_options[5],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.show_newresource_btn'),
                                'name.help' => Lang::get('global.show_newresource_btn_message'),
                                'key' => 'show_newresource_btn',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'show_newresource_btn',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.show_fullscreen_btn'),
                                'name.help' => Lang::get('global.show_fullscreen_btn_message'),
                                'key' => 'show_fullscreen_btn',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'show_fullscreen_btn',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.setting_resource_tree_node_name'),
                                'name.help' => Lang::get('global.setting_resource_tree_node_name_desc'),
                                'key' => 'resource_tree_node_name',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'resource_tree_node_name',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 'pagetitle',
                                                'value' => '[*pagetitle*]',
                                            ],
                                            [
                                                'key' => 'longtitle',
                                                'value' => '[*longtitle*]',
                                            ],
                                            [
                                                'key' => 'menutitle',
                                                'value' => '[*menutitle*]',
                                            ],
                                            [
                                                'key' => 'alias',
                                                'value' => '[*alias*]',
                                            ],
                                            [
                                                'key' => 'createdon',
                                                'value' => '[*createdon*]',
                                            ],
                                            [
                                                'key' => 'editedon',
                                                'value' => '[*editedon*]',
                                            ],
                                            [
                                                'key' => 'publishedon',
                                                'value' => '[*publishedon*]',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.session_timeout'),
                                'name.help' => Lang::get('global.session_timeout_msg'),
                                'key' => 'session_timeout',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'session_timeout',
                                    'attrs' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.tree_show_protected'),
                                'name.help' => Lang::get('global.tree_show_protected_message'),
                                'key' => 'tree_show_protected',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'tree_show_protected',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 1,
                                                'value' => Lang::get('global.yes'),
                                            ],
                                            [
                                                'key' => 0,
                                                'value' => Lang::get('global.no'),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.datepicker_offset'),
                                'name.help' => Lang::get('global.datepicker_offset_message'),
                                'key' => 'datepicker_offset',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'datepicker_offset',
                                    'attrs' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.datetime_format'),
                                'name.help' => Lang::get('global.datetime_format_message'),
                                'key' => 'datetime_format',
                                'value' => [
                                    'component' => 'Fields/Select',
                                    'model' => 'datetime_format',
                                    'attrs' => [
                                        'data' => [
                                            [
                                                'key' => 'dd-mm-YYYY',
                                                'value' => 'dd-mm-YYYY',
                                            ],
                                            [
                                                'key' => 'mm/dd/YYYY',
                                                'value' => 'mm/dd/YYYY',
                                            ],
                                            [
                                                'key' => 'YYYY/mm/dd',
                                                'value' => 'YYYY/mm/dd',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.nologentries_title'),
                                'name.help' => Lang::get('global.nologentries_message'),
                                'key' => 'number_of_logs',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'number_of_logs',
                                    'attrs' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                            [
                                'name' => Lang::get('global.noresults_title'),
                                'name.help' => Lang::get('global.noresults_message'),
                                'key' => 'number_of_results',
                                'value' => [
                                    'component' => 'Fields/Input',
                                    'model' => 'number_of_results',
                                    'attrs' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function tab4(): array
    {
        return [
            [
                'component' => 'Panel',
                'attrs' => [
                    'id' => 'tab4',
                    'class' => 'grow',
                    'filter' => false,
                    'columns' => $this->columns(),
                    'data' => [
                        'data' => [
                            [
                                'data' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function tab5(): array
    {
        return [
            [
                'component' => 'Panel',
                'attrs' => [
                    'id' => 'tab5',
                    'class' => 'grow',
                    'filter' => false,
                    'columns' => $this->columns(),
                    'data' => [
                        'data' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function tab6(): array
    {
        return [
            [
                'component' => 'Panel',
                'attrs' => [
                    'id' => 'tab6',
                    'class' => 'grow',
                    'filter' => false,
                    'columns' => $this->columns(),
                    'data' => [
                        'data' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function tab7(): array
    {
        return [
            [
                'component' => 'Panel',
                'attrs' => [
                    'id' => 'tab7',
                    'class' => 'grow',
                    'filter' => false,
                    'columns' => $this->columns(),
                    'data' => [
                        'data' => [],
                    ],
                ],
            ],
        ];
    }
}
