<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = ['username', 'full_name', 'email', 'password', 'role', 'status', 'avatar'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }
    public function getNameAttribute(): string { return $this->full_name; }
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar && Storage::disk('public')->exists($this->avatar)
            ? Storage::disk('public')->url($this->avatar)
            : asset('images/avatar-placeholder.svg');
    }
}
