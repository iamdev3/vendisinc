<div class="flex items-center space-x-2">
    <x-filament::dropdown>
        <x-slot name="trigger">
            <x-filament::button
                color="gray"
                size="sm"
                :outlined="true"
                class="flex items-center space-x-1"
            >
                <span class="text-xs font-medium">
                    {{ strtoupper(app()->getLocale()) }}
                </span>
                <x-heroicon-m-chevron-down class="w-3 h-3" />
            </x-filament::button>
        </x-slot>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item
                :href="route('filament.admin.locale.switch', ['locale' => 'en'])"
                :active="app()->getLocale() === 'en'"
                class="flex items-center space-x-2"
            >
                <span class="fi fi-us w-4 h-4 rounded-sm"></span>
                <span>English</span>
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item
                :href="route('filament.admin.locale.switch', ['locale' => 'gu'])"
                :active="app()->getLocale() === 'gu'"
                class="flex items-center space-x-2"
            >
                <span class="fi fi-in w-4 h-4 rounded-sm"></span>
                <span>ગુજરાતી</span>
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
