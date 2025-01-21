<x-public-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{$tenant->name}} {{ __('public homepage') }}
        </h2>
    </x-slot>

    <div class="py-12 flex flex-row w-full">

        <div class="basis-2/3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="bg-white shadow-md rounded-md overflow-hidden">
                    <div class="bg-gray-300 py-2 px-4">
                        <h2 class="text-xl font-semibold text-gray-800">Your public info</h2>
                    </div>
                    <div class="py-2 px-4">
                        <p class="mt-4 text-sm/relaxed">
                            This is the public homepage of tenant {{$tenant->name}}.
                        </p>

                        <p class="mt-4 text-sm/relaxed">
                            If you are open for public registration, new users can register for this tenant through here:
                            <span class="float-right">
                                <a href="{{route('register', ['tenant' => $tenant->slug])}}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" wire:navigate>
                                    Public registration
                                </a>
                            </span>
                        </p>

                        <p class="mt-4 text-sm/relaxed">
                            Or you can invite them personally through the Tenant Admin, and they will receive an email.
                        </p>

                        <p class="mt-4 text-sm/relaxed">
                            For more information on the setup, see Github.
                        </p>

                    </div>
                </div>
            </div>
        </div>

        <div class="basis-1/3 max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="bg-white shadow-md rounded-md overflow-hidden max-w-lg mx-auto">
                    <div class="bg-gray-300 py-2 px-4">
                        <h2 class="text-xl font-semibold text-gray-800">Info</h2>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-public-layout>
