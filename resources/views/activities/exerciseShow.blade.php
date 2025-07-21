<x-app-layout>
    <section class="text-gray-600 body-font relative">
        <div class="container px-5 py-10 mx-auto">
            <div class="flex flex-col text-center w-full mb-4">
                <h2 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">運動時間(削除用)</h2>
            </div>
            <div class="lg:w-1/2 md:w-2/3 mx-auto">
                <div class="flex flex-wrap -m-2">
                    <form method="post" action="{{ route('activities.store') }}" class="w-full">
                        @csrf
                        <div class="p-2 mt-3">
                            <input type="hidden" name="type" value="exercise">
                            <label for="start_time">開始時間</label>
                            @if ($errors->any())
                                <div class="mb-4 text-red-600">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                            <div class="flex gap-4">
                                <input disabled type="text" name="date" id="date_picker" class="basis-1/2 rounded" value="{{ old('date', $date) }}">
                                <input disabled type="text" name="start_time" id="start_time" value="{{ old('start_time', $activity->start_time) }}" class="basis-1/2 rounded">
                            </div>
                        </div>
                        <div class="p-2 mt-3">
                            <label for="end_time">終了時間</label>
                            <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                            <input disabled type="text" name="end_time" id="end_time" value="{{ old('end_time', $activity->end_time) }}" class="w-full rounded">
                        </div>
                        <div class="p-2 mt-3">
                            <label for="quality">運動の質</label>
                            <x-input-error :messages="$errors->get('quality')" class="mt-2" />
                            <label class="mr-3 ml-2"><input disabled type="radio" name="quality" value="1" {{ old('quality', $activity->quality) == 1 ? 'checked' : '' }}> 1</label>
                            <label class="mr-3"><input disabled type="radio" name="quality" value="2" {{ old('quality', $activity->quality) == 2 ? 'checked' : '' }}> 2</label>
                            <label class="mr-3"><input disabled type="radio" name="quality" value="3" {{ old('quality', $activity->quality) == 3 ? 'checked' : '' }}> 3</label>
                            <label class="mr-3"><input disabled type="radio" name="quality" value="4" {{ old('quality', $activity->quality) == 4 ? 'checked' : '' }}> 4</label>
                            <label><input disabled type="radio" name="quality" value="5" {{ old('quality', $activity->quality) == 5 ? 'checked' : '' }}> 5</label>
                        </div>
                        <div class="p-2 mt-3 w-full">
                            <div class="relative">
                                <label for="memo" class="leading-7 text-sm text-gray-600">メモ</label>
                                <x-input-error :messages="$errors->get('memo')" class="mt-2" />
                                <textarea disabled id="message" name="memo" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">{{ old('memo') }}</textarea>
                            </div>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('activities.destroy', $activity->id) }}">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="flex mx-auto text-white bg-red-400 border-0 py-2 px-8 focus:outline-none hover:bg-red-600 rounded text-lg"
                            onclick="return confirm('本当に削除しますか？')"
                        >
                            削除
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>
    <script>
        //flatpickrでフォームを作成するためのスクリプト
        flatpickr("#date_picker", {
            locale: "ja"
        });
        const setting = {
            locale: "ja",         //日本語化
            enableTime: true,       //タイムピッカーに設定
            noCalendar: true,       //同上
            dateFormat: "H:i",      //同上
            time_24hr: true,         //24時間
            defaultDate: null
        };

        flatpickr("#start_time", setting);
        flatpickr("#end_time", setting);    
    </script>
</x-app-layout>
