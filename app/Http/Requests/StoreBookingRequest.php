<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'accommodation_id' => 'required|exists:property_accommodations,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'guest_id' => 'required_without:create_new_guest|exists:guests,id',
            'guest_name' => 'required_if:create_new_guest,true|string|max:255',
            'guest_mobile' => 'required_if:create_new_guest,true|string|max:20',
            'total_amount' => 'required|numeric|min:0',
            'advance_paid' => 'required|numeric|min:0|lte:total_amount',
        ];
    }

    public function messages()
    {
        return [
            'property_id.required' => 'Please select a property.',
            'accommodation_id.required' => 'Please select an accommodation.',
            'check_in_date.required' => 'Check-in date is required.',
            'check_out_date.required' => 'Check-out date is required.',
            'check_out_date.after' => 'Check-out date must be after check-in date.',
            'guest_name.required_if' => 'Guest name is required when creating a new guest.',
            'guest_mobile.required_if' => 'Guest mobile is required when creating a new guest.',
            'total_amount.required' => 'Total amount is required.',
            'advance_paid.lte' => 'Advance paid cannot exceed total amount.',
        ];
    }
}