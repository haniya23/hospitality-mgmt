<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PropertyPhotoController extends Controller
{
    public function upload(Request $request, Property $property)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240'
            ]);
            
            // Remove existing photo
            $existingPhoto = $property->photos()->first();
            if ($existingPhoto) {
                if (Storage::disk('public')->exists($existingPhoto->file_path)) {
                    Storage::disk('public')->delete($existingPhoto->file_path);
                }
                $existingPhoto->delete();
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('photo'));
            $image->resize(1200, 675);
            $encoded = $image->toJpeg(85);
            
            $filename = 'property-photos/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, $encoded);
            
            $property->photos()->create([
                'file_path' => $filename,
                'is_main' => true,
                'caption' => 'Property Photo',
                'file_size' => strlen($encoded),
                'sort_order' => 0,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Photo uploaded successfully']);
            
        } catch (\Exception $e) {
            \Log::error('Photo upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}