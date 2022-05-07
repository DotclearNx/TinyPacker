<?php
/**
 * @class Dotclear\Plugin\TinyPacker\Admin\Prepend
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

namespace Dotclear\Plugin\TinyPacker\Admin;

use Dotclear\App;
use Dotclear\Exception\ModuleException;
use Dotclear\Modules\ModuleDefine;
use Dotclear\Modules\ModulePrepend;
use Dotclear\Modules\Plugin\PluginList;
use Dotclear\Helper\File\Files;
use Dotclear\Helper\File\Path;
use Dotclear\Helper\File\Zip\Zip;
use Dotclear\Helper\Network\Http;
use Dotclear\Helper\Html\Html;

class Prepend extends ModulePrepend
{
    public function loadModule(): void
    {
        if (!$this->tinyPackerRepositoryDir()) {
            return;
        }

        App::core()->behavior()->add(
            'adminModulesListGetActions',
            [$this, 'tinyPackerAdminModulesListGetActions']
        );
        App::core()->behavior()->add(
            'adminModulesListDoActions',
            [$this, 'tinyPackerAdminModulesListDoActions']
        );
    }

    /**
     * Blog's public sub-directory where to put packages
     * @var     string
     */
    public $tinyPacker_sub_dir = 'packages';

    /**
     * Add button to create package to modules lists
     * 
     * @param   PluginList     $list       PluginList instance
     * @param   string              $id         Module id
     * @param   ModuleDefine      $module     ModuleDefine instance
     * 
     * @return  string                          HTML submit button
     */
    public function tinyPackerAdminModulesListGetActions(PluginList $list, string $id, ModuleDefine $module): string
    {
        if ($list->getList() != 'plugin-activate' 
         && $list->getList() != 'theme-activate') {
            return '';
        }

        return 
        '<input type="submit" name="tinypacker[' .
        Html::escapeHTML($id) . ']" value="Pack" />';
    }

    /**
     * Create package on modules lists action
     * 
     * @param   PluginList     $list       PluginList instance
     * @param   array               $modules    Selected modules ids
     * @param   string              $type       List type (plugins|themes)
     * 
     * @throws  ModuleException                 If no public dir or module
     */
    public function tinyPackerAdminModulesListDoActions(PluginList $list, array $modules, string $type): void
    {
        # Pack action
        if (empty($_POST['tinypacker']) 
         || !is_array($_POST['tinypacker'])) {
            return;
        }
        $topack = array_keys($_POST['tinypacker']);
        $id = $topack[0];

        # Repository directory
        if (($root = $this->tinyPackerRepositoryDir()) === false) {
            throw new ModuleException(
                __('Destination directory is not writable.'
            ));
        }

        # Module to pack
        if (!$list->modules()->hasModule($id)) {
            throw new ModuleException(__('No such module.'));
        }
        $module = $list->modules()->getModule($id);

        # Excluded files and dirs
        $exclude = [
            '\.',
            '\.\.',
            '__MACOSX',
            '\.svn',
            '\.hg.*?',
            '\.git.*?',
            'CVS',
            '\.directory',
            '\.DS_Store',
            'Thumbs\.db'
        ];

        # Packages names
        $files = [
            $type . '-' . $id . '.zip',
            $type . '-' . $id . '-' . $module->version() . '.zip'
        ];

        # Create zip
        foreach($files as $f) {

            @set_time_limit(300);
            $fp = fopen($root . '/' . $f, 'wb');

            $zip = new Zip($fp);

            foreach($exclude AS $e) {
                $zip->addExclusion(sprintf(
                    '#(^|/)(%s)(/|$)#', 
                    $e
                ));
            }

            $zip->addDirectory($module->root(), $id, true);
            $zip->write();
            $zip->close();
            unset($zip);
        }

        App::core()->notice()->addSuccessNotice(
            __('Task successfully executed.')
        );
        Http::redirect($list->getURL());
    }

    /**
     * Check and create directories used by packer
     * 
     * @return  string|bool     Cleaned path or false on error
     */
    public function tinyPackerRepositoryDir(): string|bool
    {
        $dir = Path::real(
            App::core()->blog()->public_path . '/' . $this->tinyPacker_sub_dir, 
            false
        );

        try {
            if (!is_dir($dir)) {
                Files::makeDir($dir, true);
            }
            if (is_writable($dir)) {

                return $dir;
            }
        }
        catch(\Exception $e) {}

        return false;
    }
}