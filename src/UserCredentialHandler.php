<?php namespace drkwolf\Larauser;

use drkwolf\Package\HandlerAbstract;
use drkwolf\Larauser\Entities\User;
use drkwolf\Larauser\Events\UserCredentialUpdatedEvent;
use Illuminate\Support\Facades\Hash;

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
        $this->User = $this->getModel($user, User::class, 'findOrFail');
    }

    protected function updateAction($params = []) {
        $this->User->password = Hash::make($this->get('password'));

        if ($this->get('email')) {
            $this->User->email = $this->get('email');
        }

        if ($this->get('phone')) {
            $this->User->phone = $this->get('phone');
        }

        $this->User->save();

        event(new UserCredentialUpdatedEvent($this->User));
        return $this->User;
    }

    protected function dataFields($action = null) {
        return [
            'password',
            'email',
            'phone',
        ];
    }

    public function rules($action = null, $params = []) {
        return [
            'password'    => 'required|min:8',
            'email'     => 'email|unique:users,id,'. $this->User->id,
            'phone'     => 'unique:users,id,'. $this->User->id,
        ];
    }

}
