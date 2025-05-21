{{-- resources/views/filament/widgets/balance-sheet-report.blade.php --}}
<x-filament::widget>
    <x-filament::card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Aktiva --}}
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-bold mb-4 text-primary-600 border-b pb-2">AKTIVA</h2>
                <ul class="space-y-2">
                    @foreach($this->getAktiva() as $item)
                        <li class="flex justify-between py-1 px-2 hover:bg-gray-50">
                            <span>{{ $item->coa_name }}</span>
                            <span class="font-mono">Rp {{ number_format($item->current_balance, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="font-bold mt-4 pt-2 border-t flex justify-between text-primary-600">
                    <span>TOTAL AKTIVA</span>
                    <span>Rp {{ number_format($this->getTotalAktiva(), 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Pasiva --}}
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-bold mb-4 text-danger-600 border-b pb-2">PASIVA</h2>
                <ul class="space-y-2">
                    @foreach($this->getPasiva() as $item)
                        <li class="flex justify-between py-1 px-2 hover:bg-gray-50">
                            <span>{{ $item->coa_name }}</span>
                            <span class="font-mono">Rp {{ number_format($item->current_balance, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="font-bold mt-4 pt-2 border-t flex justify-between text-danger-600">
                    <span>TOTAL PASIVA</span>
                    <span>Rp {{ number_format($this->getTotalPasiva(), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>