<x-filament::widget>
    <x-filament::card>


        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Balance Sheet Report</h1>
            <button wire:click="exportToPdf" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </button>
        </div>
        <form wire:submit.prevent="submit">
            {{ $this->form }}
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <!-- Aktiva Section -->
            <div style="border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; background-color: #f8fafc;">
                <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: #1e40af; border-bottom: 2px solid #1e40af; padding-bottom: 0.5rem;">
                    Neraca - AKTIVA
                </h2>

                @foreach ($this->getFilteredChartOfAccounts() as $category => $group)
                @if (strtolower($category) === 'aktiva')
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-weight: 600; color: #334155; margin-bottom: 0.5rem;">{{ $category }}</h3>
                    <ul style="margin-left: 1rem;">
                        @foreach ($group as $akun)
                        <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding: 0.5rem 0;">
                            <span style="flex: 2;">{{ $akun->coa_name }}</span>
                            <span style="flex: 1; text-align: right;">Rp {{ number_format($akun->current_balance, 0, ',', '.') }}</span>
                        </li>

                        @if($this->showDetails)
                        <li style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #64748b; padding-left: 1rem;">
                            <span style="flex: 2;">&nbsp;&nbsp;↳ Total Debit</span>
                            <span style="flex: 1; text-align: right;">Rp {{ number_format($akun->total_debit, 0, ',', '.') }}</span>
                        </li>
                        <li style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #64748b; padding-left: 1rem; margin-bottom: 0.5rem;">
                            <span style="flex: 2;">&nbsp;&nbsp;↳ Total Kredit</span>
                            <span style="flex: 1; text-align: right;">Rp {{ number_format($akun->total_credit, 0, ',', '.') }}</span>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    <div style="display: flex; justify-content: space-between; font-weight: 600; margin-top: 0.5rem; border-top: 2px solid #e2e8f0; padding-top: 0.5rem;">
                        <span>Total {{ $category }}</span>
                        <span>Rp {{ number_format($this->getTotalByCategory($group), 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <!-- Pasiva Section -->
            <div style="border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; background-color: #f8fafc;">
                <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: #9d174d; border-bottom: 2px solid #9d174d; padding-bottom: 0.5rem;">
                    Neraca - PASIVA
                </h2>

                @foreach ($this->getFilteredChartOfAccounts() as $category => $group)
                @if (in_array(strtolower($category), ['pasiva', 'kewajiban', 'modal']))
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-weight: 600; color: #334155; margin-bottom: 0.5rem;">{{ $category }}</h3>
                    <ul style="margin-left: 1rem;">
                        @foreach ($group as $akun)
                        <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding: 0.5rem 0;">
                            <span style="flex: 2;">{{ $akun->coa_name }}</span>
                            <span style="flex: 1; text-align: right;">Rp {{ number_format($akun->current_balance, 0, ',', '.') }}</span>
                        </li>

                        @if($this->showDetails)
                        <li style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #64748b; padding-left: 1rem;">
                            <span style="flex: 2;">&nbsp;&nbsp;↳ Total Debit</span>
                            <span style="flex: 1; text-align: right;">Rp {{ number_format($akun->total_debit, 0, ',', '.') }}</span>
                        </li>
                        <li style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #64748b; padding-left: 1rem; margin-bottom: 0.5rem;">
                            <span style="flex: 2;">&nbsp;&nbsp;↳ Total Kredit</span>
                            <span style="flex: 1; text-align: right;">Rp {{ number_format($akun->total_credit, 0, ',', '.') }}</span>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    <div style="display: flex; justify-content: space-between; font-weight: 600; margin-top: 0.5rem; border-top: 2px solid #e2e8f0; padding-top: 0.5rem;">
                        <span>Total {{ $category }}</span>
                        <span>Rp {{ number_format($this->getTotalByCategory($group), 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>



        <!-- Journal Summary -->
        <div style="margin-top: 1.5rem; border-top: 2px solid #e2e8f0; padding-top: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: #334155;">
                Total Jurnal
                @if($this->startDate || $this->endDate)
                ({{ $this->startDate ? Carbon\Carbon::parse($this->startDate)->format('d M Y') : 'Awal' }} - {{ $this->endDate ? Carbon\Carbon::parse($this->endDate)->format('d M Y') : 'Sekarang' }})
                @else
                (Semua Tanggal)
                @endif
            </h3>
            <ul style="margin-top: 0.5rem;">
                <li style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0;">
                    <span>Total Debit:</span>
                    <span style="font-weight: 600; color: #1d4ed8;">Rp {{ number_format($this->getTotalDebit(), 0, ',', '.') }}</span>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                    <span>Total Kredit:</span>
                    <span style="font-weight: 600; color: #7e22ce;">Rp {{ number_format($this->getTotalCredit(), 0, ',', '.') }}</span>
                </li>
            </ul>
        </div>
    </x-filament::card>

    {{-- Ringkasan  --}}
    <x-filament::card>
        <!-- Balance Summary -->
        <div style="margin-top: 2rem; border-top: 2px solid #e2e8f0; padding-top: 1rem; background-color: #f1f5f9; border-radius: 0.5rem; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: #334155;">Ringkasan Neraca</h3>
            <ul style="margin-top: 0.5rem;">
                <li style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0;">
                    <span>Total Aktiva:</span>
                    <span style="font-weight: 600; color: #1e40af;">Rp {{ number_format($this->getTotalAktiva(), 0, ',', '.') }}</span>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0;">
                    <span>Total Pasiva:</span>
                    <span style="font-weight: 600; color: #9d174d;">Rp {{ number_format($this->getTotalPasiva(), 0, ',', '.') }}</span>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0;">
                    <span>Status Neraca:</span>
                    <span style="font-weight: 700; {{ $this->isBalanced() ? 'color: #15803d;' : 'color: #b91c1c;' }}">
                        {{ $this->isBalanced() ? 'SEIMBANG' : 'TIDAK SEIMBANG' }}
                    </span>
                </li>
                @unless($this->isBalanced())
                <li style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                    <span>Selisih:</span>
                    <span style="font-weight: 600; color: #b45309;">
                        Rp {{ number_format($this->getBalanceDifference(), 0, ',', '.') }}
                    </span>
                </li>
                @endunless
            </ul>
        </div>

    </x-filament::card>




</x-filament::widget>
