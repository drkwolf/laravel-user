<?php namespace drkwolf\Larauser;

use drkwolf\Package\HandlerAbstract;
use drkwolf\Larauser\Events\UserCreatedEvent;
use drkwolf\Larauser\Events\UserUpdatedEvent;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UserHandler extends HandlerAbstract {
    protected $media_prefix;
    protected $avatar_field = 'avatar';
    private $role;

    public $User;
    public $UserOptions;

    /**

     * @param $data array
     * @param null $player User|int
     * @param string $media_prefix to get upload from request
     */
    public function __construct($presenter, $data, UserOptions $userOptions, $role, $user = null, $media_prefix = null) {
        // $this->data = Arr::except($data, 'options');
        $override = [ 'id' => $this->getModelId($user)];
        parent::__construct($presenter, $data, $override);

        $this->role            = $role;
        $this->media_prefix    = $media_prefix;

        $UserClass             = config('laratrust.models.user');
        $method                = $user? 'findOrFail' : 'findOrNew';
        $this->User            = $this->getModel($user, $UserClass, $method);
        $this->UserOptions     = $userOptions;
    }

    public function AttachAvatarAction($params = []) {
        // Attach picture
        $avatarCollection = config('larauser.model.avatar_collection', 'avatars');
        $pic_path = ($this->media_prefix? $this->media_prefix . '.' : '') . $this->avatar_field;
        if (request()->hasFile($pic_path)) {
            $this->User->addMediaFromRequest($pic_path)
            ->toMediaCollection($avatarCollection);
        }
    }

    protected function createAction($params = []) {
        $this->User->fillWithOptions($this->filteredData, $this->UserOptions);
        $this->User->save();

        $this->AttachAvatarAction($params);

        event(new UserCreatedEvent($this->User, $this->role));

        return $this->User;
    }

    protected function updateAction($params = []) {
        $this->User->fillWithOptions($this->filteredData, $this->UserOptions);
        $this->User->update();

        $this->AttachAvatarAction($params);

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
