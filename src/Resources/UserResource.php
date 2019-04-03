<?php namespace drkwolf\Larauser\Resources;

use drkwolf\Larauser\Entities\RoleUser;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    protected $rolesUsers;

    public static function fields() {
        return [
            'id',
            'first_name',
            'last_name',
            'sex',
            'birthdate',

            'phone',
            'email',
            'address',
            'city',
            'postcode',
            'state',
            'country'
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
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
            'contact' => $this->default_contact,
            'contacts' => $this->contacts,

            // relationships
            'teamsIds' => $this->whenLoaded('teams', $this->teams->pluck('id')),
            'teamsIds2' => $this->getTeamsIds(),
            'tutorsIds' => $this->whenLoaded('tutors', $this->tutors->pluck('id')),
            'subscriptionsIds' => $this->whenLoaded('subscriptions', $this->subscriptions->pluck('id')),
            'playersIds' => $this->whenLoaded('minors', $this->minors->pluck('id')),

            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }

    protected function getTeamsIds() {
        return RoleUser::where('user_id', $this->id)
            ->where('club_id', 1)  // FIXME
            ->whereNotNull('team_id')
            ->with('role')
            ->get()
            ->mapToGroups(function ($item, $key) {
                $key = 'teams' .  ucfirst($item->role->name) . 'Ids';
               return [ $key => $item->team_id];
            })->toArray();
    }
}
