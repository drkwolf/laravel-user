<?php namespace drkwolf\Larauser\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleUserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return RoleUserResource::collection($this->collection);
//        return parent::toArray($request);
    }
}
