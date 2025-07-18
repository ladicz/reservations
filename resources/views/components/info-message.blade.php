<div x-data="{ showDiv: true }">
    <template
        x-if="showDiv"
    >
        <div class="relative bg-green-100 text-green-800 mt-1 pt-2 px-2 pb-2 rounded mb-4">
            <button class="absolute top-0 right-2"
                type="button"
                @click="showDiv = false"
            >x</button>

            <div class="pr-3 text-center">
                {{ $slot }}
            </div>
        </div>
    </template>
</div>

