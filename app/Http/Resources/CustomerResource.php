<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
            'phone_number' => $this->phone_number,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'marital_status' => $this->marital_status,
            'nationality' => $this->nationality,
            'citizenship_number' => $this->citizenship_number,
            'passport_number' => $this->passport_number,
            'position' => $this->position,
            'designation' => $this->designation,
            'user_type' => $this->user_type,
            'access_type' => $this->access_type,
            'has_email_access' => $this->has_email_access,
            'access_email_type' => $this->access_email_type,
            'approval_status' => $this->approval_status,
            'register_type' => $this->register_type,
            'is_submitted' => $this->is_submitted,
            'theme_style' => $this->theme_style,
            'emergency_contact' => $this->emergency_contact,
            'contact_person_name' => $this->contact_person_name,
            'contact_relationship' => $this->contact_relationship,
            'is_mfa_enabled' => (bool) $this->is_mfa_enabled,
            'is_email_authentication_enabled' => (bool) $this->is_email_authentication_enabled,
            'is_active' => (bool) $this->is_active,
            'is_login_verified' => (bool) $this->is_login_verified,
            'last_logged_in' => $this->last_logged_in?->toIso8601String(),
            'admin_id' => $this->admin_id,
            'owner_id' => $this->owner_id,
            'image' => $this->image,
            'image_path' => $this->image_path,
            'image_url' => is_array($this->image_path) ? ($this->image_path['original'] ?? null) : (is_string($this->image_path) ? $this->image_path : ($this->image ? (str_starts_with($this->image, 'http') ? $this->image : asset($this->image)) : null)),
            'file_path' => $this->file_path ?? $this->image_path,
            'bookings_count' => $this->bookings_count,
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
