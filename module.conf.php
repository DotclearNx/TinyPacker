<?php
/**
 * @brief tinyPacker, a plugin for Dotclear 2
 * 
 * @package Dotclear
 * @subpackage Plugin
 * 
 * @author Jean-Christian Denis
 * 
 * @copyright Jean-Christian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

use Dotclear\App;

if (!class_exists('Dotclear\App')) {
    exit(1);
}

return [
    'name'=>__('Tiny packer'),
    'description'=>__('Quick pack theme or plugin into public dir'),
    'version'=>'0.5',
    'author'=>'Jean-Christian Denis',
    'type'=>'Plugin',
    'support'=>'https://github.com/DotclearNx/TinyPacker',
    'details'=>'https://plugins.dotaddict.org/dc2/details/tinyPacker',
    'repository'=>'https://raw.githubusercontent.com/DotclearNx/TinyPacker/master/dcstore.xml',
    'requires' => [
        'core' => '3.0-dev',
    ],
];
