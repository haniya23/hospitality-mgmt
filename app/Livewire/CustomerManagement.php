<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guest;

class CustomerManagement extends Component
{
    use WithPagination;
    
    public $showCreateModal = false;
    public $name = '';
    public $mobile_number = '';
    public $email = '';
    public $search = '';

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->resetForm();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->mobile_number = '';
        $this->email = '';
    }

    public function createCustomer()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Guest::create([
            'name' => $this->name,
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
        ]);

        session()->flash('success', 'Customer created successfully!');
        $this->closeCreateModal();
    }

    public function render()
    {
        $customers = Guest::when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        })->latest()->paginate(15);

        return view('livewire.customer-management', compact('customers'))
            ->extends('layouts.mobile')
            ->section('content');
    }
}