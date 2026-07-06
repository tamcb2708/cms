@extends('layouts.dashboard')

@section('title', __('messages.admin_create_title'))

@section('content')
<div class="max-w-5xl mx-auto py-6">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <div class="p-2 bg-accent-100 dark:bg-accent-500/20 text-accent-600 dark:text-accent-400 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                {{ __('messages.admin_create_title') }}
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.admin_create_desc') }}</p>
        </div>
        <a href="{{ route('admin-directory.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('messages.admin_btn_cancel') }}
        </a>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin-directory.store') }}" method="POST" class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden">
                @csrf
                
                <div class="p-6 space-y-6">
                    <!-- General Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" id="username" required value="{{ old('username') }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-accent-500 focus:border-accent-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors" placeholder="user_admin">
                            @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.admin_full_name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-accent-500 focus:border-accent-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors" placeholder="Nguyễn Văn A">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.admin_email') }} <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                </div>
                                <input type="email" name="email" id="email" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-accent-500 focus:border-accent-500 block pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors" placeholder="admin@domain.com">
                            </div>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.admin_password') }} <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="password" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-accent-500 focus:border-accent-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors" placeholder="••••••••">
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.admin_password_confirm') }} <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-accent-500 focus:border-accent-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors" placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Validity Period -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="space-y-2">
                            <label for="valid_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Có hiệu lực từ ngày</label>
                            <input type="text" name="valid_from" id="valid_from" value="{{ old('valid_from') }}" class="flatpickr-datetime w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-accent-500 focus:border-accent-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors" placeholder="Chọn ngày giờ bắt đầu...">
                            <p class="text-xs text-gray-500">Bỏ trống nếu có hiệu lực ngay lập tức.</p>
                            @error('valid_from') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="valid_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ngày hết hạn</label>
                            <input type="text" name="valid_until" id="valid_until" value="{{ old('valid_until') }}" class="flatpickr-datetime w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-accent-500 focus:border-accent-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors" placeholder="Chọn ngày giờ hết hạn...">
                            <p class="text-xs text-gray-500">Bỏ trống nếu không bao giờ hết hạn.</p>
                            @error('valid_until') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700" x-data="{ 
                            selectedRole: '{{ count($roles) === 1 ? $roles[0]->id : '' }}' 
                        }">
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gán Quyền (Role) <span class="text-red-500">*</span></label>
                                <a href="{{ route('settings') }}?section=permissions" class="text-xs text-accent-600 dark:text-accent-400 hover:underline">Quản lý Rules (Roles)</a>
                            </div>
                            
                            @if(count($roles) === 0)
                                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 rounded-lg text-sm border border-yellow-100 dark:border-yellow-800/30">
                                    Chưa có Role nào trong hệ thống. Vui lòng sang mục Quản lý Rules để tạo Role trước.
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($roles as $role)
                                    <label class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none transition-all duration-200"
                                           :class="selectedRole === '{{ $role->id }}' ? 'bg-accent-50 border-accent-500 ring-1 ring-accent-500 dark:bg-accent-900/20 dark:border-accent-500' : 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:border-gray-300'">
                                        <input type="radio" name="role" value="{{ $role->id }}" class="sr-only" x-model="selectedRole">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                                                    {{ $role->name }}
                                                    @if($role->is_system)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">SYSTEM</span>
                                                    @endif
                                                </span>
                                                <span class="mt-1 flex items-center text-xs text-gray-500">{{ $role->description ?: 'Không có mô tả.' }}</span>
                                            </span>
                                        </span>
                                        <svg class="h-5 w-5 text-accent-600 transition-opacity duration-200" :class="selectedRole === '{{ $role->id }}' ? 'opacity-100' : 'opacity-0'" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                        </svg>
                                    </label>
                                    @endforeach
                                </div>
                            @endif
                            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    
                    <!-- Status -->
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="status" value="active" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-accent-600"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                {{ __('messages.admin_status') }}: <strong class="text-green-600 ml-1">{{ __('messages.admin_status_active') }}</strong>
                            </span>
                        </label>
                    </div>

                </div>
                
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="window.history.back()" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-4 py-2 transition-colors">
                        {{ __('messages.admin_btn_cancel') }}
                    </button>
                    <button type="submit" class="bg-accent-600 hover:bg-accent-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('messages.admin_btn_save') }}
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Sidebar Info Panel -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/30 rounded-xl p-5 text-blue-800 dark:text-blue-300">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold mb-1">Quy định Bảo mật</h4>
                        <p class="text-sm leading-relaxed text-blue-700 dark:text-blue-400">
                            Hệ thống yêu cầu mật khẩu phải đạt chuẩn: tối thiểu 8 ký tự, bao gồm chữ hoa, chữ thường và số. Các tài khoản Super Admin bắt buộc phải được theo dõi (Audit Log) mọi hành động.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-white/10 p-5">
                <h4 class="font-bold text-gray-900 dark:text-white mb-4">Các cấp độ Phân Quyền</h4>
                
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="mt-1 shrink-0 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <strong class="block text-sm text-gray-900 dark:text-white">Super Admin</strong>
                            <span class="text-xs text-gray-500 leading-relaxed block mt-1">Nên giới hạn số lượng tài khoản này. Có thể can thiệp sâu vào Database, Source Code và Cấu hình Server.</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="mt-1 shrink-0 bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                        <div>
                            <strong class="block text-sm text-gray-900 dark:text-white">Support / CSKH</strong>
                            <span class="text-xs text-gray-500 leading-relaxed block mt-1">Chỉ sử dụng cho đội ngũ chăm sóc khách hàng. Được quyền tra cứu thông tin nhưng không thể lưu các thay đổi hệ thống.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('.flatpickr-datetime', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            locale: "vn"
        });
    });
</script>
@endpush
