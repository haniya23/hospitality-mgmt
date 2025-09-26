<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user owns the property
        $property = $this->route('property');
        return $property && $property->owner_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $section = $this->input('section');
        
        switch ($section) {
            case 'basic':
                return [
                    'name' => 'required|string|max:255',
                    'property_category_id' => 'nullable|exists:property_categories,id',
                    'description' => 'nullable|string',
                    'owner_name' => 'nullable|string|max:255',
                ];
                
            case 'location':
                return [
                    'address' => 'nullable|string',
                    'country_id' => 'nullable|integer|exists:countries,id',
                    'state_id' => 'nullable|integer|exists:states,id',
                    'district_id' => 'nullable|integer|exists:districts,id',
                    'city_id' => 'nullable|integer|exists:cities,id',
                    'pincode_id' => 'nullable|integer|exists:pincodes,id',
                    'latitude' => 'nullable|string',
                    'longitude' => 'nullable|string',
                ];
                
            case 'amenities':
                return [
                    'amenities' => 'nullable|array',
                    'amenities.*' => 'exists:amenities,id',
                ];
                
            case 'policies':
                return [
                    'check_in_time' => 'nullable|string',
                    'check_out_time' => 'nullable|string',
                    'cancellation_policy' => 'nullable|string',
                    'house_rules' => 'nullable|string',
                ];
                
            default:
                return [];
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Property name is required.',
            'name.max' => 'Property name cannot exceed 255 characters.',
            'property_category_id.exists' => 'Selected property category is invalid.',
            'owner_name.max' => 'Owner name cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'property_category_id' => 'property type',
            'owner_name' => 'owner name',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if (request()->expectsJson()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        parent::failedValidation($validator);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Handle JSON input
        if ($this->isJson()) {
            $data = json_decode($this->getContent(), true);
            if (is_array($data)) {
                // Convert empty strings to null for integer fields
                $integerFields = ['country_id', 'state_id', 'district_id', 'city_id', 'pincode_id', 'property_category_id'];
                foreach ($integerFields as $field) {
                    if (isset($data[$field]) && $data[$field] === '') {
                        $data[$field] = null;
                    }
                }
                $this->merge($data);
            }
        }
    }
}
