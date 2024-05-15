<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use App\Services\FileUploadServices;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = File::with('office')->get();
        return response()->json(['files' => $files], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, FileUploadServices $file_upload)
    {

        $validate = $request->validate([
            'title' => 'required | string',
            'file' => 'required | file | mimes:doc,docx,pdf,xlsx,xls,ppt,pptx,txt',
            'office_id' => 'required | numeric',
            'remarks' => 'required | string',
            'date_received' => 'required | date',
        ]);

        $path = $file_upload->upload($request->file('file'), 'uploads');
        $date_format = date_format(date_create($validate['date_received']), 'Y-m-d H:i:s');

        $file = File::create([
            'title' => $validate['title'],
            'path' => $path,
            'user_id' => 1,
            'office_id' => $validate['office_id'],
            'remarks' => $validate['remarks'],
            'date_received' => $date_format,
        ]);

        if ($file) return response()->json(['message' => 'File uploaded successfully'], 200);
        return response()->json(['message' => 'Failed to upload file'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
       
        $validate = $request->validate([
            'title' => 'required | string',
            'office_id' => 'required | numeric',
            'remarks' => 'required | string',
            'date_received' => 'required | date',
        ]);

        $file = File::findOrFail($id);

        $file->title = $validate['title'];
        $file->office_id = $validate['office_id'];
        $file->remarks = $validate['remarks'];
        $file->date_received = $validate['date_received'];
        $file->save();

        if ($file) return response()->json(['message' => 'File updated successfully'], 200);
        return response()->json(['message' => 'Failed to update file'], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $file = File::findOrFail($id);
        if ($file){
            $file->delete();
            return response()->json(['message' => 'File deleted successfully'], 200);
        }

        return response()->json(['message' => 'Failed to delete file'], 500);
    }

    public function download($id)
    {
        $file = File::find($id);
        return Storage::download($file->path);
    }
}
