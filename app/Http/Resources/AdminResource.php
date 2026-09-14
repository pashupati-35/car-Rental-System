<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $imageUrl = is_array($this->image_path)
            ? ($this->image_path['original'] ?? null)
            : (is_string($this->image_path) ? $this->image_path : ($this->image ? (str_starts_with($this->image, 'http') ? $this->image : asset($this->image)) : ($this->avatar ? (str_starts_with($this->avatar, 'http') ? $this->avatar : asset($this->avatar)) : null)));

        return [
            'id' => $this->id,
            'unique_identifier' => $this->unique_identifier,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'address' => $this->address,
            'designation' => $this->designation,
            'position' => $this->position,
            'avatar' => $this->avatar,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'image_url' => $imageUrl,
            'file_path' => $this->file_path ?? $this->image_path,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'nationality' => $this->nationality,
            'citizenship_number' => $this->citizenship_number,
            'passport_number' => $this->passport_number,
            'user_type' => $this->user_type,
            'access_type' => $this->access_type,
            'has_email_access' => (bool) $this->has_email_access,
            'access_email_type' => $this->access_email_type,
            'approval_status' => $this->approval_status,
            'register_type' => $this->register_type,
            'is_submitted' => (bool) $this->is_submitted,
            'theme_style' => $this->theme_style,
            'emergency_contact' => $this->emergency_contact,
            'contact_person_name' => $this->contact_person_name,
            'contact_relationship' => $this->contact_relationship,
            'is_mfa_enabled' => (bool) $this->is_mfa_enabled,
            'is_email_authentication_enabled' => (bool) $this->is_email_authentication_enabled,
            'is_active' => (bool) $this->is_active,
            'is_login_verified' => (bool) $this->is_login_verified,
            'last_logged_in' => $this->last_logged_in?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
