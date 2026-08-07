<x-filament-panels::page>

    <div class="grid grid-cols-4 gap-4">

        <x-filament::section>

            <h2 class="text-lg font-bold">

                Total Job

            </h2>

            <div class="text-3xl">

                {{ \App\Models\SyncJob::count() }}

            </div>

        </x-filament::section>

        <x-filament::section>

            <h2 class="text-lg font-bold">

                Running

            </h2>

            <div class="text-3xl">

                {{ \App\Models\SyncJob::where('status','running')->count() }}

            </div>

        </x-filament::section>

        <x-filament::section>

            <h2 class="text-lg font-bold">

                Failed

            </h2>

            <div class="text-3xl">

                {{ \App\Models\SyncJob::where('status','failed')->count() }}

            </div>

        </x-filament::section>

        <x-filament::section>

            <h2 class="text-lg font-bold">

                Log Hari Ini

            </h2>

            <div class="text-3xl">

                {{ \App\Models\SyncLog::whereDate('created_at',today())->count() }}

            </div>

        </x-filament::section>

    </div>

</x-filament-panels::page>