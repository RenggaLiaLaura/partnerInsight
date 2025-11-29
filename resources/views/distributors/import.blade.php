@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Import Distributors</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload Excel file to bulk import distributor data.</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session('errors_count'))
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <strong>Import completed with {{ session('errors_count') }} errors:</strong>
        <ul class="mt-2 ml-4 list-disc">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Upload Form -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upload File</h3>
        
        <form action="{{ route('distributors.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Excel File</label>
                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" 
                       id="file_input" 
                       type="file" 
                       name="file" 
                       accept=".xlsx,.xls,.csv"
                       required>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Accepted formats: .xlsx, .xls, .csv</p>
                @error('file')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand-600 dark:hover:bg-brand-700 dark:focus:ring-brand-800">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                Import Data
            </button>
        </form>
    </div>

    <!-- Instructions -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Instructions</h3>
        
        <ol class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
            <li class="flex items-start">
                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900 dark:text-brand-300 mr-3 font-semibold">1</span>
                <span>Download the template file below</span>
            </li>
            <li class="flex items-start">
                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900 dark:text-brand-300 mr-3 font-semibold">2</span>
                <span>Fill in your distributor data following the format</span>
            </li>
            <li class="flex items-start">
                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900 dark:text-brand-300 mr-3 font-semibold">3</span>
                <span>Upload the completed file using the form</span>
            </li>
        </ol>

       
    </div>
</div>
@endsection
