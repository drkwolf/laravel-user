<?php namespace drkwolf\Larauser\Resources;

use drkwolf\Package\Presenter\PresenterAbstract;
use Carbon\Carbon;

class UserInfoResource extends PresenterAbstract {

    public function successResponse($params = []): array {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'sex' => $this->sex,
            'birthdate' => $this->when($this->birthdate, function () { return $this->birthdate->toDateString();}),

            'phone' => $this->when($this->phone, $this->phone),
            'email' => $this->when($this->email, $this->email),

            // attached attributes
            'age'   => $this->age,
            'name' => $this->name,

            // personal
            'picture' => $this->picture_url,
            'address' => $this->address,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'state' => $this->state,
            'country' => $this->country,

            'options' => $this->options,
            'contacts' => $this->contacts,
        ];
    }
}
