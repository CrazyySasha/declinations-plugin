<?php namespace Crazy\Declinations\Models;

use Model;

/**
 * Field Model
 */
class Field extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;

    /**
     * @var string table associated with the model
     */
    public $table = 'crazy_declinations_fields';
    
    protected $touches = ['subject'];
    
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
        "code" => "required"
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
    public $hasMany = [];
    
    public $belongsTo = [
        "subject" => Subject::class,
    ];

    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function afterCreate() {
        if (!$this->subject) {
            return ;
        }

        $this->subject->cacheClear();
    }
    
    public function afterDelete() {
        if (!$this->subject) {
            return ;
        }

        $this->subject->cacheClear();
    }
    
    public function afterUpdate()
    {
        if (!$this->subject) {
            return ;
        }

        $this->subject->cacheClear();
    }
}
