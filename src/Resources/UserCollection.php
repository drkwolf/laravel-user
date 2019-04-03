<?php namespace drkwolf\Larauser\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection {

    public function __construct($resource, $rolesUsers = null) {
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request)
    {
        return UserResource::collection($this->collection);
    }
}
