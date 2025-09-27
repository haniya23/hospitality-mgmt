<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\B2bPartner;

class B2BManagement extends Component
{
    use WithPagination;
    
    public $showCreateModal = false;
    public $partner_name = '';

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
        $this->partner_name = '';

        $this->mobile_number = '';
        $this->email = '';
    }

    public function createPartner()
    {
        $this->validate([
            'partner_name' => 'required|string|max:255',

            'mobile_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        B2bPartner::create([
            'partner_name' => $this->partner_name,
            'partner_type' => 'Business Partner',
            'phone' => $this->mobile_number,
            'email' => $this->email,
            'status' => 'active',
            'requested_by' => auth()->id(),
        ]);

        session()->flash('success', 'B2B Partner created successfully!');
        $this->closeCreateModal();
    }

    public function render()
    {
        $partners = B2bPartner::where('requested_by', auth()->id())
            ->when($this->search, function($query) {
                $query->where('partner_name', 'like', '%' . $this->search . '%')

                      ->orWhere('phone', 'like', '%' . $this->search . '%');
            })->latest()->paginate(15);

        return view('livewire.b2-b-management', compact('partners'))
            ->extends('layouts.app')
            ->section('content');
    }
}