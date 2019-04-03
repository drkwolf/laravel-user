<?php namespace drkwolf\Larauser;

use Illuminate\Support\Arr;
use drkwolf\Package\ValidatableHandler;

class UserOptions extends ValidatableHandler {
    protected $data = [];
    private $schemas = [ ];
    protected $sections;

    public function __construct(array $data, array $sections = []) {
        $this->data = $data;
        $this->sections = $sections;
    }

    /** 
     * update user attributes
     */
    public function getAttributes($oldData) {
        foreach ($this->getSchema($this->sections, []) as $key => $value) {
            if (Arr::has($this->data, $key)) {
                Arr::set($oldData, $key, Arr::get($this->data, $key));
            }
        }
        return $oldData;
    }

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
        return $this->schema;
    }
}
