<?php namespace Crazy\Declinations\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Backend\Widgets\Form;
use Backend\Widgets\Lists;
use System\Classes\PluginManager;
use System\Classes\SettingsManager;

/**
 * Subjects Backend Controller
 */
class Subjects extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\RelationController::class,
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var string relationConfig file
     */
    public $relationConfig = 'config_relation.yaml';

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Crazy.Declinations', 'crazy.declinations.subjects');
    }

    public function formExtendFields(Form $form)
    {
        if (!PluginManager::instance()->exists("RainLab.Translate")) {
            $form->addFields([
                'is_translate' => [
                    "label" => 'crazy.declinations::form.is_translate',
                    "hidden" => true,
                ], 
            ]);
        }
    }

    public function listExtendColumns(Lists $list)
    {
        if (!PluginManager::instance()->exists("RainLab.Translate")) {
            $list->removeColumn("is_translate");
        }
    }
}
