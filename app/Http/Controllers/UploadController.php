<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Jobs\ProcessCsvUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Resources\UploadResource;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function index()
    {
        return UploadResource::collection(
            Upload::latest()->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt', // no limit
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $upload = Upload::create([
            'filename' => $originalName,
            'filepath' => '-',
            'status' => 'pending',
        ]);
        $filePath = $file->storeAs('uploads', $upload->id . '_' . $originalName);
        $upload->filepath = $filePath;
        $upload->save(); // Save again after setting filepath

        ProcessCsvUpload::dispatch($upload);

        return response()->json([
            'message' => 'File uploaded successfully',
            'upload' => $upload
        ]);
    }
}

