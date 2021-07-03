<?php namespace Crazy\Declinations\Controllers;

use Backend\Behaviors\ReorderController;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Fields Backend Controller
 */
class Fields extends Controller
{
    public $implement = [
        ReorderController::class
    ];

    /**
     * @var string reorderConfig file
     */
    public $reorderConfig = 'config_reorder.yaml';

   
    protected $subjectId;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Crazy.Declinations', 'crazy.declinations.subjects');
    }

    public function reorder($subjectId = null, $context = null)
    {
        $this->vars["subjectId"] = $this->subjectId = $subjectId;
        $this->vars["context"] = $context;
        
        return $this->asExtension(ReorderController::class)->reorder();
    }

    public function reorderExtendQuery($query)
    {
        $query->where("subject_id", $this->subjectId);
    }
}
