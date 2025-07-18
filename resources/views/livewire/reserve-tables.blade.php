<div>
    <div class="{{$show ? 'block' : 'hidden'}}">
        <form
            wire:submit="save"
        >
            <table class="w-full text-sm rounded-sm overflow-hidden">
                <thead class="bg-blue-800 text-gray-200">
                    <tr class>
                        <th class="p-2 w-5/12 text-left">{{__('Stůl')}}</th>
                        <th class="p-2 w-4/12 text-left">{{__('Počet míst')}}</th>
                        @auth
                            <th class="w-3/12 text-right pr-2">{{__('Rezervovat')}}</th>
                        @endauth

                    </tr>
                </thead>
                <tbody class="bg-blue-400">
                    @foreach ($tables as $table)
                        <tr class="p-2 text-black"
                            wire:key="{{$table->id}}"
                        >
                            <td class="p-2">{{$table->name}}</td>
                            <td class="p-2 text-left">{{$table->number_of_seats}}</td>
                            @auth
                                <td class="p-2 text-right">
                                    <input type="checkbox"
                                        value="{{$table->id}}"
                                        wire:model="tableIds"
                                    >
                                </td>
                            @endauth
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @auth
                <button class=" enabled:hover:bg-blue-600 enabled:bg-blue-300 disabled:bg-gray-500 py-1 px-2 rounded-sm m-1"
                    wire:dirty.attr.remove="disabled"
                    disabled
                >
                    {{__('Rezervovat')}}
                </button>
            @endauth
        </form>
        @guest
            <p class="mt-1">{{__('Pro rezervaci stolu se prosím přihlaste.')}}</p>
        @endguest
    </div>
    @if ($noTablesFound)
        <p>{{__('Na Vámi zvolený čas bohužel nemáme žádné stoly.')}}</p>
    @endif
</div>
