<?php namespace Crazy\Declinations\Models;

use Cache;
use Model;
use System\Classes\PluginManager;

/**
 * Subject Model
 */
class Subject extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table associated with the model
     */
    public $table = 'crazy_declinations_subjects';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['*'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = [];

    /**
     * @var array rules for validation
     */
    public $rules = [
        "name" => "required",
        "code" => "required",
        "controller" => "required",
        "model" => "required"
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = [];

    /**
     * @var array appends attributes to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array hidden attributes removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array hasOne and other relations
     */
    public $hasOne = [];
    public $hasMany = [
        "fields" => Field::class
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function afterUpdate()
    {
        $this->cacheClear();
    }

    public function afterCreate()
    {
        $this->cacheClear();
    }

    public function afterDelete()
    {
        $this->cacheClear();
    }
    
    public function getControllerOptions()
    {
        foreach(PluginManager::instance()->getPlugins() as $code => $plugin) {
            $controllersPath = PluginManager::instance()->getPluginPath($code) . "/controllers";
            if (!PluginManager::instance()->exists($code) || !is_dir($controllersPath)) {
                continue;
            }

            $files = scandir($controllersPath);
            
            foreach ($files as $file) {
                if (is_dir($controllersPath . "/" . $file)) {
                    continue;
                }
                $controllerFile = explode(".", $file);
                $controllerName = is_array($controllerFile) ? $controllerFile[0] : "";
                if (!class_exists($class  = str_replace(".", "\\", $code) . "\\controllers\\" . $controllerName)) {
                    continue;
                }
                $options[$class] = "$code -> $controllerName";
            }
        }
        return $options;
    }
    public function getModelOptions()
    {
        foreach(PluginManager::instance()->getPlugins() as $code => $plugin) {
            $modelsPath = PluginManager::instance()->getPluginPath($code) . "/models";
            if (!PluginManager::instance()->exists($code) || !is_dir($modelsPath)) {
                continue;
            }

            $files = scandir($modelsPath);
            
            foreach ($files as $file) {
                if (is_dir($modelsPath . "/" . $file)) {
                    continue;
                }
                $modelFile = explode(".", $file);
                $modelName = is_array($modelFile) ? $modelFile[0] : "";
                if (!class_exists($class  = str_replace(".", "\\", $code) . "\\models\\" . $modelName)) {
                    continue;
                }
                $options[$class] = "$code -> $modelName";
            }
        }
        return $options;
    }

    public static function listCached()
    {
        return Cache::remember(
            "crazy.declinables.subjects", 
            now()->addMinutes(24*60),
            function () {
                return self::isActive()->with("fields")->get()->toArray();
            },
        );
    }

    public function cacheClear()
    {
        Cache::forget("crazy.declinables.subjects");
    }

    public function scopeIsActive($query, $isActive = true) 
    {
        return $query->whereIsActive($isActive);
    }
}
