<?php namespace drkwolf\Larauser\Traits;

use Illuminate\Support\Arr;


trait HasOptions {
    protected $optionsAttributes = [ ];

    public function initializeHasOptions () {
        $this->attributes['options'] = json_encode($this->optionsAttributes);
        $this->casts['options'] = 'array';
        $this->fillable[]  = 'options';
    }

    public function getOptionsObjAttribute() {
        return json_decode($this->attributes['options']);
    }

    // add update function
    public function fillWithOptions(array $data, $optionsHandler) {
        $newOptions = Arr::get($data, 'options');
        $data = Arr::except($data, 'options');
        $this->fill($data);
        $this->options = $optionsHandler->UpdateOptions($newOptions, $this->options);
    }
}