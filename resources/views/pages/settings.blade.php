<x-filament::page>
    <form wire:submit.prevent="save" class="fi-form grid gap-y-6">
        {{ $this->form }}
        <div class="fi-form-actions">

            <x-filament::button type="submit" class="mt-4">
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament::page>