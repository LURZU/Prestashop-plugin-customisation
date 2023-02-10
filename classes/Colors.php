<?php

class Colors extends ObjectModel
{
    public $id_configurator_color;
    public $name;
    public $code;
    
    public static $definition = [
        'table' => 'configurator_color',
        'primary' => 'id_configurator_color',
        'multilang' => true,
        'fields' => [
            // Champs Standards
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true],
            'code' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true],
        ],
    ];
}