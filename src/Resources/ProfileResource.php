<?php namespace drkwolf\Larauser\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class ProfileResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name'    => $this->name,
            'birthdate' => $this->birthdate instanceof Carbon
                ? $this->birthdate->toDateString()
                : $this->birthdate,
            'sex' => $this->sex,
            'options' => $this->resource->options,

            // login data
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'active' => $this->active? $this->active : 0,

            // attached attributes
            'age'   => $this->age,

            // personal
            'picture' => $this->picture_url,
            'address' => $this->address,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'state' => $this->state,
            'country' => $this->country,

            'contacts' => $this->contacts,
        ];
    }
}
