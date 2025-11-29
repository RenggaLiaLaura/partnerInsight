<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PT. ADYABOGA PRANATA INDUSTRIES') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased" style="background-image: url('/images/login-bg.png'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">
    
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
      <div class="px-3 py-3 lg:px-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center justify-start rtl:justify-end">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                   <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
             </button>
             <div class="flex md:me-24">
               <img src="/images/logo.png" class="h-8 me-3" alt="Logo" />
               <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">PT. ADYABOGA PRANATA INDUSTRIES</span>
             </div>
          </div>
          <div class="flex items-center flex-1 justify-center px-2 lg:ml-6 lg:justify-end">
              <div class="w-full max-w-lg lg:max-w-xs" x-data="{
                  query: '',
                  results: [],
                  isOpen: false,
                  search() {
                      if (this.query.length < 2) {
                          this.results = [];
                          this.isOpen = false;
                          return;
                      }
                      fetch(`/global-search?query=${this.query}`)
                          .then(response => response.json())
                          .then(data => {
                              this.results = data;
                              this.isOpen = true;
                          });
                  },
                  goTo(id) {
                      window.location.href = `/distributors/${id}`;
                  }
              }" @click.away="isOpen = false">
                  <label for="search" class="sr-only">Search</label>
                  <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                          </svg>
                      </div>
                      <input id="search" x-model="query" @input.debounce.300ms="search()" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-brand-300 focus:ring focus:ring-brand-200 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Search distributors..." type="search">
                      
                      <div x-show="isOpen && results.length > 0" class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto max-h-60 focus:outline-none sm:text-sm dark:bg-gray-700" style="display: none;">
                          <template x-for="result in results" :key="result.id">
                              <div @click="goTo(result.id)" class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 dark:hover:bg-gray-600">
                                  <div class="flex items-center">
                                      <span class="font-normal block truncate dark:text-white" x-text="result.name"></span>
                                      <span class="ml-2 text-xs text-gray-500 dark:text-gray-400" x-text="result.region"></span>
                                  </div>
                              </div>
                          </template>
                      </div>
                  </div>
              </div>
          </div>
          
          <!-- Notification Bell -->
          <div class="flex items-center" x-data="{
              open: false,
              unreadCount: 0,
              notifications: [],
              fetchNotifications() {
                  fetch('{{ route('notifications.unread') }}')
                      .then(response => response.json())
                      .then(data => {
                          this.notifications = data.notifications;
                          this.unreadCount = data.unread_count;
                      });
              },
              markAsRead(id) {
                  fetch(`/notifications/${id}/read`, {
                      method: 'POST',
                      headers: {
                          'X-CSRF-TOKEN': '{{ csrf_token() }}',
                          'Content-Type': 'application/json'
                      }
                  }).then(() => {
                      this.fetchNotifications();
                  });
              },
              markAllRead() {
                  fetch('{{ route('notifications.readAll') }}', {
                      method: 'POST',
                      headers: {
                          'X-CSRF-TOKEN': '{{ csrf_token() }}',
                          'Content-Type': 'application/json'
                      }
                  }).then(() => {
                      this.fetchNotifications();
                  });
              }
          }" x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 30000)">
              <div class="relative ml-3">
                  <button @click="open = !open" @click.away="open = false" class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:text-gray-300 dark:hover:text-white">
                      <span class="sr-only">View notifications</span>
                      <!-- Bell Icon -->
                      <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                      </svg>
                      <!-- Badge -->
                      <span x-show="unreadCount > 0" class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
                  </button>

                  <!-- Dropdown -->
                  <div x-show="open" 
                       x-transition:enter="transition ease-out duration-100"
                       x-transition:enter-start="transform opacity-0 scale-95"
                       x-transition:enter-end="transform opacity-100 scale-100"
                       x-transition:leave="transition ease-in duration-75"
                       x-transition:leave-start="transform opacity-100 scale-100"
                       x-transition:leave-end="transform opacity-0 scale-95"
                       class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 dark:bg-gray-700 dark:ring-gray-600"
                       style="display: none;">
                      <div class="py-1">
                          <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-600 flex justify-between items-center">
                              <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                              <button @click="markAllRead" class="text-xs text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">Mark all read</button>
                          </div>
                          
                          <div class="max-h-60 overflow-y-auto">
                              <template x-for="notification in notifications" :key="notification.id">
                                  <div @click="markAsRead(notification.id)" class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-0">
                                      <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.title"></p>
                                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="notification.message"></p>
                                      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="new Date(notification.created_at).toLocaleString()"></p>
                                  </div>
                              </template>
                              <div x-show="notifications.length === 0" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                  No new notifications
                              </div>
                          </div>
                          
                          <div class="border-t border-gray-100 dark:border-gray-600">
                              <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-center text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300 font-medium">
                                  View all notifications
                              </a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <div class="flex items-center">
              <div class="flex items-center ms-3 mr-4">
                <div>
                  <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                    <span class="sr-only">Open user menu</span>
                    <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                  </button>
                </div>
                <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                  <div class="px-4 py-3" role="none">
                    <p class="text-sm text-gray-900 dark:text-white" role="none">
                      {{ Auth::user()->name ?? 'User' }}
                    </p>
                    <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                      {{ Auth::user()->email ?? 'email@example.com' }}
                    </p>
                  </div>
                  <ul class="py-1" role="none">
                    <li>
                      <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="none">Settings</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="none">Sign out</a>
                        </form>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
        </div>
      </div>
    </nav>
    
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
       <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
           <ul class="space-y-3 font-medium">
             <li>
                <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded-lg group {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-600 dark:bg-gray-700 dark:text-white' : 'text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700' }}">
                   <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('dashboard') ? 'text-brand-600 dark:text-white' : 'text-gray-400 group-hover:text-brand-600 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                      <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                      <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                   </svg>
                   <span class="ms-3">Dashboard</span>
                </a>
             </li>
             
             <!-- Kelola Data Distributor -->
             <li>
                <a href="{{ route('distributors.index') }}" class="flex items-center p-2 rounded-lg group {{ request()->routeIs('distributors.*') ? 'bg-brand-50 text-brand-600 dark:bg-gray-700 dark:text-white' : 'text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700' }}">
                   <svg class="flex-shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('distributors.*') ? 'text-brand-600 dark:text-white' : 'text-gray-400 group-hover:text-brand-600 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                      <path d="m17.418 3.623-.018-.008a6.713 6.713 0 0 0-2.4-.569V2h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2H9.89A6.977 6.977 0 0 1 12 8v5h-2V8A5 5 0 1 0 0 8v6a1 1 0 0 0 1 1h8v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-4h6a1 1 0 0 0 1-1V8a5 5 0 0 0-2.582-4.377ZM6 12H4a1 1 0 0 1 0-2h2a1 1 0 0 1 0 2Z"/>
                   </svg>
                   <span class="flex-1 ms-3 whitespace-nowrap">Data Distributor</span>
                </a>
             </li>

             <!-- Kelola Kepuasan Distributor -->
             @if(Auth::user()->role === 'admin')
             <li>
                <a href="{{ route('satisfaction.index') }}" class="flex items-center p-2 rounded-lg group {{ request()->routeIs('satisfaction.*') ? 'bg-brand-50 text-brand-600 dark:bg-gray-700 dark:text-white' : 'text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700' }}">
                   <svg class="flex-shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('satisfaction.*') ? 'text-brand-600 dark:text-white' : 'text-gray-400 group-hover:text-brand-600 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                   </svg>
                   <span class="flex-1 ms-3 whitespace-nowrap">Kepuasan Distributor</span>
                </a>
             </li>
             @endif

             <!-- Kelola Kinerja Penjualan -->
             @if(Auth::user()->role === 'admin')
             <li>
                <a href="{{ route('sales.index') }}" class="flex items-center p-2 rounded-lg group {{ request()->routeIs('sales.*') ? 'bg-brand-50 text-brand-600 dark:bg-gray-700 dark:text-white' : 'text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700' }}">
                   <svg class="flex-shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('sales.*') ? 'text-brand-600 dark:text-white' : 'text-gray-400 group-hover:text-brand-600 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                      <path d="M17 5.923A1 1 0 0 0 16 5h-3V4a4 4 0 1 0-8 0v1H2a1 1 0 0 0-1 .923L.166 16A1.984 1.984 0 0 0 1.992 18h14.016a1.984 1.984 0 0 0 1.826-2L17 5.923ZM7 9a1 1 0 0 1-2 0V7h2v2Zm0-5a2 2 0 1 1 4 0v1H7V4Zm6 5a1 1 0 1 1-2 0V7h2v2Z"/>
                   </svg>
                   <span class="flex-1 ms-3 whitespace-nowrap">Kinerja Penjualan</span>
                </a>
             </li>
             @endif

             <!-- Audit Logs (Admin only) -->
             @if(Auth::user()->role === 'admin')
             <li>
                <a href="{{ route('audit-logs.index') }}" class="flex items-center p-2 rounded-lg group {{ request()->routeIs('audit-logs.*') ? 'bg-brand-50 text-brand-600 dark:bg-gray-700 dark:text-white' : 'text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700' }}">
                   <svg class="flex-shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('audit-logs.*') ? 'text-brand-600 dark:text-white' : 'text-gray-400 group-hover:text-brand-600 dark:group-hover:text-white' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                   <span class="flex-1 ms-3 whitespace-nowrap">Audit Logs</span>
                </a>
             </li>
             @endif

             <!-- Clustering Group -->
             <li>
                    <a href="{{ route('clustering.index') }}" class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('clustering.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <svg class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ request()->routeIs('clustering.*') ? 'text-brand-600 dark:text-brand-500' : '' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span class="flex-1 ml-3 whitespace-nowrap">Clustering Analysis</span>
                    </a>
                </li>
          </ul>
       </div>
    </aside>
    
    <div class="p-4 sm:ml-64">
       <div class="p-4 rounded-lg mt-14">
          @yield('content')
       </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#1C64F2',
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#E02424',
                });
            @endif
        });
    </script>
</body>
</html>
