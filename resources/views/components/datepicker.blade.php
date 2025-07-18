
    <div x-data="datepickerComponent('{{ $datePickerFormat }}', '{{ $datePickerMinDate }}')"
        x-init="init()"
        class="w-full"
    >
        <input type="text"
            id="my-picker"
            x-ref="picker"
            class="datepicker bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="{{__('Vyberte datum')}}"
            wire:model="{{$modelName}}"
            @change-date.camel="@this.set('{{$modelName}}', $event.target.value)"
        />
    </div>

