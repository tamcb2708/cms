@extends('layouts.dashboard')
@section('title', 'Tài khoản của tôi')

@section('content')
<div class="max-w-[1000px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-6 h-6 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            {{ __('messages.account_info') }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('messages.account_info_desc') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Left Side: Profile Information -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-white/[0.02]">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.account_details') }}</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('account.profile') }}" method="POST" class="space-y-5">
                        @csrf
                        @php $isAdminEios = $user->email === 'admin@eios.vn'; @endphp
                        
                        <!-- Readonly Info -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-800">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Username</label>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->username ?: '-' }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Role (Quyền hạn)</label>
                                <div class="text-sm font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                    {{ $user->cmsRole ? $user->cmsRole->name : 'N/A' }}
                                    @if($user->cmsRole && $user->cmsRole->is_system)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-accent-100 text-accent-700 dark:bg-accent-500/20 dark:text-accent-400">SYSTEM</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Có hiệu lực từ</label>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->valid_from ? \Carbon\Carbon::parse($user->valid_from)->format('d/m/Y H:i') : 'Ngay lập tức' }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Ngày hết hạn</label>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->valid_until ? \Carbon\Carbon::parse($user->valid_until)->format('d/m/Y H:i') : 'Không bao giờ' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.full_name') }}</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500">
                                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.email') }}</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" {{ $isAdminEios ? 'readonly' : '' }} class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500 {{ $isAdminEios ? 'opacity-60 cursor-not-allowed bg-gray-50' : '' }}">
                                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.phone') }}</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" {{ $isAdminEios ? 'readonly' : '' }} class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500 {{ $isAdminEios ? 'opacity-60 cursor-not-allowed bg-gray-50' : '' }}">
                                @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.department') }}</label>
                                <input type="text" name="department" value="{{ old('department', $user->department) }}" {{ $isAdminEios ? 'readonly' : '' }} class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500 {{ $isAdminEios ? 'opacity-60 cursor-not-allowed bg-gray-50' : '' }}">
                                @error('department') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.job_title') }}</label>
                                <input type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}" {{ $isAdminEios ? 'readonly' : '' }} class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500 {{ $isAdminEios ? 'opacity-60 cursor-not-allowed bg-gray-50' : '' }}">
                                @error('job_title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.language') }}</label>
                                @if($isAdminEios)
                                    <input type="text" readonly value="{{ $user->locale === 'vi' ? __('messages.vietnamese') : __('messages.english') }}" class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white opacity-60 cursor-not-allowed bg-gray-50">
                                @else
                                <select name="locale" class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500">
                                    <option value="en" {{ old('locale', $user->locale) === 'en' ? 'selected' : '' }}>{{ __('messages.english') }}</option>
                                    <option value="vi" {{ old('locale', $user->locale) === 'vi' ? 'selected' : '' }}>{{ __('messages.vietnamese') }}</option>
                                </select>
                                @error('locale') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                @endif
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 shadow-sm text-sm font-semibold rounded-lg text-white bg-accent-600 hover:bg-accent-700 focus:outline-none transition-colors">
                                {{ __('messages.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Change Password -->
        @if(!$isAdminEios)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-white/[0.02]">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.change_password') }}</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('account.password') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.current_password') }}</label>
                            <input type="password" name="current_password" class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500">
                            @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.new_password') }}</label>
                            <input type="password" name="password" class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500">
                            @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.confirm_password') }}</label>
                            <input type="password" name="password_confirmation" class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-accent-500 focus:border-accent-500">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-6 py-2.5 shadow-sm text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition-colors">
                                {{ __('messages.update_password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @else
        <div class="space-y-6">
            <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm p-6 flex flex-col items-center justify-center text-center h-full">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Mật khẩu được bảo vệ</h3>
                <p class="text-xs text-gray-500">Tài khoản này là tài khoản Root System nên không được phép đổi mật khẩu từ giao diện người dùng.</p>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
