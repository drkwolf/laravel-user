<?php namespace drkwolf\Larauser\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
{

    public function __construct($resource, $club_id = null, $team_id = null)
    {
        $this->club_id = $club_id;
        $this->team_id = $team_id;
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            $this->collection->map(function($role) {
                return new RoleResource($role, $this->club_id, $this->team_id);
            })->toArray();
    }

}
