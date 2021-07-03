<?php namespace Crazy\Declinations;

use App;
use ApplicationException;
use Backend;
use Backend\Widgets\Form;
use October\Rain\Database\Model;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;

/**
 * Declinations Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Declinations',
            'description' => 'No description provided yet...',
            'author'      => 'Crazy',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        // dump(Models\Declinable::get());
        foreach (Models\Subject::listCached() as $subject) {
            if (!array_get($subject, 'is_active', false)) {
                continue;
            }
            $subjectCode = array_get($subject, 'code', null);
            ($model = $subject['model'])::extend(function (Model $model) use($subject, $subjectCode) {
                
                $model->morphMany['declinations'] = [
                    Models\Declinable::class,
                    'name' => "declinable",
                ];

                foreach (array_get($subject, 'fields', []) as $field) {
                    
                    $model->addDynamicProperty($subjectCode . 'Array', null);

                    $model->addDynamicMethod('get' . \Str::studly($subjectCode). 'Attribute', function () use ($model, $subject, $subjectCode) {
                        if ($model->{$subjectCode . "Array"}) {
                            return $model->{$subjectCode . "Array"};
                        }
                        foreach ($model->declinations as $dec) {
                            $declinations[$dec->field] = $dec->value;
                        }
                        
                        if (array_get($subject, 'is_translate', false)) {
                            $locales = \RainLab\Translate\Models\Locale::listEnabled();
                            $defaultLocale = \RainLab\Translate\Models\Locale::getDefault();
                            foreach ($locales as $code => $locale) {
                                if ($defaultLocale->code == $code) {
                                    continue;
                                }
                                foreach ($model->declinations as $dec) {
                                    // $declinations['lang'][$code][$dec->field] = $dec->translations->where();
                                    
                                    $dec->translateContext($code);
                                    $declinations['lang'][$code][$dec->field] = $dec->value;
                                }
                            }
                        }
                        return $model->{$subjectCode . "Array"} = $declinations;
                    });

                    $model->addDynamicMethod('set' . \Str::studly($subjectCode) . 'Attribute', function ($val) use ($model, $subjectCode) {
                        
                        foreach ($val as $key => $value) {
                            if ($key !== 'lang' || !is_array($value)) {
                                
                                $declination = $model->declinations()->updateOrInsert([
                                    "field" => $key, 
                                    "declinable_id" => $model->id,
                                    "declinable_type" => get_class($model), 
                                ], ['value' => $value]);
                                continue;
                            }
                            foreach ($value as $locale => $decs) {
                                
                                foreach ($decs as $field => $fieldValue) {
                                    $declination = $model->declinations()->firstOrCreate([
                                        "field" => $field, 
                                        "declinable_id" => $model->id,
                                        "declinable_type" => get_class($model), 
                                    ]);
                                    $declination->translateContext($locale);
                                    $declination->value = $fieldValue;    
                                    $declination->save();
                                }
                            }
                        }
                    });
                }
            });

            ($controller = $subject["controller"])::extendFormFields(
                function (Form $form, Model $model, $context = null) use ($subject, $subjectCode) {
                    
                    if ($form->isNested) {
                        return;
                    }
                    if (!array_get($subject, 'is_translate', false)) {
                        foreach (array_get($subject, 'fields', [])  as $index => $field) {
                            # code...
                            $form->addTabFields([
                                $subjectCode . "[" . array_get($field, 'code') . "]" => [
                                    "label" => array_get($field, 'name'),
                                    "span" => $index % 2 == 0 ? "left" : 'right',
                                    "tab" => "crazy.declinations::form.tab_for_declinated",
                                ]
                            ]);
                        }    
                        return;
                    }
                    $locales = \RainLab\Translate\Models\Locale::listEnabled();
                    $defaultLocale = \RainLab\Translate\Models\Locale::getDefault();
                    foreach ($locales as $code => $locale) {
                        $formFields = []; 
                        foreach (array_get($subject, 'fields', [])  as $index => $field) {
                            $formFields[ array_get($field, 'code')] = [
                                "label" => array_get($field, 'name'),
                                "span" => "auto",
    
                            ];
                            // $form->addTabFields([
                            //     $subjectCode . "[lang][" . $key . "]" . "[" . array_get($field, 'code') . "]" => [
                            //         "label" => array_get($field, 'name') .  " - ($key)",
                            //         "span" => "auto",

                            //         "tab" => "crazy.declinations::form.tab_for_declinated",
                            //     ]
                            // ]);
                        }
                        // dump($defaultLocale->code == $code);
                        $form->addTabFields([
                            // $subjectCode . "[" . array_get($field, 'code') . "]" => [
                            $subjectCode . ($defaultLocale->code == $code ? '':"[lang][$code]")  => [
                                // "label" => array_get($field, 'name'),
                                "label" => $locale,
                                "span" => "full",
                                // "showPanel" => false,
                                "type" => "nestedform",
                                "tab" => "crazy.declinations::form.tab_for_declinated",
                                "form" => [
                                    "fields" => $formFields,
                                ]
                            ]
                        ]);
                    }
                }
            );
        }
    }

    
    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {

        return [
            'crazy.declinations.settings' => [
                'tab' => 'Declinations',
                'label' => 'crazy.declinations::permissions.settings',
                'roles' => [
                    "developer",
                ],
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            "crazy.declinations.subjects" => [
                "label" => \Lang::get("crazy.declinations::settings.label"),
                "description" => \Lang::get("crazy.declinations::settings.description"),
                "category" => SettingsManager::CATEGORY_SYSTEM,
                "order" => 999,
                "url" => Backend::url('crazy/declinations/subjects'),
                "icon" => "icon-text-width",
                "permissions" => ["crazy.declinations.settings"]
                
            ],
        ];
    }
}
