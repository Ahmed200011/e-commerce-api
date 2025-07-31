<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "product_name" => $this->product_name,
            "description" => $this->description,
            "price" => $this->price,
            "cardImage" =>url('dashboard/assets/images/products/cards/'.$this->image),
            "detailsImage" =>url('dashboard/assets/images/products/details/'.$this->image),
            "category" => [
                "category_name" => $this->category->category_name,
            ]

        ];

    }
}
