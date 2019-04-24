<?php namespace drkwolf\Larauser;

use drkwolf\Package\HandlerAbstract;
use drkwolf\Larauser\Events\UserCreatedEvent;
use drkwolf\Larauser\Events\UserUpdatedEvent;

class UserHandler extends HandlerAbstract {
    protected $media_prefix;
    private $role;

    public $User;

    /**
     * @param $data array
     * @param null $player User|int
     * @param string $media_prefix to get upload from request
     */
    public function __construct($presenter, $data, $role, $user = null, $media_prefix = null) {
        $override = [ 'id' => $this->getModelId($user)];
        parent::__construct($presenter, $data, $override);

        $this->role            = $role;
        $this->media_prefix    = $media_prefix;

        $UserClass             = config('laratrust.models.user');
        $method                = $user? 'findOrFail' : 'findOrNew';
        $this->User            = $this->getModel($user, $UserClass, $method);
    }

    public function AttachAvatarAction($params = []) {
        // Attach picture
        $avatarCollection = config('larauser.model.avatar_collection', 'avatars');
        $avatarField = config('larauser.model.avatar_field', 'avatar');
        $pic_path = ($this->media_prefix? $this->media_prefix . '.' : '') . $avatarField;
        if (request()->hasFile($pic_path)) {
            $this->User->addMediaFromRequest($pic_path)
            ->toMediaCollection($avatarCollection);
        }
    }

    protected function createAction($params = []) {
        $this->User->fillWithOptions($this->filteredData, $this->role);
        $this->User->save();

        $this->AttachAvatarAction($params);

        event(new UserCreatedEvent($this->User, $this->role));

        return $this->User;
    }

    protected function updateAction($params = []) {
        $this->User->fillWithOptions($this->filteredData, $this->role);
        $this->User->update();

        $this->AttachAvatarAction($params);

        event(new UserUpdatedEvent($this->User, $this->role));
        return $this->User;
    }

    public function rules($action = null, $params = []) {
        $contactRules = config('larauser.contacts.rules.' . $this->role, []);

        $optionsRules = config('larauser.options.rules.' . $this->role, []);
        $rules = config('larauser.model.rules.default', []);
        $rules = $rules($this->User);
        $userRules = config('larauser.model.rules.' . $this->role, []);

        return array_merge($rules, $userRules, $contactRules, $optionsRules);
    }

}
