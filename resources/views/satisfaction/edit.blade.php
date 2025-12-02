@extends('layouts.app')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                  <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-brand-600 dark:text-gray-300 dark:hover:text-white">
                      <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                      Home
                    </a>
                  </li>
                  <li>
                    <div class="flex items-center">
                      <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                      <a href="{{ route('satisfaction.index') }}" class="ml-1 text-gray-700 hover:text-brand-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">Satisfaction</a>
                    </div>
                  </li>
                  <li>
                    <div class="flex items-center">
                      <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                      <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Edit</span>
                    </div>
                  </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Edit Satisfaction Score</h1>
        </div>
    </div>
</div>

<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <form action="{{ route('satisfaction.update', $satisfaction->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="distributor_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Distributor</label>
                <select id="distributor_id" name="distributor_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500">
                    @foreach($distributors as $distributor)
                        <option value="{{ $distributor->id }}" {{ $satisfaction->distributor_id == $distributor->id ? 'selected' : '' }}>{{ $distributor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="period" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Period</label>
                <input type="date" id="period" name="period" value="{{ $satisfaction->period }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" required>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Satisfaction Metrics (1-5)</h3>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach(['quality_product' => 'Product Quality', 'spec_conformity' => 'Specification Conformity', 'quality_consistency' => 'Quality Consistency', 'price_quality' => 'Price vs Quality', 'product_condition' => 'Product Condition', 'packaging_condition' => 'Packaging Condition'] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
                    <input type="number" id="{{ $field }}" name="{{ $field }}" value="{{ $satisfaction->$field }}" min="1" max="5" class="metric-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" required>
                </div>
                @endforeach
            </div>
            <div class="mt-4 p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                <div class="flex items-center justify-between">
                    <span class="text-lg font-medium text-gray-900 dark:text-white">Calculated Score:</span>
                    <span id="calculated_score" class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $satisfaction->score }}</span>
                </div>
            </div>
        </div>

        <button type="submit" class="text-white bg-brand-700 hover:bg-brand-800 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-brand-600 dark:hover:bg-brand-700 dark:focus:ring-brand-800">Update</button>
    </form>
</div>

<script>
    const inputs = document.querySelectorAll('.metric-input');
    const scoreDisplay = document.getElementById('calculated_score');

    function calculateAverage() {
        let sum = 0;
        let count = 0;
        inputs.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) {
                sum += val;
                count++;
            }
        });
        
        const average = count === 6 ? (sum / 6).toFixed(2) : '0.00';
        scoreDisplay.textContent = average;
    }

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value > 5) this.value = 5;
            if (this.value < 1 && this.value !== '') this.value = 1;
            calculateAverage();
        });
    });
</script>
@endsection
