<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
class UserRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $user = $this->route('user');
        return [
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user)],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
            'role' => ['nullable', Rule::in(['Administrator', 'Manager', 'Cashier', 'Inventory Clerk'])],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
            'avatar' => $user
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4096,max_height=4096']
                : ['prohibited'],
        ];
    }
    public function messages(): array
    {
        return [
            'avatar.max' => 'The profile picture must be no larger than 2 MB.',
            'avatar.mimes' => 'Choose a JPEG, PNG, or WebP image.',
            'avatar.dimensions' => 'Choose an image no larger than 4096 by 4096 pixels.',
            'username.unique' => 'That username is already in use. Please choose another.',
        ];
    }
}
