@extends('layouts.app')

@section('content')
<div class="mb-6">
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-brand-600 dark:text-gray-300 dark:hover:text-white">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('distributors.index') }}" class="ml-1 text-gray-700 hover:text-brand-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">Distributors</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Add New</span>
                </div>
            </li>
        </ol>
    </nav>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Distributor</h1>
</div>

<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <div class="p-6">
        <form action="{{ route('distributors.store') }}" method="POST">
            @csrf
            <div class="grid gap-6 mb-6 grid-cols-1 sm:grid-cols-2">
                <div>
                    <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Distributor Code</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" placeholder="e.g. DIST-001" required>
                    @error('code')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Distributor Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" placeholder="e.g. PT. Sinar Jaya" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" maxlength="12" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" placeholder="e.g. 08123456789" required>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Hidden inputs to store names for the concatenated region string -->
                    <input type="hidden" id="province_name" name="province_name">
                    <input type="hidden" id="regency_name" name="regency_name">
                    <input type="hidden" id="district_name" name="district_name">
                    <input type="hidden" id="village_name" name="village_name">

                    <div>
                        <label for="province_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Province</label>
                        <select id="province_id" name="province_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" required>
                            <option value="" selected disabled>Choose Province</option>
                        </select>
                    </div>
                    <div>
                        <label for="regency_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City/Regency</label>
                        <select id="regency_id" name="regency_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" required disabled>
                            <option value="" selected disabled>Choose City/Regency</option>
                        </select>
                    </div>
                    <div>
                        <label for="district_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">District</label>
                        <select id="district_id" name="district_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" required disabled>
                            <option value="" selected disabled>Choose District</option>
                        </select>
                    </div>
                    <div>
                        <label for="village_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Village</label>
                        <select id="village_id" name="village_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" required disabled>
                            <option value="" selected disabled>Choose Village</option>
                        </select>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                    <textarea id="address" name="address" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" placeholder="e.g. Jl. Sudirman No. 123" required>{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 sm:space-x-3">
                <a href="{{ route('distributors.index') }}" class="w-full sm:w-auto text-gray-700 bg-white border border-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancel</a>
                <button type="submit" class="w-full sm:w-auto text-white bg-brand-700 hover:bg-brand-800 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand-600 dark:hover:bg-brand-700 dark:focus:ring-brand-800">Save Distributor</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Phone Number Validation
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 12);
        });

        // 2. Cascading Dropdowns Logic
        const elements = {
            province: document.getElementById('province_id'),
            regency: document.getElementById('regency_id'),
            district: document.getElementById('district_id'),
            village: document.getElementById('village_id'),
            names: {
                province: document.getElementById('province_name'),
                regency: document.getElementById('regency_name'),
                district: document.getElementById('district_name'),
                village: document.getElementById('village_name'),
            }
        };

        const baseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';

        // Helper to fetch and populate
        const loadData = async (url, element, placeholder, nextElement = null) => {
            element.innerHTML = `<option value="" selected disabled>Loading...</option>`;
            try {
                const response = await fetch(url);
                const data = await response.json();
                element.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name.replace(/\w\S*/g, (w) => (w.replace(/^\w/, (c) => c.toUpperCase()))); // Title Case
                    element.appendChild(option);
                });
                element.disabled = false;
                if (nextElement) nextElement.innerHTML = `<option value="" selected disabled>Choose ${nextElement.id.split('_')[0].charAt(0).toUpperCase() + nextElement.id.split('_')[0].slice(1)}</option>`;
            } catch (error) {
                console.error(error);
                element.innerHTML = `<option value="" selected disabled>Error loading data</option>`;
            }
        };

        // Load Provinces
        loadData(`${baseUrl}/provinces.json`, elements.province, 'Choose Province');

        // Province Change
        elements.province.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            elements.names.province.value = selectedOption.text;
            
            elements.regency.disabled = true;
            elements.district.disabled = true;
            elements.village.disabled = true;
            elements.district.innerHTML = '<option value="" selected disabled>Choose District</option>';
            elements.village.innerHTML = '<option value="" selected disabled>Choose Village</option>';

            loadData(`${baseUrl}/regencies/${this.value}.json`, elements.regency, 'Choose City/Regency');
        });

        // Regency Change
        elements.regency.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            elements.names.regency.value = selectedOption.text;

            elements.district.disabled = true;
            elements.village.disabled = true;
            elements.village.innerHTML = '<option value="" selected disabled>Choose Village</option>';

            loadData(`${baseUrl}/districts/${this.value}.json`, elements.district, 'Choose District');
        });

        // District Change
        elements.district.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            elements.names.district.value = selectedOption.text;

            elements.village.disabled = true;

            loadData(`${baseUrl}/villages/${this.value}.json`, elements.village, 'Choose Village');
        });

        // Village Change
        elements.village.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            elements.names.village.value = selectedOption.text;
        });
    });
</script>
@endsection
