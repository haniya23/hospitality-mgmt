<?php

namespace App\Http\Controllers;

use App\Models\StaffTemplate;
use App\Models\Property;
use Illuminate\Http\Request;

class StaffTemplateController extends Controller
{
    public function index()
    {
        $templates = StaffTemplate::getTemplates();
        $properties = Property::where('owner_id', auth()->id())->get();
        
        return view('staff.templates.index', compact('templates', 'properties'));
    }

    public function apply(Request $request)
    {
        $request->validate([
            'template_type' => 'required|in:single_property,multiple_properties',
            'property_id' => 'required|exists:properties,id'
        ]);

        try {
            StaffTemplate::applyTemplate(
                $request->template_type,
                $request->property_id,
                auth()->id()
            );

            return redirect()->back()->with('success', 'Staff template applied successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to apply template: ' . $e->getMessage());
        }
    }

    public function preview($type)
    {
        $templates = StaffTemplate::getTemplates();
        
        if (!isset($templates[$type])) {
            abort(404);
        }

        return response()->json($templates[$type]);
    }
}