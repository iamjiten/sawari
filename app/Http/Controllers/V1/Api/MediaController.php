<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Plank\Mediable\Exceptions\MediaUpload\ConfigurationException;
use Plank\Mediable\Exceptions\MediaUpload\FileExistsException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotFoundException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotSupportedException;
use Plank\Mediable\Exceptions\MediaUpload\FileSizeException;
use Plank\Mediable\Exceptions\MediaUpload\ForbiddenException;
use Plank\Mediable\Facades\MediaUploader;

class MediaController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            Media::class,
            MediaResource::class,
            '',
            ''
        );
    }

    public function uploadFiles(Request $request)
    {
        $this->validate($request, [
            'uploaded-file' => 'required',
            'upload-for' => 'required',
        ]);
        $path = $request->get('upload-for');
        if ($path == 'postImage') {
            $this->validate($request, [
                'uploaded-file' => 'required|file|image|max:10240',
            ]);
        }

        if ($path == 'postVideos') {
            $this->validate($request, [
                'uploaded-file' => 'required|file|mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4,jpg,jpeg,png,bmp,tiff,mp4|max:10240',
            ]);
        }

        if ($path == 'excel') {
            $this->validate($request, [
                'uploaded-file' => 'required|max:5000|mimes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,xls,xlsx,csv,txt|max:10240',
            ]);
        }

        try {
            if ($path == 'base64') {
                $jpg = Image::make($request->input('uploaded-file'))->encode('jpg');
                $media = MediaUploader::fromString($jpg)
                    ->toDestination(config('filesystems.multi') ? 'minio_write'
                        : config('filesystems.default'),
                        $path)
                    ->makePublic()
                    ->upload();
            } else {
                $media = MediaUploader::fromSource($request->file('uploaded-file'))
                    ->toDestination(config('filesystems.multi') ? 'minio_write'
                        : config('filesystems.default'),
                        $path)
                    ->makePublic()
                    ->upload();
            }


            $media->forceFill([
                'reference_name' => $media->filename,
                'alt_text' => $media->filename,
                'caption' => $media->filename,
                'disk' => config('filesystems.default'),
            ])->save();

            $file = $media->directory . '/' . $media->filename . '.'
                . $media->extension;
            $p = Storage::disk(config('filesystems.multi') ? 'minio_write'
                : config('filesystems.default'))
                ->url($file);

            return Response::json([
                'code' => 200,
                'id' => $media->id,
                'media' => $media->id,
                'file' => $media->id,
                'payload' => $p,
                'image' => $p,
                'path' => $p,
                'aggregate_type' => $media->aggregate_type,
                'message' => 'Success',
            ], 200);
        } catch (ConfigurationException|FileExistsException|FileNotFoundException|FileNotSupportedException|FileSizeException|ForbiddenException $exception) {
            abort(400, $exception->getMessage());
        }
    }
}
