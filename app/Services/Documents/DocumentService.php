<?php

namespace App\Services\Documents;

use App\Models\DocumentVersion;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function storeNewVersion(Submission $submission, User $uploader, UploadedFile $file): DocumentVersion
    {
        $disk = config('filesystems.default');

        $nextVersion = (int) ($submission->documentVersions()->max('version_number') ?? 0) + 1;

        $path = $file->store("submissions/{$submission->id}", $disk);

        return DocumentVersion::query()->create([
            'submission_id' => $submission->id,
            'version_number' => $nextVersion,
            'file_key' => $path,
            'file_type' => $file->getClientMimeType(),
            'uploaded_by' => $uploader->id,
            'created_at' => now(),
        ]);
    }

    public function temporaryDownloadUrl(string $fileKey, string $disk = null): string
    {
        $disk ??= config('filesystems.default');

        return Storage::disk($disk)->temporaryUrl($fileKey, now()->addMinutes(10));
    }
}
