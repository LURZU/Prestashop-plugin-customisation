<?php

class Pages extends ObjectModel
{

    public $id_configurator_page;
    public $name;
    public $link_rewrite;
    public $active = 1;
    public $detail;

    public static $definition = [
        'table' => 'configurator_page',
        'primary' => 'id_configurator_page',
        'multilang' => true,
        'fields' => [
            // Champs Standards
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true],
            'link_rewrite' => ['type' => self::TYPE_STRING, 'validate' => 'isLinkRewrite', 'size' => 255, 'required' => false],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'detail' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'required' => true],
        ],
    ];
}
