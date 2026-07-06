<div x-data="toastManager()" 
     @notify.window="addToast($event.detail)"
     class="fixed top-6 z-[100] flex flex-col items-center gap-2 pointer-events-none w-full max-w-md px-4"
     style="left: 50%; transform: translateX(-50%);">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
             class="pointer-events-auto w-full overflow-hidden rounded-xl shadow-2xl ring-1 ring-black ring-opacity-5 flex items-center p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 backdrop-blur-sm"
             :class="{
                'shadow-green-500/20': toast.type === 'success',
                'shadow-red-500/20': toast.type === 'error',
                'shadow-blue-500/20': toast.type === 'info',
             }"
             >
            <div class="flex-shrink-0">
                <!-- Success Icon -->
                <svg x-show="toast.type === 'success'" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <!-- Error Icon -->
                <svg x-show="toast.type === 'error'" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <!-- Info Icon -->
                <svg x-show="toast.type === 'info'" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="toast.title"></p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="toast.message" x-show="toast.message"></p>
            </div>
            <div class="ml-4 flex flex-shrink-0">
                <button @click="removeToast(toast.id)" type="button" class="inline-flex rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('toastManager', () => ({
            toasts: [],
            addToast(toast) {
                const id = Date.now() + Math.random().toString(36).substring(2, 9);
                const newToast = {
                    id: id,
                    type: toast.type || 'info',
                    title: toast.title || '',
                    message: toast.message || '',
                    show: true
                };
                
                this.toasts.push(newToast);
                
                setTimeout(() => {
                    this.removeToast(id);
                }, toast.duration || 5000);
            },
            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.show = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300);
                }
            }
        }));
    });

    window.showToast = function(title, message = '', type = 'success', duration = 5000) {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { title, message, type, duration }
        }));
    }
</script>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.showToast("Thành công", "{{ addslashes(session('success')) }}", 'success'); }, 100);
        });
    </script>
@endif
@if(session('success_profile'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.showToast("Thành công", "{{ addslashes(session('success_profile')) }}", 'success'); }, 100);
        });
    </script>
@endif
@if(session('success_password'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.showToast("Thành công", "{{ addslashes(session('success_password')) }}", 'success'); }, 100);
        });
    </script>
@endif

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.showToast("Lỗi", "{{ addslashes($errors->first()) }}", 'error', 8000); }, 100);
        });
    </script>
@endif
