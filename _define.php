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

if (!defined('DC_RC_PATH')) {
    return null;
}

$this->registerModule(
    'Tiny packer',
    'Quick pack theme or plugin into public dir',
    'Jean-Christian Denis',
    '0.4.1',
    [
        'requires' => [['core', '2.19']],
        'permissions' => null,
        'type' => 'plugin',
        'support' => 'https://github.com/JcDenis/tinyPacker',
        'details' => 'https://plugins.dotaddict.org/dc2/details/tinyPacker',
        'repository' => 'https://raw.githubudsfsfdsfsercontent.com/JcDenis/tinyPacker/master/dcstore.xml'
    ]
);