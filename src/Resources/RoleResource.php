<?php namespace drkwolf\Larauser\Resources;

use App\Packages\Response\KeyResourceResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{

    public function __construct($resource, $club_id = null, $team_id = null)
    {
        $this->club_id = $club_id;
        $this->team_id = $team_id;
        parent::__construct($resource);
    }

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
            'uuid' => $this->uuid,
            'name' => $this->name,
            'display_name' => $this->display_name,
//            'users'   => $this->getUsers(),
            'update_at' => $this->updated_at->toDateTimeString(),
        ];
    }


    protected function getUsers() {
        $teams = $this->Teams($this->club_id)->orderBy('id')->get(['id', 'role_user.club_id']);
        $data = [];
        foreach ($teams as $team) {
            $data[] =  [
                'id' => $team->id,
                'club_id' => (int)$team->club_id,
                'usersIds' => $this->Users($team->club_id, $team->id)
                    ->get(['id'])
                    ->pluck('id')->sort()->toArray()
            ];
        }
        return $data;
    }
}
