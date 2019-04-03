<?php namespace drkwolf\Larauser\Presenter;

use drkwolf\Package\Presenter\PresenterAbstract;

class UserCredentialPresenter extends PresenterAbstract {

    public function successResponse($params = []) {
        return $this->getData();
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function getData() {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
