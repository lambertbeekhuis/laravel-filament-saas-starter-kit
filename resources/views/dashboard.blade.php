<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 flex flex-row w-full">
        <div class="basis-2/3">
            <div>{{auth()->tenant()}}</div>
            <div>{{auth()->tenant()}}</div>
        </div>
        <div class="basis-1/3 max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="bg-white shadow-md rounded-md overflow-hidden max-w-lg mx-auto">
                    <div class="bg-gray-300 py-2 px-4">
                        <h2 class="text-xl font-semibold text-gray-800">Users of {{$tenant->name}}</h2>
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
