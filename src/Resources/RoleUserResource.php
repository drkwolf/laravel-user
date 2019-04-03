<?php namespace drkwolf\Larauser\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleUserResource extends JsonResource
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
            'id'    => $this->counter,
            'role_name' => $this->Role->name,
            'role_id' => $this->role_id,
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'club_id' => $this->club_id,

            'metadata' => $this->metadata,
            'update_at' => $this->update_at,
        ];
    }
}
