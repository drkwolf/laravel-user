<?php namespace drkwolf\Larauser;

use drkwolf\Package\HandlerAbstract;
use drkwolf\Larauser\Events\UserCredentialUpdatedEvent;
use Illuminate\Support\Arr;

class UserCredentialHandler extends HandlerAbstract {
    public $User;

    /**
     * @param $data array
     * @param null $player User|int
     * @param string $media_prefix to get upload from request
     */
    public function __construct($presenter, $data, $user) {
        parent::__construct($presenter, $data);
        $this->data['id'] = $this->getModelId($user);
        $UserClass  = config('laratrust.models.user');
        $this->User = $this->getModel($user, $UserClass, 'findOrFail');
    }

    protected function updateAction($filteredData, $params = []) {
        //hashed by User.setPasswordAttribute
        $this->User->password = $this->get('password');

        if ($this->get('email')) {
            $this->User->email = $this->get('email');
        }

        if ($this->get('phone')) {
            $this->User->phone = $this->get('phone');
        }

        if ($this->get('username')) {
            $this->User->username = $this->get('username');
        }

        $this->User->save();

        event(new UserCredentialUpdatedEvent($this->User));
        return $this->User;
    }

    public function resetPasswordAction($filteredData, $params = []) {
        //hashed by User.setPasswordAttribute
        $this->User->password = $this->get('password');
        $this->User->save();
        return $this->User;
    }

    protected function dataFields($action = null) {
        switch ($action) {
            case 'update':
                return ['password', 'email', 'phone', 'username'];
            case 'resetPassword':
                return ['password'];
            default:
                throw new \Exception(static::class . '@dataFields: unknown $action: '. $action);
        }
    }

    public function rules($action = null, $params = []) {
        $rules = [
            'password'    => 'required|min:8',
            'email'     => 'email|unique:users,id,'. $this->User->id,
            'phone'     => 'unique:users,id,'. $this->User->id,
            'username'     => 'unique:users,id,'. $this->User->id,
        ];
        if ($this->dataFields($action)) {
            return Arr::only($rules, $this->dataFields($action));
        } else {
            return $rules;
        }
    }

}
