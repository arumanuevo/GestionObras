<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'organization' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? $user->phone,
                'address' => $validated['address'] ?? $user->address,
                'city' => $validated['city'] ?? $user->city,
                'state' => $validated['state'] ?? $user->state,
                'postal_code' => $validated['postal_code'] ?? $user->postal_code,
                'country' => $validated['country'] ?? $user->country,
                'organization' => $validated['organization'] ?? $user->organization,
                'position' => $validated['position'] ?? $user->position,
            ];

            // Manejar la foto de perfil
            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo_path) {
                    // Eliminar la imagen anterior
                    $oldPath = public_path('storage/' . $user->profile_photo_path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $image = $request->file('profile_photo');
                $filename = time() . '_' . $image->getClientOriginalName();

                // Guardar la imagen directamente en public/storage/profile-photos
                $destinationPath = public_path('storage/profile-photos');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                $image->move($destinationPath, $filename);

                // Guardar la ruta relativa en la base de datos
                $data['profile_photo_path'] = 'profile-photos/' . $filename;
            }

            // Verificar si se completaron los campos principales para marcar como completo
            $profileCompleted = !empty($validated['name']) &&
                               (!empty($validated['phone']) ||
                                !empty($validated['organization']) ||
                                !empty($validated['position']) ||
                                $request->hasFile('profile_photo'));

            $data['profile_completed'] = $profileCompleted;

            $user->update($data);

            return redirect()->route('profile.show')->with('success', 'Perfil actualizado con éxito.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar perfil: ' . $e->getMessage());
        }
    }
}


