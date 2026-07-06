@extends('layouts.dashboard')

@section('title', __('messages.db_tracker') . ' - Config Error')

@section('content')
<div class="h-full flex flex-col bg-gray-50 dark:bg-gray-900 p-6 items-center justify-center">
    <div class="text-center p-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-200 dark:border-red-500/20 max-w-md w-full">
        <svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Chưa cấu hình hoặc bị vô hiệu hóa</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Bạn cần bật tính năng theo dõi và nhập thông tin kết nối Database trong phần <a href="{{ route('settings', ['section' => 'database']) }}" class="text-accent-500 hover:underline font-medium">Cấu hình hệ thống (Settings)</a> để sử dụng tính năng này.</p>
        
        @if($errors->any())
            <div class="mt-4 text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 p-3 rounded-lg text-left">
                <strong>Error details:</strong><br>
                {{ $errors->first() }}
            </div>
        @endif
    </div>
</div>
@endsection
