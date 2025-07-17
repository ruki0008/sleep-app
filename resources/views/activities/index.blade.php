<x-app-layout>
    @if(session('message'))
        <div class="text-white font-bold bg-blue-400">
            {{ session('message') }}
        </div>
    @endif
    <section class="text-gray-600 body-font relative">
        <div class="container px-5 py-10 mx-auto">
            <div class="flex flex-col text-center w-full mb-4">
                <h2 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">ホーム</h2>
            </div>
        </div>
</x-app-layout>
