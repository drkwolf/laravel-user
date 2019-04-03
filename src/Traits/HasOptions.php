<?php namespace drkwolf\Larauser\Traits;

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

}