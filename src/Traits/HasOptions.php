<?php namespace drkwolf\Larauser\Traits;

use Illuminate\Support\Arr;


trait HasOptions {
    protected $optionsAttributes = [ ];

    public function initializeHasOptions () {
        // $attributes = config('larauser.options.attributes', $this->optionsAttributes);
        // // assign only 
        // if ($this->currentRoleName) {
        //     $filters =  config('larauser.options.filters.' . $this->currentRoleName, []);
        //     $attributes = Arr::only($attributes, $filters);
        // }

        // $this->attributes['options'] = json_encode($attributes);
        $this->casts['options'] = 'array';
        // $this->fillable[]  = 'options';
    }

    public function getOptionsObjAttribute() {
        return json_decode($this->attributes['options']);
    }

    // add update function
    public function fillWithOptions(array $data, $filter) {
        $newOptions = Arr::get($data, 'options', []);
        $data = Arr::except($data, 'options');
        $this->fill($data);
      $this->initUserOptionsAttributes($newOptions, $filter);
    }

    private function initUserOptionsAttributes(array $newOptions, $filter) {
        $attributes = config('larauser.options.attributes', $this->optionsAttributes);
        $sections = config('larauser.options.filters.' . $filter, []);
        $schema = $this->getFilteredOptions($attributes, $sections);

        $options = $this->options;
        $newOptions = $this->filterByArrayKeys($newOptions, $schema);

        // set filtered options
        foreach ($attributes as $key => $value) {
            if (Arr::has($newOptions, $key)) $options[$key] = $newOptions[$key];
        }
        $this->options = $options;
    }

    public function getFilteredOptions($attributes, $sections = [], $default = []) {
        if ($sections) {
            $resp = [];
            foreach($sections as $item) {
               $resp[$item]  = Arr::get($attributes, $item, $default);
            }
            return $resp;
        } 
        return $attributes;
    }

    // FIXME Extends Laravel Collection
    public function filterByArrayKeys($array, $schema) {
        $keys = collect($schema)->flatMap(function ($item, $key) {
            return $this->prefixKey($key. '.', $item);
        })->keys()->toArray();
       return $this->arrayDotOnly($array, $keys);
    }

    private function arrayDotOnly($array, $keys) {
        $newArray = [];
        foreach ((array) $keys as $key) {
            Arr::set($newArray, $key, Arr::get($array, $key)); 
        }
        return $newArray;
    }

    public function prefixKey($prefix, $array) {
        $result = array();
        foreach ($array as $key => $value) {
                if (is_array($value))
                    $result = array_merge($result, $this->prefixKey($prefix . $key . '.', $value));
                else
                    $result[$prefix . $key] = $value;
            }
        return $result;
    }

}