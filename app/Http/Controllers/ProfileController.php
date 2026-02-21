<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image; 

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

    // Todos los campos son opcionales
    $validated = $request->validate([
        'first_name' => 'nullable|string|max:100',
        'last_name' => 'nullable|string|max:100',
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
        $data = [];

        // Solo actualizamos los campos que se enviaron en el formulario
        if ($request->has('first_name')) {
            $data['first_name'] = $validated['first_name'];
        }
        if ($request->has('last_name')) {
            $data['last_name'] = $validated['last_name'];
        }
        if ($request->has('phone')) {
            $data['phone'] = $validated['phone'];
        }
        if ($request->has('address')) {
            $data['address'] = $validated['address'];
        }
        if ($request->has('city')) {
            $data['city'] = $validated['city'];
        }
        if ($request->has('state')) {
            $data['state'] = $validated['state'];
        }
        if ($request->has('postal_code')) {
            $data['postal_code'] = $validated['postal_code'];
        }
        if ($request->has('country')) {
            $data['country'] = $validated['country'];
        }
        if ($request->has('organization')) {
            $data['organization'] = $validated['organization'];
        }
        if ($request->has('position')) {
            $data['position'] = $validated['position'];
        }

        // Manejar la foto de perfil
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::delete('public/' . $user->profile_photo_path);
            }

            $image = $request->file('profile_photo');
            $filename = time() . '_' . $image->getClientOriginalName();

            // Guardar la imagen sin redimensionar
            $path = $request->file('profile_photo')->store('profile-photos', 'public');

            $data['profile_photo_path'] = $path;
        }

        // Verificar si se completaron los campos principales para marcar como completo
        $profileCompleted = !empty($user->first_name) && !empty($user->last_name) &&
                           (!empty($user->phone) || !empty($user->organization) || !empty($user->position) || $request->hasFile('profile_photo'));

        $data['profile_completed'] = $profileCompleted;

        $user->update($data);

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado con éxito.');

    } catch (\Exception $e) {
        Log::error('Error al actualizar perfil: ' . $e->getMessage());
        return back()->with('error', 'Error al actualizar perfil: ' . $e->getMessage());
    }
}


}
