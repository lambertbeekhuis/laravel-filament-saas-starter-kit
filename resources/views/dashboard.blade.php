<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{$tenant->name}} {{ __('dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 flex flex-row w-full">

        <div class="basis-2/3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="bg-white shadow-md rounded-md overflow-hidden">
                    <div class="bg-gray-300 py-2 px-4">
                        <h2 class="text-xl font-semibold text-gray-800">Your Saas info</h2>
                    </div>
                    <div class="py-2 px-4">
                        <p class="mt-4 relaxed">This is the dashboard of tenant {{$tenant->name}} for authenticated users, with its own Tenant-users and own Tenant-data.</p>

                        <p class="mt-4 relaxed">Is has users specific to this client/tenant. And you can build your business logic from here!</p>

                        <p class="mt-4 relaxed">New users can register for this tenant through here (if you are open for public registration):</p>

                        <p class="mt-4">
                            <a href="{{route('register', ['tenant' => $tenant->slug])}}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold mx-4 py-2 px-4 rounded" wire:navigate>
                                Public registration
                            </a>
                        </p>


                        <p class="mt-12 relaxed">
                            Or you can invite them personally through the TenantAdmin.
                        </p>

                        <p class="mt-4 relaxed">
                            Both the TenantAdmin and the SuperAdmin can be access through the profile dropdown on the top right, or directly here for the example:
                        </p>

                        <p class="mt-4">
                            @can('admin', 'web')
                                <a href="{{route('filament.admin.pages.dashboard', ['tenant' => $tenant->id])}}" class="m-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" target="_blank">
                                    {{ __('To TenantAdmin') }}
                                </a>
                            @endif

                            @if (auth()->user()->isSuperAdmin())
                                <a href="{{route('filament.superadmin.pages.dashboard')}}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" target="_blank">
                                    {{ __('To SuperAdmin') }}
                                </a>
                            @endif
                        </p>

                        <p class="mt-4 relaxed">
                            For more technical information, see Github.
                        </p>

                    </div>
                </div>
            </div>
        </div>

        <div class="basis-1/3 max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="bg-white shadow-md rounded-md overflow-hidden max-w-lg mx-auto">
                    <div class="bg-gray-300 py-2 px-4">
                        <h2 class="text-xl font-semibold text-gray-800">Users</h2>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @foreach($users as $user)
                        <li class="flex items-center py-4 px-6">
                            <span class="text-gray-700 text-lg font-medium mr-4">{{$loop->iteration}}.</span>
                            @if($media = $user->getMedia('profile')->first())
                                <img class="w-12 h-12 rounded-full object-cover mr-4"
                                     src="{{$media->getUrl('preview')}}"
                                     alt="{{$user->name}}"
                                />
                            @else
                                <div class="relative inline-flex items-center justify-center w-12 h-12 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600 mr-4">
                                    <span class="font-medium text-gray-600 dark:text-gray-300">{{$user->initials}}</span>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-800">{{$user->full_name}}</h3>
                                <p class="text-gray-600 text-base">{{$user->city}}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
