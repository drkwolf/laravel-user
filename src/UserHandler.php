<?php namespace drkwolf\Larauser;

use drkwolf\Package\HandlerAbstract;
use drkwolf\Larauser\Events\UserCreatedEvent;
use drkwolf\Larauser\Events\UserUpdatedEvent;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UserHandler extends HandlerAbstract {
    protected $media_prefix;
    private $role;

    public $User;
    public $UserOptions;

    /**

     * @param $data array
     * @param null $player User|int
     * @param string $media_prefix to get upload from request
     */
    public function __construct($presenter, $data, UserOptions $userOptions, $role, $user = null, $media_prefix = null) {
        $this->data = Arr::except($data, 'options');
        parent::__construct($presenter, $data);

        $this->data['id']      = $this->getModelId($user);
        $this->role            = $role;
        $this->media_prefix    = $media_prefix;

        $UserClass             = config('laratrust.models.user');
        $this->User            = $this->getModel($user, $UserClass, 'findOrNew');
        $this->UserOptions     = $userOptions;
    }

    protected function createAction($params = []) {
        $this->User->fillWithOptions($this->data, $this->UserOptions);
        $this->User->save();

        // Attach picture
        $pic_path = $this->media_prefix? $this->media_prefix . '.picture' : 'picture';
        if (request()->hasFile($pic_path)) {
            $this->User->addMediaFromRequest($pic_path)->toMediaCollection('pictures');
        }
        event(new UserCreatedEvent($this->User, $this->role));

        return $this->User;
    }

    protected function updateAction($params = []) {
        $this->User->fillWithOptions($this->data, $this->UserOptions);
        $this->User->update();

        // Attach picture
        $pic_path = $this->media_prefix? $this->media_prefix . '.picture' : 'picture';
        if (request()->hasFile($pic_path)) {
            $this->User->addMediaFromRequest($pic_path)->toMediaCollection('pictures');
        }

        event(new UserUpdatedEvent($this->User, $this->role));
        return $this->User;
    }

    public function rules($action = null, $params = []) {
        $rules = [
            'first_name'    => 'required|string|max:50',
            'last_name'     => 'required|string|max:50',
            'birthdate'     => 'date|nullable',
            'sex'           => [ 'nullable', Rule::in(['M', 'F']), ],
            'email'         => 'nullable,email|unique:users,email',
            'phone'         => 'nullable|unique:users,phone',
        ];
        return array_merge(
            $rules, $this->UserOptions->rules(actions, $params)
        );
    }

}
