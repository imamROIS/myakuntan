<x-filament-widgets::widget>
    <x-filament::card>
        {{-- Filter Form --}}
        <div class="mb-6">
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 1rem;">
                {{-- Period Type --}}
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Jenis Periode</label>
                    <select wire:model="period" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">
                        <option value="monthly">Bulanan</option>
                        <option value="yearly">Tahunan</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                {{-- Month/Year --}}
                <div>
                    @if($period === 'monthly')
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Bulan</label>
                    <select wire:model="month" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">
                        @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    @endif

                    @if($period !== 'custom')
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; margin-top: @if($period === 'monthly') 0.5rem @endif">Tahun</label>
                    <select wire:model="year" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">

                        @foreach($this->getAvailableYears() as $year => $label)
                        <option value="{{ $year }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>

                {{-- Date Range --}}
                <div>
                    @if($period === 'custom')
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Dari Tanggal</label>
                    <input type="date" wire:model="startDate" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">

                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; margin-top: 0.5rem;">Sampai Tanggal</label>
                    <input type="date" wire:model="endDate" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">
                    @endif
                </div>
            </div>
        </div>

    </x-filament::card>
    <x-filament::card style="font-family: 'Segoe UI', system-ui, sans-serif;">

        {{-- Header --}}
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e40af;">LAPORAN ARUS KAS</h1>
            <p style="color: #64748b; margin-top: 0.5rem;">Periode: {{ $this->getPeriodLabel() }}</p>
        </div>

        {{-- Beginning Balance --}}
        <div style="margin-bottom: 1.5rem; padding: 1rem; background-color: #f8fafc; border-radius: 0.5rem;">
            <div style="display: flex; justify-content: space-between;">
                <span style="font-weight: 600;">Saldo Awal Kas</span>
                <span style="font-weight: 600;">
                    Rp {{ number_format($this->getBeginningBalance(), 0, ',', '.') }}
                    @if($this->getBeginningBalance() == 0)
                    <span style="color: red; font-size: 0.8rem;">(Pastikan opening balance diisi)</span>
                    @endif
                </span>
            </div>
        </div>

        {{-- Operating Activities --}}
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: #2563eb; padding-bottom: 0.5rem; border-bottom: 2px solid #2563eb;">
                ARUS KAS DARI AKTIVITAS OPERASI
            </h2>

            <div style="margin-top: 1rem;">
                @foreach ($this->getOperatingActivities() as $activity)
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px dashed #e2e8f0;">
                    <span>{{ $activity['account_name'] }}</span>
                    <span style="font-weight: 500; color: {{ $activity['net_cash'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        Rp {{ number_format($activity['net_cash'], 0, ',', '.') }}
                    </span>
                </div>
                @endforeach

                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; font-weight: 600; border-top: 2px solid #e2e8f0; margin-top: 0.5rem;">
                    <span>Total Arus Kas Operasi</span>
                    <span style="color: #2563eb;">
                        Rp {{ number_format($this->getOperatingActivities()->sum('net_cash'), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Investing Activities --}}
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: #9333ea; padding-bottom: 0.5rem; border-bottom: 2px solid #9333ea;">
                ARUS KAS DARI AKTIVITAS INVESTASI
            </h2>

            <div style="margin-top: 1rem;">
                @foreach ($this->getInvestingActivities() as $activity)
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px dashed #e2e8f0;">
                    <span>{{ $activity['account_name'] }}</span>
                    <span style="font-weight: 500; color: {{ $activity['net_cash'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        Rp {{ number_format($activity['net_cash'], 0, ',', '.') }}
                    </span>
                </div>
                @endforeach

                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; font-weight: 600; border-top: 2px solid #e2e8f0; margin-top: 0.5rem;">
                    <span>Total Arus Kas Investasi</span>
                    <span style="color: #9333ea;">
                        Rp {{ number_format($this->getInvestingActivities()->sum('net_cash'), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Financing Activities --}}
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: #ca8a04; padding-bottom: 0.5rem; border-bottom: 2px solid #ca8a04;">
                ARUS KAS DARI AKTIVITAS PENDANAAN
            </h2>

            <div style="margin-top: 1rem;">
                @foreach ($this->getFinancingActivities() as $activity)
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px dashed #e2e8f0;">
                    <span>{{ $activity['account_name'] }}</span>
                    <span style="font-weight: 500; color: {{ $activity['net_cash'] >= 0 ? '#16a34a' : '#dc2626' }};">
                        Rp {{ number_format($activity['net_cash'], 0, ',', '.') }}
                    </span>
                </div>
                @endforeach

                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; font-weight: 600; border-top: 2px solid #e2e8f0; margin-top: 0.5rem;">
                    <span>Total Arus Kas Pendanaan</span>
                    <span style="color: #ca8a04;">
                        Rp {{ number_format($this->getFinancingActivities()->sum('net_cash'), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Net Cash Flow --}}
        <div style="margin-bottom: 1.5rem; padding: 1rem; background-color: #f8fafc; border-radius: 0.5rem;">
            <div style="display: flex; justify-content: space-between; font-weight: 600;">
                <span>Kenaikan/Penurunan Kas Bersih</span>
                <span style="color: {{ $this->getNetCashFlow() >= 0 ? '#16a34a' : '#dc2626' }};">
                    Rp {{ number_format($this->getNetCashFlow(), 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Ending Balance --}}
        <div style="padding: 1rem; background-color: #e0f2fe; border-radius: 0.5rem;">
            <div style="display: flex; justify-content: space-between; font-weight: 700;">
                <span>SALDO AKHIR KAS</span>
                <span style="color: #0369a1;">
                    Rp {{ number_format($this->getEndingBalance(), 0, ',', '.') }}
                </span>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
