<?php
namespace App\Http\Controllers;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('id')->get();
        return view('users', compact('users'));
    }
    public function create(): View
    {
        return view('users.form', ['user' => new User()]);
    }
    public function store(UserRequest $request): RedirectResponse
    {
        User::create($this->attributes($request));
        return redirect()->route('users.index')->with('success', 'User account created successfully.');
    }
    public function edit(User $user): View
    {
        return view('users.form', compact('user'));
    }
    public function update(UserRequest $request, User $user, AvatarService $avatars): RedirectResponse
    {
        $data = $this->attributes($request);
        if (isset($data['email']) && $data['email'] !== $user->email) { $user->email_verified_at = null; }
        $oldAvatar = $user->avatar;
        $newAvatar = null;
        if ($request->hasFile('avatar')) {
            $newAvatar = $avatars->store($request->file('avatar'));
            $data['avatar'] = $newAvatar;
        }
        try {
            $user->update($data);
        } catch (\Throwable $exception) {
            if ($newAvatar) { Storage::disk('public')->delete($newAvatar); }
            throw $exception;
        }
        if ($newAvatar && $oldAvatar && str_starts_with($oldAvatar, 'avatars/')) {
            Storage::disk('public')->delete($oldAvatar);
        }
        return redirect()->route('users.index')->with('success', 'User account updated successfully.');
    }
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }
    public function destroy(\Illuminate\Http\Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 403, 'Use your profile settings to delete your own account.');
        $request->validate(['confirm_delete' => ['accepted']]);
        $avatar = $user->avatar;
        $user->delete();
        if ($avatar && str_starts_with($avatar, 'avatars/')) { Storage::disk('public')->delete($avatar); }
        return redirect()->route('users.index')->with('success', 'User account deleted successfully.');
    }
    private function attributes(UserRequest $request): array
    {
        $data = $request->safe()->except('avatar');
        if (empty($data['password'])) { unset($data['password']); }
        $data['role'] = $data['role'] ?? 'Cashier';
        $data['status'] = $data['status'] ?? 'Active';
        return $data;
    }
}

