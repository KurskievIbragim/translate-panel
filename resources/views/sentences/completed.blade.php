@extends('layouts.home')

@section('content')
    <div class="container mx-auto p-6 flex flex-col justify-between mx-8">
        <div class="flex items-center w-full justify-between">
            <div>
                <form class="flex items-center max-w-sm mx-auto p-6" action="{{ route('sentences.search') }}" method="GET">
                    @csrf
                    <label for="simple-search" class="sr-only">Search</label>
                    <div class="relative w-full">

                        <input type="text" id="simple-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Введите для поиска..." required name="search"/>
                    </div>
                    <button type="submit" class="p-2.5 ms-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                        <span class="sr-only">Найти</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="relative overflow-x-auto px-6 ">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Идентификатор
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Предложение
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Перевод
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Автор
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Переведен
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($sentencesTranslateCompleted as $item)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{$item->id}}
                        </th>
                        <td class="px-6 py-4">
                            {{$item->sentence}}
                        </td>
                        <td class="px-6 py-4">
                            @if($item->translations->isNotEmpty())
                                @foreach($item->translations as $translation)
                                    <div>
                                        <!-- Перевод предложения -->
                                        {{$translation->translation}}
                                    </div>
                                @endforeach
                            @else
                                Нет перевода
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <!-- Перебираем все переводы предложения -->
                            @if($item->translations->isNotEmpty())
                                @foreach($item->translations as $translation)
                                    <div>
                                        <!-- Автор перевода -->
                                        @if($translation->user)
                                            {{$translation->user->name}}
                                        @else
                                            (Автор неизвестен)
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                Нет перевода
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{$item->created_at}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection