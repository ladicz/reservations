<div class="w-80">
    <form
        wire:submit="save"
    >
        <x-label for="name">{{__('Name')}}</x-label>
        <x-input
            id="name"
            type="text"
            wire:model="name"
        />
        <x-label for="email">{{__('Email')}}</x-label>
        <x-input
            id="email"
            type="text"
            wire:model="email"
        />
        @error('email')
            <x-validation-message>{{$message}}</x-validation-message>
        @enderror
        <x-label for="password">{{__('Password')}}</x-label>
        <x-input
            id="password"
            type="password"
            wire:model="password"
        />
        @error('password')
            <x-validation-message>{{$message}}</x-validation-message>
        @enderror
        <x-label for="password_confirmation">{{__('Confirm password')}}</x-label>
        <x-input
            id="password_confirmation"
            type="password"
            wire:model="password_confirmation"
        />
        <x-button>{{__('Save')}}</x-button>
    </form>
</div>
