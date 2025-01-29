<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'legal_system'=>$this->legal_system,
            'answer' => $this->answer,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'lawyer' => new UserResource($this->whenLoaded('user')),
            'situation' => new SituationResource($this->whenLoaded('situation')),

        ];
    }
}
