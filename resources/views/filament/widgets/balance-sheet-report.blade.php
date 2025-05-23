<x-filament::widget>
    <x-filament::card>
        <form wire:submit.prevent="submit">
            {{ $this->form }}
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <h2 class="text-xl font-bold mb-2">Neraca - AKTIVA</h2>
                @foreach ($this->getFilteredChartOfAccounts() as $category => $group)
                    @if (strtolower($category) === 'aktiva')
                        <div class="mb-4">
                            <h3 class="font-semibold">{{ $category }}</h3>
                            <ul class="ml-4">
                                @foreach ($group as $akun)
                                    <li class="flex justify-between border-b py-1">
                                        <span>{{ $akun->coa_name }}</span>
                                        <span>Rp {{ number_format($akun->current_balance, 0, ',', '.') }},-</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="flex justify-between font-bold mt-2 border-t pt-2">
                                <span>Total {{ $category }}</span>
                                <span>Rp {{ number_format($this->getTotalByCategory($group), 0, ',', '.') }},-</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div>
                <h2 class="text-xl font-bold mb-2">Neraca - PASIVA</h2>
                @foreach ($this->getFilteredChartOfAccounts() as $category => $group)
                    @if (in_array(strtolower($category), ['pasiva', 'kewajiban', 'modal']))
                        <div class="mb-4">
                            <h3 class="font-semibold">{{ $category }}</h3>
                            <ul class="ml-4">
                                @foreach ($group as $akun)
                                    <li class="flex justify-between border-b py-1">
                                        <span>{{ $akun->coa_name }}</span>
                                        <span>Rp {{ number_format($akun->current_balance, 0, ',', '.') }},-</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="flex justify-between font-bold mt-2 border-t pt-2">
                                <span>Total {{ $category }}</span>
                                <span>Rp {{ number_format($this->getTotalByCategory($group), 0, ',', '.') }},-</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="mt-8 border-t pt-4">
            <h3 class="text-lg font-semibold">Ringkasan Neraca</h3>
            <ul class="mt-2">
                <li class="flex justify-between">
                    <span>Total Aktiva:</span>
                    <span class="font-medium">Rp {{ number_format($this->getTotalAktiva(), 0, ',', '.') }},-</span>
                </li>
                <li class="flex justify-between">
                    <span>Total Pasiva:</span>
                    <span class="font-medium">Rp {{ number_format($this->getTotalPasiva(), 0, ',', '.') }},-</span>
                </li>
                <li class="flex justify-between">
                    <span>Status Neraca:</span>
                    <span class="font-bold {{ $this->isBalanced() ? 'text-green-600' : 'text-red-600' }}">
                        {{ $this->isBalanced() ? 'Seimbang' : 'Tidak Seimbang' }}
                    </span>
                </li>
                @unless($this->isBalanced())
                    <li class="flex justify-between">
                        <span>Selisih:</span>
                        <span class="font-bold text-yellow-600">
                            Rp {{ number_format($this->getBalanceDifference(), 0, ',', '.') }},-
                        </span>
                    </li>
                @endunless
            </ul>
        </div>

        <div class="mt-6 border-t pt-4">
            <h3 class="text-lg font-semibold">Total Jurnal Tahun {{ $selectedYear }}</h3>
            <ul class="mt-2">
                <li class="flex justify-between">
                    <span>Total Debit:</span>
                    <span class="font-medium text-blue-600">Rp {{ number_format($this->getTotalDebit(), 0, ',', '.') }},-</span>
                </li>
                <li class="flex justify-between">
                    <span>Total Kredit:</span>
                    <span class="font-medium text-purple-600">Rp {{ number_format($this->getTotalCredit(), 0, ',', '.') }},-</span>
                </li>
            </ul>
        </div>
    </x-filament::card>
</x-filament::widget>
