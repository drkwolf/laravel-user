<?php namespace drkwolf\Larauser;

use Illuminate\Support\Arr;
use drkwolf\Package\ValidatableHandler;

class UserOptions extends ValidatableHandler {
    protected $schemas;
    private $sections;
    
    public function __construct(array $sections = []) {
        $this->sections = $sections;
    }

    public function updateOptions($newOptions, $oldOptions) {
        foreach ($this->getSchema($this->sections, []) as $key => $value) {
            if (Arr::has($newOptions, $key)) {
                Arr::set($oldOptions, $key, Arr::get($newOptions, $key));
            }
        }
        return $oldOptions;
    }

    public function getSectionSchema() {
        return $this->getSchema($this->sections, []);
    }

    /**
     * return the schema from sections
     */
    public function getSchema($sections = [], $default = []) {
        if ($sections) {
            $resp = [];
            foreach($sections as $item) {
               $resp[$item]  = Arr::get($this->schemas, $item, $default);
            }
            return $resp;
        } 
        return $this->schemas;
    }

    public function rules($action = null, $params = []) {
        return [
            'options' => $this->getSchema($this->sections)
        ];
    }
}
