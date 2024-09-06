
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">

<x-app-layout>
    @if(auth()->user()->role == 1)
        @if($sentences->count() == 0)
            <div class="container mx-auto p-6">
                <form id="uploadForm" action="{{ route('sentences.upload') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md translater-form">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-gray-700 font-bold mb-2">Choose file:</label>
                        <input type="file" name="file" class="form-control w-full px-3 py-2 border rounded" required>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Upload
                    </button>
                </form>
            </div>
        @else
            <div class="container mx-auto p-6 flex justify-between card-container">
                <div class="bg-white p-6 rounded shadow-md mx-8 w-1/3">
                    В базе <strong class="text-green-900 focus:text-red-600 ...">{{$sentences->count()}}</strong> предложений!
                </div>

                <div class="bg-white p-6 rounded shadow-md  w-1/3 mx-8">
                    В базе <strong class="text-green-900 focus:text-red-600 ...">{{$users->count()}}</strong> учителей!
                </div>

                <div class="bg-white p-6 rounded shadow-md  w-1/3 mx-8">
                    Переведено: <strong class="text-green-900 focus:text-red-600 ...">{{$sentencesTranslate->count()}}</strong>
                </div>


            </div>
        @endif
    @endif

    @if(auth()->user()->role == 3)



            <div class="container mx-auto p-6">

                <form method="POST" action="{{ route('translate.save') }}" class="flex justify-center px-6 translater-form">
                    @csrf

                    <div class="w-1/2 mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 ">
                        <select name="sentence_id" style="opacity: 0;">
                            <option value="{{$sentence->id}}">{{$sentence->id}}</option>
                        </select>
                        <div class="px-4 py-2 bg-white rounded-t-lg dark:bg-gray-800 ">
                            <label for="comment" class="sr-only">Оригинал предложение</label>
                            <textarea id="comment" rows="4" class="w-full px-0 text-sm text-gray-900 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400" placeholder="" readonly>{{ $sentence->sentence }}</textarea>
                        </div>
                    </div>

                    <div class="w-1/2 mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 ml-6">
                        <div class="px-4 py-2 bg-white rounded-t-lg dark:bg-gray-800">
                            <label for="comment" class="sr-only">Введите предложение</label>
                            <textarea id="comment" rows="4" class="w-full px-0 text-sm text-gray-900 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400" placeholder="Write a comment..." name="translation" required ></textarea>
                        </div>
                        <select name="user_id" style="opacity: 0;">
                            <option value="{{auth()->user()->id}}">{{auth()->user()->id}}</option>
                        </select>
                        <div class="flex items-center justify-between px-3 py-2 border-t dark:border-gray-600">
                            <div class="sentence-price">
                                <p>Стоимость перевода: <span class="text-green-900 focus:text-red-600 ...">{{$sentence->price}}</span> рублей</p>
                            </div>
                            <button type="submit" class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
                                Перевод
                            </button>
                        </div>
                    </div>
                </form>
            </div>


            <div class="container mx-auto p-6 flex justify-between card-container">
                <div class="bg-white p-6 rounded shadow-md mx-8 w-1/3 flex justify-between items-center">
                    <div>
                        Переведено <strong class="text-green-900 focus:text-red-600 ...">{{$completedSentences->count()}}</strong> предложений!
                    </div>
                </div>

                <div class="bg-white p-6 rounded shadow-md  w-1/3 mx-8 flex justify-between items-center">
                    <div>
                        Отклонено <strong class="text-green-900 focus:text-red-600 ...">{{$deletedSentences->count()}}</strong>
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
                        Сумма: <strong class="text-green-900 focus:text-red-600 ..."></strong>
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

        @if(auth()->user()->role == 2)
            @if(isset($sentencesTranslate))
                <div class="container mx-auto p-6 flex flex-col justify-between card-container">
                    <h3 class="mb-8">Предложения в процессе {{$sentencesTranslate->count()}}:</h3>
                    <div class="relative overflow-x-auto">
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
                                <th scope="col" class="px-6 py-3">
                                    Действие
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sentencesTranslate as $item)
                                @foreach($translates as $translate)
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
                                    @if(isset($translate->created_at))
                                        <td class="px-6 py-4">
                                            {{$translate->created_at}}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4">
                                        <form action="{{route('sentences.approve', $item->id)}}" method="post">
                                            @csrf
                                            <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Подтвердить</button>
                                        </form>

                                        <form action="{{route('sentences.reject', $item->id)}}" method="post">
                                            @csrf
                                            <button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Отклонить</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            @endif
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
</x-app-layout>


<script>
    $(document).ready(function() {
        $('#uploadForm').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#modalTitle').text('Success');
                    $('#modalMessage').text('File uploaded and sentences saved successfully.');
                    $('#resultModal').removeClass('hidden').addClass('flex');
                },
                error: function(xhr, status, error) {
                    $('#modalTitle').text('Error');
                    $('#modalMessage').text('An error occurred: ' + xhr.responseJSON.message);
                    $('#resultModal').removeClass('hidden').addClass('flex');
                }
            });
        });

        $('#closeModalButton').on('click', function() {
            $('#resultModal').removeClass('flex').addClass('hidden');
        });
    });
</script>

</body>
</html>













