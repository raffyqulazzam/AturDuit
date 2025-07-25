@extends('layouts.app')

@section('title', 'Pengaturan - AturDuit')
@section('page-title', 'Pengaturan')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900">Pengaturan</h2>
            <p class="text-sm text-gray-600">Kelola preferensi dan konfigurasi aplikasi Anda</p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Profile Settings -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Profil</h3>
                </div>
                <form action="{{ route('settings.profile') }}" method="POST" class="px-6 py-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap
                            </label>
                            <input type="text" id="name" name="name" required
                                   value="{{ old('name', Auth::user()->name) }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" id="email" name="email" required
                                   value="{{ old('email', Auth::user()->email) }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Settings -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ubah Password</h3>
                </div>
                <form action="{{ route('settings.password') }}" method="POST" class="px-6 py-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password Saat Ini
                            </label>
                            <input type="password" id="current_password" name="current_password" required
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password Baru
                                </label>
                                <input type="password" id="password" name="password" required
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Konfirmasi Password Baru
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300">
                            <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Pengaturan Notifikasi</h3>
                    <p class="text-sm text-gray-600 mt-1">Kelola notifikasi yang urgent untuk aktivitas keuangan Anda</p>
                </div>
                <form action="{{ route('settings.notifications') }}" method="POST" class="px-6 py-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <input type="checkbox" id="budget_alerts" name="budget_alerts" value="1"
                                   {{ old('budget_alerts', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                            <div class="ml-3">
                                <label for="budget_alerts" class="text-sm font-medium text-gray-700">
                                    Alert Budget Hampir Habis
                                </label>
                                <p class="text-sm text-gray-500">Notifikasi ketika budget mencapai 75% dari batas yang ditentukan</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <input type="checkbox" id="large_transaction_alerts" name="large_transaction_alerts" value="1"
                                   {{ old('large_transaction_alerts', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                            <div class="ml-3">
                                <label for="large_transaction_alerts" class="text-sm font-medium text-gray-700">
                                    Alert Transaksi Besar
                                </label>
                                <p class="text-sm text-gray-500">Notifikasi untuk transaksi di atas Rp 1.000.000</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <input type="checkbox" id="savings_goal_alerts" name="savings_goal_alerts" value="1"
                                   {{ old('savings_goal_alerts', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                            <div class="ml-3">
                                <label for="savings_goal_alerts" class="text-sm font-medium text-gray-700">
                                    Alert Target Tabungan
                                </label>
                                <p class="text-sm text-gray-500">Notifikasi ketika target tabungan hampir tercapai (75%+)</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <input type="checkbox" id="low_balance_alerts" name="low_balance_alerts" value="1"
                                   {{ old('low_balance_alerts', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                            <div class="ml-3">
                                <label for="low_balance_alerts" class="text-sm font-medium text-gray-700">
                                    Alert Saldo Rendah
                                </label>
                                <p class="text-sm text-gray-500">Notifikasi ketika saldo akun di bawah Rp 100.000</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Data Management -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Manajemen Data</h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button onclick="exportData()" 
                                class="inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                            Export Data
                        </button>
                        
                        <button onclick="importData()" 
                                class="inline-flex items-center justify-center px-4 py-2 border border-green-300 rounded-md text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                            Import Data
                        </button>
                        
                        <button onclick="resetData()" 
                                class="inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <i data-lucide="trash" class="w-4 h-4 mr-2"></i>
                            Reset Data
                        </button>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Backup data Anda secara berkala. Reset data akan menghapus semua transaksi, budget, dan target tabungan.
                    </p>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Sistem</h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-gray-600">Versi Aplikasi</p>
                            <p class="font-medium">AturDuit v1.0.0</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Total Transaksi</p>
                            <p class="font-medium">{{ number_format($systemInfo['total_transactions'], 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Total Kategori</p>
                            <p class="font-medium">{{ number_format($systemInfo['total_categories'], 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Total Akun</p>
                            <p class="font-medium">{{ number_format($systemInfo['total_accounts'], 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Bergabung Sejak</p>
                            <p class="font-medium">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status Akun</p>
                            <p class="font-medium text-green-600">Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function exportData() {
    if (confirm('Export semua data transaksi, budget, dan tabungan?')) {
        window.location.href = '{{ route("settings.export") }}';
    }
}

function importData() {
    alert('Fitur import akan segera tersedia. Gunakan format CSV standar AturDuit.');
}

function resetData() {
    if (confirm('PERINGATAN: Ini akan menghapus SEMUA data Anda termasuk transaksi, budget, dan target tabungan. Apakah Anda yakin?')) {
        if (confirm('Konfirmasi sekali lagi. Tindakan ini tidak dapat dibatalkan!')) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("settings.reset") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
}
</script>
@endsection
