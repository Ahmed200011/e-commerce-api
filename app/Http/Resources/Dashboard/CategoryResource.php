<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "category_name"=>$this->category_name,
            "parent_id"=>$this->parent_id,
            "parent"=> [
                "category_name"=> $this->parent->category_name,
            ],
            'products'=>$this->products
        ];
    }
}
