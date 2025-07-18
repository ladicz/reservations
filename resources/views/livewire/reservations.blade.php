<div>
    <x-session-messages />
    @if ($reservations->count() > 0)
    <div class="flex justify-end w-full text-left">
        <x-button
            wire:click="toggleFuture()"
        >
            {{($onlyFuture) ? __('Všechny') : __('Pouze aktuální')}}
        </x-button>
    </div>
    @endif

    <div class="flex flex-col gap-1 w-full">
        @foreach ($reservations as $reservation)
            <div
                wire:key="{{$reservation->id}}"
                x-data="{ expanded: false }"
            >
                <div class="flex rounded-sm items-center justify-between p-2 bg-blue-800 whitespace-nowrap">
                    <p class="text-gray-200 mr-10">
                        {{__('Rezervace') . ' '. $reservation->from->format($dateFormat)}},
                        {{__('počet míst') .' '. $reservation->tables_sum_number_of_seats}}.
                    </p>
                    <span class="flex justify-end items-center">
                        <x-button
                            @click="expanded = !expanded"
                        >
                                Stoly
                        </x-button>
                        <x-delete-button
                            wire:click="delete({{$reservation->id}})"
                            wire:confirm="{{__('Opravdu smazat rezervaci?')}}"
                        >
                            Smazat
                        </x-delete-button>
                    </span>
                </div>
                <div
                    class="mb-1"
                    x-show="expanded"
                    x-collapse
                    x-cloak
                >
                    @foreach ($reservation->tables as $table)
                        <div
                            class="pl-1 mx-2 px-2 py-1 bg-blue-400"
                            wire:key="{{$table->id}}"
                        >
                            {{$table->name}} ({{__('počet míst').' '.$table->number_of_seats}})
                        </div>
                    @endforeach
                </div>
            </div>

        @endforeach
        <div class="my-3">
            {{$reservations->links()}}
        </div>
    </div>
</div>
