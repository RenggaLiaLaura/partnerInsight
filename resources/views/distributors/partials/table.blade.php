<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-4">Name</th>
                <th scope="col" class="px-6 py-4">Region</th>
                <th scope="col" class="hidden md:table-cell px-6 py-4">Address</th>
                <th scope="col" class="hidden md:table-cell px-6 py-4">Phone</th>
                @if(Auth::user()->role === 'admin')
                <th scope="col" class="px-6 py-4 text-center">Actions</th>
                @else
                <th scope="col" class="px-6 py-4 text-center">Action</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($distributors as $distributor)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold mr-3">
                            {{ substr($distributor->name, 0, 1) }}
                        </div>
                        {{ $distributor->name }}
                    </div>
                </th>
                <td class="px-6 py-4 whitespace-nowrap">{{ $distributor->region }}</td>
                <td class="hidden md:table-cell px-6 py-4 truncate max-w-xs">{{ $distributor->address }}</td>
                <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">{{ $distributor->phone }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('distributors.show', $distributor->id) }}" class="inline-flex items-center justify-center w-7 h-7 text-teal-700 transition-colors duration-150 bg-teal-100 rounded-lg hover:bg-teal-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 dark:bg-teal-900 dark:text-teal-300 dark:hover:bg-teal-800" title="View Details">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('distributors.edit', $distributor->id) }}" class="inline-flex items-center justify-center w-7 h-7 text-amber-700 transition-colors duration-150 bg-amber-100 rounded-lg hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:bg-amber-900 dark:text-amber-300 dark:hover:bg-amber-800" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                        <form action="{{ route('distributors.destroy', $distributor->id) }}" method="POST" class="inline-block delete-form" data-confirm-title="Hapus Distributor?" data-confirm-text="Data distributor akan dihapus permanen!">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-7 h-7 text-rose-700 transition-colors duration-150 bg-rose-100 rounded-lg hover:bg-rose-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 dark:bg-rose-900 dark:text-rose-300 dark:hover:bg-rose-800" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No distributors found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<!-- Pagination -->
<div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
    {{ $distributors->links() }}
</div>
