@extends('layouts.home')

@section('content')

    @if(auth()->user()->role == 1)
        @if($sentences->count() == 0)
            <div class="container mx-auto p-6">
                <h2 class="text-3xl font-extrabold dark:text-white py-4">Загрузите файл корпуса и предложения отобразятся на странице</h2>
                <form id="uploadForm" action="{{ route('sentences.upload') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-gray-700 font-bold mb-2">Выберите файл</label>
                        <input type="file" name="file" class="form-control w-full px-3 py-2 border rounded" required>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Загрузить
                    </button>
                </form>
            </div>
        @else
            <div class="container mx-auto p-6 flex justify-between">
                <div class="bg-white p-6 rounded shadow-md mx-8 w-1/3 flex justify-between items-center">
                    <div>
                        В базе <strong class="text-green-900 focus:text-red-600 ...">{{$sentences->count()}}</strong> предложений!
                    </div>
                    <div class="flex flex-col items-center">
                        <form action="{{route('sentences.delete')}}" method="post">
                            @csrf
                            @method('delete')
                            <button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                                <img src="{{asset('img/icons/delete.svg')}}" alt="" style="width: 20px; height: 20px">
                            </button>
                        </form>
                        <span style="color: rgb(26 86 219)">Очистить</span>

                    </div>
                </div>

                <div class="bg-white p-6 rounded shadow-md  w-1/3 mx-8 flex justify-between items-center">
                    <div>
                        В базе <strong class="text-green-900 focus:text-red-600 ...">{{$users->count()}}</strong> учитель!
                    </div>
                    <div>
                        <a href="{{route('users.index')}}" class="flex flex-col items-center" style="cursor: pointer;">
                            <img src="{{asset('img/icons/arrowright.svg')}}" alt="" style="width: 130px; height: 40px;">
                            <span style="color: rgb(26 86 219)">показать</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white p-6 rounded shadow-md  w-1/3 mx-8 flex justify-between items-center">
                    <div>
                        Переведено: <strong class="text-green-900 focus:text-red-600 ...">{{$sentencesTranslateCompleted->count()}}</strong>
                    </div>
                    <div>
                        <a class="flex flex-col items-center" style="cursor: pointer;">
                            <img src="{{asset('img/icons/arrowright.svg')}}" alt="" style="width: 130px; height: 40px;">
                            <span style="color: rgb(26 86 219)">показать</span>
                        </a>
                    </div>
                </div>


            </div>
        @endif
    @endif



    @if(auth()->user()->role === 1)
        <div class="container mx-auto p-6 flex flex-col justify-between mx-8">
            <h3 class="mb-8 px-6">Предложения в процессе {{$sentencesTranslate->count()}}:</h3>
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
                            Цена
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Переведен
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sentencesTranslate as $item)
                        @foreach($translates as $translate) @endforeach
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{$item->id}}
                            </th>
                            <td class="px-6 py-4">
                                {{$item->sentence}}
                            </td>
                            <td class="px-6 py-4">
                                {{$translate->translation}}
                            </td>
                            <td class="px-6 py-4">
                                {{$item->locked_by}}
                            </td>
                            <td class="px-6 py-4">
                                {{$item->price}}
                            </td>
                            <td class="px-6 py-4">
                                {{$translate->created_at}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    @endif



    <!-- Modal -->
    <div id="resultModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
            <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle"></h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500" id="modalMessage"></p>
                </div>
            </div>
            <div class="bg-gray-50 p-4 flex justify-end">
                <button id="closeModalButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Close
                </button>
            </div>
        </div>
    </div>

@endsection