<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
class AvatarService
{
    public function store(UploadedFile $upload): string
    {
        try {
            $manager = new ImageManager(new Driver());
            $jpeg = $manager->read($upload->getRealPath())
                ->cover(256, 256)->toJpeg(quality: 85, strip: true);
        } catch (\Throwable $exception) {
            report($exception);
            throw ValidationException::withMessages([
                'avatar' => 'This image could not be prepared. Please try another JPEG, PNG, or WebP file.',
            ]);
        }

        $path = 'avatars/'.Str::uuid().'.jpg';
        if (!Storage::disk('public')->put($path, (string) $jpeg)) {
            throw new \RuntimeException('The prepared avatar could not be saved.');
        }
        return $path;
    }
}
