Beste {{$user->name}},

Je bent uitgenodigd voor het project {{$client->name}}. Klik op de onderstaande link om je account met email {{$user->email}} te activeren en toegang te krijgen tot het project.

{{--
@component('mail::button', ['url' => route('password.request', [])])
    Activeer account
@endcomponent
--}}
