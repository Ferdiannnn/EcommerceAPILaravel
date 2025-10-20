<?php

namespace App\Http\Resources;

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
            'user_id' => $this->user_id,
            'category_id' => $this->whenLoaded('Category', fn() => $this->Category->title),
            'title' => $this->title,
            'description' => $this->description,
            'qty' => $this->qty,
            'price' =>  'Rp ' . number_format($this->price),
            'img' => $this->img
        ];
    }
}
