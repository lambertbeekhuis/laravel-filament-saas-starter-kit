<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="bg-white shadow-md rounded-md overflow-hidden max-w-lg mx-auto mt-16">
                    <div class="bg-gray-100 py-2 px-4">
                        <h2 class="text-xl font-semibold text-gray-800">Users of {{$client->name}}</h2>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @foreach($users as $user)
                        <li class="flex items-center py-4 px-6">
                            <span class="text-gray-700 text-lg font-medium mr-4">1.</span>
                            <img class="w-12 h-12 rounded-full object-cover mr-4" src="{{$user->getMedia('profile')->first()?->getUrl('preview')}}"
                                 alt="{{$user->name}}">
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
