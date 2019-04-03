<?php namespace drkwolf\Larauser\Presenter;

use drkwolf\Larauser\Entities\User;

class TutorPresenter extends UserPresenter {

    private $minor;

    public function __construct(User $minor, $resource = null) {
        $this->minor = $minor;
        parent::__construct($resource);
    }

    public function successResponse($params = []) {
        switch ($this->action) {
            case 'create':
                $this->insertAction('users', $this->userData());
                $this->attachAction('users', 'minorsIds', $this->id, [$this->minor->id]);
                $this->attachAction('users', 'tutorsIds', $this->minor->id, [$this->id]);
                break;
            case 'update':
                $this->updateAction('users', $this->userData());
                break;
        }

        return [ 'actions' => $this->getOrmActions() ];
    }
}
