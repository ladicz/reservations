<div>
    @if (session()->has('success'))
        <x-info-message>
            {{ session('success') }}
        </x-info-message>
    @endif
    @if (session()->has('fail'))
        <x-error-message>
            {{ session('fail') }}
        </x-error-message>
    @endif
</div>
