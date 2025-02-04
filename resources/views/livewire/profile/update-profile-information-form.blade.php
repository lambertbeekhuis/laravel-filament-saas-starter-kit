<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

/**
 * https://livewire.laravel.com/docs/uploads
 * Key for file-uploads https://www.youtube.com/watch?v=kfkKUuvF2Lc&list=PLaDrsvip-wJvbi8t1zq3mG16Wk8DTwuYT&index=4
 */
new class extends Component {

    use WithFileUploads;

    public string $name = '';
    public ?string $middle_name = '';
    public ?string $last_name = '';
    public string $email = '';
    public ?string $phone = '';
    public $new_photo; // new uploaded photo
    public ?string $profile_photo_url = ''; // existing profile photo


    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->middle_name = $user->middle_name;
        $this->last_name = $user->last_name;
        $this->phone = $user->phone;
        $this->email = $user->email;

        $this->profile_photo_url = $user->getProfilePhotoUrl('preview', true);
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'phone', 'max:20'], // https://github.com/Propaganistas/Laravel-Phone
            'new_photo' => ['nullable', 'image', 'max:3048'],
        ]);

        $user->fill($validated);

        // dd($this->new_photo);

        // add profile photo
        $request = request();

        if ($this->new_photo) {
            $user->clearMediaCollection('profile');
            $user->addMedia($this->new_photo->getRealPath())
                ->toMediaCollection('profile');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function updatedNewPhoto($photo): void
    {
        //$this->validate('new_photo', ['nullable', 'image', 'max:10']);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" enctype="multipart/form-data" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')"/>
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required
                          autofocus autocomplete="name"/>
            <x-input-error class="mt-2" :messages="$errors->get('name')"/>
        </div>

        <div>
            <x-input-label for="middle_name" :value="__('Middle name')"/>
            <x-text-input wire:model="middle_name" id="middle_name" name="middle_name" type="text"
                          class="mt-1 block w-full" autocomplete="middle_name"/>
            <x-input-error class="mt-2" :messages="$errors->get('middle_name')"/>
        </div>

        <div>
            <x-input-label for="last_name" :value="__('Family name')"/>
            <x-text-input wire:model="last_name" id="last_name" name="last_name" type="text" class="mt-1 block w-full"
                          autocomplete="last_name"/>
            <x-input-error class="mt-2" :messages="$errors->get('last_name')"/>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required
                          autocomplete="username"/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification"
                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif

        </div>

        <div>
            <x-input-label for="phone" :value="__('Mobile')"/>
            <x-text-input wire:model="phone" id="phone" name="phone" type="text" class="mt-1 block w-full"
                          autocomplete="phone"/>
            <x-input-error class="mt-2" :messages="$errors->get('phone')"/>
        </div>


        <div>
            <x-input-label for="new_photo" value="Profile photo"/> {{-- Label for info file --}}
            @if ($new_photo)
                <div class="shrink-0 my-2">
                    <img src="{{ $new_photo->temporaryUrl() }}" alt="Profile photo" class="w-36 h-50 rounded-full"/>
                </div>
            @else
                <div class="shrink-0 my-2">
                    <img src="{{ $profile_photo_url }}" alt="Profile photo" class="w-36 h-50 rounded-full"/>
                </div>
            @endif
            <label class="block mt-2">
                <span class="sr-only">Choose photo</span> {{-- Screen reader text --}}
                <input type="file" wire:model="new_photo" id="new_photo" name="new_photo" accept=".jpg,.jpeg,.png" class="block w-full text-sm text-slate-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-violet-50 file:text-violet-700
                                    hover:file:bg-violet-100
                                "/> {{-- File input field --}}
            </label>

            <x-input-error class="mt-2" :messages="$errors->get('new_photo')"/>
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
