<x-app-layout>
    @if(session('message'))
        <div class="text-white font-bold bg-blue-400">
            {{ session('message') }}
        </div>
    @endif
    <section class="text-gray-600 body-font relative">
        {{-- <div class="container px-5 py-10 mx-auto">
            <div class="flex flex-col text-center w-full mb-4">
                <h2 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">ホーム</h2>
            </div>
        </div> --}}
        <div class="container px-5 py-10 mx-auto">
            <div class="flex flex-col items-center text-center w-full mb-4">
                <h2 class="sm:text-3xl text-2xl font-medium title-font mb-4 bg-blue-400 text-white rounded px-4 py-2">
                    総睡眠時間: {{ $s_sum }}時間
                </h2>
                <h2 class="sm:text-3xl text-2xl font-medium title-font mb-4 bg-blue-400 text-white rounded px-4 py-2">
                    平均: {{ $s_avg }}時間
                </h2>
                <h2 class="sm:text-3xl text-2xl font-medium title-font mb-4 bg-red-300 text-white rounded px-4 py-2">
                    総運動時間: {{ $e_sum }}時間
                </h2>
                <h2 class="sm:text-3xl text-2xl font-medium title-font mb-4 bg-red-300 text-white rounded px-4 py-2">
                    平均: {{ $e_avg }}時間
                </h2>
            </div>
        </div>
    </section>
</x-app-layout>
