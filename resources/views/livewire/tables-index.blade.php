<div class="w-full">
    <x-session-messages />
    @if (!$startTime)
        <h2 class="m-2 text-center text-2xl">{{__('Choose a reservation date, start time, and duration')}}</h2>
    @endif
    <div class="mx-auto w-full max-w-56">
        <form class="grid w-full grid-cols-1 gap-3 p-2">
            <div>
                <label class="mb-1 block text-sm font-medium" for="my-picker">{{__('Date')}}</label>
                <x-datepicker modelName="startDate" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium" for="reservation-start-time">{{__('Start time')}}</label>
                <select
                    id="reservation-start-time"
                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900"
                    wire:model.live="startTime"
                >
                    @foreach ($times as $time)
                        <option value="{{$time}}">{{$time ?: __('Choose time')}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium" for="reservation-duration">{{__('Duration')}}</label>
                <select
                    id="reservation-duration"
                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900"
                    wire:model.live="durationInMinutes"
                >
                    @foreach ($durations as $duration)
                        <option value="{{$duration}}">{{$duration}} {{__('minutes')}}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <livewire:reserve-tables :$tables :$startDate :$startTime :$durationInMinutes/>
    </div>
</div>
