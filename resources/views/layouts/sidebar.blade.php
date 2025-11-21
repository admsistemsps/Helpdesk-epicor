<!-- resources/views/layouts/sidebar.blade.php -->
<div class="flex items-center justify-between px-4 py-4 border-b border-gray-700">
    <h1 class="font-semibold text-xs tracking-wider overflow-hidden whitespace-nowrap"
        x-show="sidebarOpen"
        x-transition>
        E-TICKET SPS
    </h1>
    <button @click="sidebarOpen = !sidebarOpen"
        class="text-gray-400 hover:text-white transition-colors duration-200 flex-shrink-0"
        :class="!sidebarOpen && 'mx-auto'">
        <i :class="sidebarOpen ? 'fa-xmark' : 'fa-bars'" class="fa-solid text-base"></i>
    </button>
</div>

@php
$avatarPath = Auth::user()->profile_photo_path;
$avatar = $avatarPath
? asset('storage/' . $avatarPath)
: 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name);
@endphp

<div class="flex items-center px-3 py-3 border-b border-gray-700">
    <img src="{{ $avatar }}"
        class="w-8 h-8 rounded-full object-cover flex-shrink-0"
        :class="!sidebarOpen && 'mx-auto'">
    <div class="ml-2 leading-tight overflow-hidden"
        x-show="sidebarOpen"
        x-transition>
        <p class="font-medium text-gray-100 text-xs truncate">{{ Auth::user()->name }}</p>
        <p class="text-[11px] text-gray-400 truncate">{{ Auth::user()->department->code ?? 'User' }}</p>
        <span class="text-green-400 text-[10px] flex items-center mt-0.5">
            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></span> Online
        </span>
    </div>
</div>

<nav class="flex-1 overflow-y-auto px-2 pb-4">
    <p class="text-[10px] uppercase tracking-wider text-gray-500 mt-2 mb-1 overflow-hidden"
        x-show="sidebarOpen"
        x-transition>General</p>
    <div x-show="!sidebarOpen" x-transition class="border-t-2 border-gray-700 my-2 hidden md:block"></div>

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}"
        class="flex items-center px-2.5 py-2 rounded-md hover:bg-gray-800 transition group 
        {{ request()->routeIs('dashboard') 
        ? 'bg-gray-800 text-white font-semibold border-l-4 border-white' 
            : 'hover:bg-gray-800 text-gray-300' }}"
        :class="!sidebarOpen && 'justify-center'">
        <i class="fa-solid fa-gauge text-sm w-5 text-center flex-shrink-0"></i>
        <span class="ml-3 group-hover:text-white truncate overflow-hidden whitespace-nowrap"
            x-show="sidebarOpen"
            x-transition>Dashboard</span>
    </a>

    @php
    use Illuminate\Support\Facades\Auth;
    use App\Models\TicketHead;

    $user = Auth::user();

    $ticketCount = TicketHead::query()
    ->where(function ($query) use ($user) {
    $query->where('assignee_id', $user->id);
    $query->orWhere(function ($subQuery) {
    $subQuery->whereNull('assignee_id')
    ->where('status', 'Sent to Consultant');
    });
    })
    ->whereIn('status', ['Assigned', 'In Progress', 'Feedback', 'Sent to Consultant'])
    ->count();
    @endphp

    <!-- Ticket Dropdown -->
    <div x-data="{ openMenu: {{ request()->routeIs('tickets.*') ? 'true' : 'false' }} }">
        <button @click="sidebarOpen && (openMenu = !openMenu)"
            class="flex items-center w-full px-2.5 py-2 rounded-md hover:bg-gray-800 transition
            {{ request()->routeIs('tickets.*') ? 'bg-gray-800 text-white font-semibold border-l-4 border-white' 
            : 'hover:bg-gray-800 text-gray-300' }}"
            :class="!sidebarOpen && 'justify-center'">
            <i class="fa-solid fa-ticket text-sm w-5 text-center flex-shrink-0"></i>
            <span class="ml-3 truncate overflow-hidden whitespace-nowrap" x-show="sidebarOpen" x-transition>Ticket</span>
            <i class="fa-solid fa-chevron-right ml-auto text-[10px] transition-transform flex-shrink-0"
                :class="openMenu ? 'rotate-90' : ''" x-show="sidebarOpen"></i>
        </button>
        <div x-show="openMenu && sidebarOpen" x-transition class="ml-7 mt-1 space-y-0.5">
            <a href="{{ route('tickets.create') }}" class="block text-[12px] py-0.5 px-2 rounded-md
                {{ request()->routeIs('tickets.create') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">New</a>
            <a href="{{ route('tickets.index') }}" class="block text-[12px] py-0.5 px-2 rounded-md
            {{ request()->routeIs('tickets.index') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">Tracking</a>
            <a href="{{ route('tickets.approver') }}" class="block text-[12px] py-0.5 px-2 rounded-md
            {{ request()->routeIs('tickets.approver') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">Approval</a>
            <a href="{{ route('tickets.assigner') }}" class="block text-[12px] py-0.5 px-2 rounded-md
            {{ request()->routeIs('tickets.assigner') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">Assign</a>
            <a href="{{ route('tickets.worker') }}"
                class="flex items-center justify-between text-[12px] py-0.5 px-2 pr-3 rounded-md hover:text-purple-400
                {{ request()->routeIs('tickets.worker') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">
                <span>To-Do</span>
                @if ($ticketCount > 0)
                <span
                    class="inline-flex items-center justify-center w-4 h-4 text-[10px] font-semibold text-white bg-red-500 rounded-full shadow-sm">
                    {{ $ticketCount }}
                </span>
                @endif
            </a>
        </div>
    </div>

    @if($user->role_id == 1 || $user->role_id == 2)
    <p class="text-[10px] uppercase tracking-wider text-gray-500 mt-2 mb-1 overflow-hidden"
        x-show="sidebarOpen"
        x-transition>Setting</p>
    <div x-show="!sidebarOpen" x-transition class="border-t-2 border-gray-700 my-2 hidden md:block"></div>

    <!-- Master Dropdown -->
    <div x-data="{ 
        openMenu: {{ request()->routeIs([
            'departments.*', 
            'divisions.*', 
            'positions.*', 
            'roles.*', 
            'users.*', 
            'menus.*', 
            'priorities.*',
            'sites.*'
        ]) ? 'true' : 'false' }} 
    }">
        <button @click="sidebarOpen && (openMenu = !openMenu)"
            class="flex items-center w-full px-2.5 py-2 rounded-md hover:bg-gray-800 transition
            {{ request()->routeIs([
                'departments.*', 
                'divisions.*', 
                'positions.*', 
                'roles.*', 
                'users.*', 
                'menus.*', 
                'priorities.*',
                'sites.*'
            ]) 
            ? 'bg-gray-800 text-white font-semibold border-l-4 border-white' 
            : 'hover:bg-gray-800 text-gray-300' }}"
            :class="!sidebarOpen && 'justify-center'">
            <i class="fa-solid fa-cube text-sm w-5 text-center flex-shrink-0"></i>
            <span class="ml-3 truncate overflow-hidden whitespace-nowrap" x-show="sidebarOpen" x-transition>Master</span>
            <i class="fa-solid fa-chevron-right ml-auto text-[10px] transition-transform flex-shrink-0"
                :class="openMenu ? 'rotate-90' : ''" x-show="sidebarOpen"></i>
        </button>
        <div x-show="openMenu && sidebarOpen" x-transition class="ml-7 mt-1 space-y-0.5">
            <a href="{{ route('sites.index') }}"
                class="block text-[12px] py-0.5 px-2 rounded-md transition 
           {{ request()->routeIs('sites.*') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">
                Master Site
            </a>
            <a href="{{ route('departments.index') }}"
                class="block text-[12px] py-0.5 px-2 rounded-md transition 
           {{ request()->routeIs('departments.*') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">
                Master Departemen
            </a>
            <a href="{{ route('divisions.index') }}"
                class="block text-[12px] py-0.5 px-2 rounded-md transition 
           {{ request()->routeIs('divisions.*') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">
                Master Divisi
            </a>
            <a href="{{ route('positions.index') }}"
                class="block text-[12px] py-0.5 px-2 rounded-md transition 
           {{ request()->routeIs('positions.*') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">
                Master Jabatan
            </a>
            <a href="{{ route('roles.index') }}"
                class="block text-[12px] py-0.5 px-2 rounded-md transition 
           {{ request()->routeIs('roles.*') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">
                Master Role
            </a>
            <a href="{{ route('users.index') }}"
                class="block text-[12px] py-0.5 px-2 rounded-md transition 
           {{ request()->routeIs('users.*') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">
                Master User
            </a>
            <a href="{{ route('menus.index') }}"
                class="block text-[12px] py-0.5 px-2 rounded-md transition 
           {{ request()->routeIs('menus.*') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">
                Master Menu
            </a>
            <a href="{{ route('priorities.index') }}"
                class="block text-[12px] py-0.5 px-2 rounded-md transition 
           {{ request()->routeIs('priorities.*') ? 'bg-gray-800 text-white font-medium' : 'hover:text-purple-400' }}">
                Master Prioritas
            </a>
        </div>
    </div>

    <p class="text-[10px] uppercase tracking-wider text-gray-500 mt-2 mb-1 overflow-hidden"
        x-show="sidebarOpen"
        x-transition>Report</p>
    <div x-show="!sidebarOpen" x-transition class="border-t-2 border-gray-700 my-2 hidden md:block"></div>

    <a href="{{ route('reports.tickets.index') }}"
        class="flex items-center px-2.5 py-2 rounded-md hover:bg-gray-800 transition 
       {{ request()->routeIs('reports.tickets.index') 
        ? 'bg-gray-800 text-white font-semibold border-l-4 border-white' 
            : 'hover:bg-gray-800 text-gray-300' }}"
        :class="!sidebarOpen && 'justify-center'">
        <i class="fa-solid fa-chart-line text-sm w-5 text-center flex-shrink-0"></i>
        <span class="ml-3 truncate overflow-hidden whitespace-nowrap" x-show="sidebarOpen" x-transition>Laporan</span>
    </a>

    <a href="#"
        class="flex items-center px-2.5 py-2 rounded-md hover:bg-gray-800 transition"
        :class="!sidebarOpen && 'justify-center'">
        <i class="fa-solid fa-map text-sm w-5 text-center flex-shrink-0"></i>
        <span class="ml-3 truncate overflow-hidden whitespace-nowrap" x-show="sidebarOpen" x-transition>Peta</span>
    </a>
    @endif

    <p class="text-[10px] uppercase tracking-wider text-gray-500 mt-3 mb-1 overflow-hidden"
        x-show="sidebarOpen"
        x-transition>Information</p>
    <div x-show="!sidebarOpen" x-transition class="border-t-2 border-gray-700 my-2 hidden md:block"></div>

    <a href=""
        class="flex items-center px-2.5 py-2 rounded-md hover:bg-gray-800 transition 
        {{ request()->routeIs('kb.index') 
        ? 'bg-gray-800 text-white font-semibold border-l-4 border-white' 
        : 'hover:bg-gray-800 text-gray-300' }}"
        :class="!sidebarOpen && 'justify-center'">
        <i class="fa-solid fa-book text-sm w-5 text-center flex-shrink-0"></i>
        <span class="ml-3 truncate overflow-hidden whitespace-nowrap" x-show="sidebarOpen" x-transition>Knowledge Base</span>
    </a>
</nav>

<!-- FOOTER -->
<div class="border-t border-gray-700 flex justify-around py-2 mt-auto">
    <!-- Uncomment if needed -->
</div>