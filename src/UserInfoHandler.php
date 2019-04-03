<?php namespace drkwolf\Larauser;

use drkwolf\Package\HandlerAbstract;
use drkwolf\Larauser\Entities\User;
use drkwolf\Larauser\Events\UserUpdatedEvent;
use Illuminate\Validation\Rule;

class UserInfoHandler extends HandlerAbstract {
    protected $media_prefix;
    public $User;

    /**
     * @param $data array
     * @param null $player User|int
     * @param string $media_prefix to get upload from request
     */
    public function __construct($presenter, $data, $user = null, $media_prefix = null) {
        parent::__construct($presenter, $data);
        $this->media_prefix = $media_prefix;
        $this->User = $this->getModel($user, User::class, 'findOrNew');
    }

    protected function updateAction($params = []) {
        $this->User->fill($this->data);
        $this->User->save();

        // Attach picture
        $pic_path = $this->media_prefix? $this->media_prefix . '.picture' : 'picture';
        if (request()->hasFile($pic_path)) {
            $this->User->addMediaFromRequest($pic_path)->toMediaCollection('pictures');
        }

        event(new UserUpdatedEvent($this->User));
        return $this->User;
    }

    protected function dataFields($action = null) {
        return [
            'first_name',
            'last_name',
            'birthdate',
            'sex',
            'address',
            'postcode',
            'city',
            'state',
            'country',
            'contacts.emails',
            'contacts.phones',
        ];
    }

    public function rules($action = null, $params = []) {
        $rules = [

            'first_name'    => 'required|string|max:50',
            'last_name'     => 'required|string|max:50',
            'birthdate'     => 'required|date',
            'sex'           => [ 'required', Rule::in(['M', 'F']), ],
//            'email'         =>  $email,
            'address'       => 'required',
            'postcode'      => 'required',
            'city'          => 'required',
            'state'         => 'required',
            'country'       => 'string',

            'contacts'      => 'array',
            'contacts.phones.*.prefix'      => 'required',
            'contacts.phones.*.suffix'      => 'required',
            'contacts.emails.*.email'      => 'email',
        ];

        $new_rules = array_get($params, 'rules', []);
        return array_merge($rules, $new_rules);
    }

}
