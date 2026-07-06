@extends('layouts.dashboard')

@section('title', $title)

@section('content')
    <div class="flex h-full min-h-[240px] flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-gray-200 text-center dark:border-white/10">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $title }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500">This page is coming soon.</p>
    </div>
@endsection
