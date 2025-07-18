<div class="w-80">
    <x-session-messages />
    <form
        wire:submit.prevent="login">
            <x-label for="email">{{__('Email')}}</x-label>
            <x-input
                id="email"
                type="text"
                wire:model="email"
            />
            @error('email')
                <div>
                    <x-validation-message>{{$message}}</x-validation-message>
                </div>
            @enderror
            <x-label for="password">{{__('Heslo')}}</x-label>
            <x-input
                id="password"
                type="password"
                wire:model="password"
            />
            @error('password')
                <div>
                    <x-validation-message>{{$message}}</x-validation-message>
                </div>
            @enderror
            <x-button>{{__('Přihlásit')}}</x-button>
    </form>
</div>
