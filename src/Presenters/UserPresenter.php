<?php namespace drkwolf\Larauser\Presetner;

use drkwolf\Package\ClientActionsTrait;
use drkwolf\Package\Presenter\OrmPresenterAbstract;
use Carbon\Carbon;

class UserPresenter extends OrmPresenterAbstract {

    public function successResponse($params = []) {
        switch ($this->action) {
            case 'create':
                $this->insertAction('users', $this->userData());
                break;
            case 'update':
                $this->updateAction('users', $this->userData());
                break;
        }

        return [
            'actions' => $this->getOrmActions()
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function userData() {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'sex' => $this->sex,
            'birthdate' => $this->birthdate instanceof Carbon
                ? $this->birthdate->toDateString()
                : $this->birthdate,

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

            // relationships
            'teamsIds' => $this->whenLoaded('teams', $this->teams->pluck('id')),
//            'teamsIds2' => $this->getTeamsIds(),
            'tutorsIds' => $this->whenLoaded('tutors', $this->tutors->pluck('id')),
            'subscriptionsIds' => $this->whenLoaded('subscriptions', $this->subscriptions->pluck('id')),
            'playersIds' => $this->whenLoaded('minors', $this->minors->pluck('id')),

            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
