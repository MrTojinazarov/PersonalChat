<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foydalanuvchilar Ro'yxati va Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @vite('resources/js/app.js')
    <style>
        .chat-box {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background: #f9f9f9;
        }

        .message {
            margin-bottom: 10px;
        }

        .message.sent {
            text-align: right;
            color: #fff;
            background-color: #007bff;
            padding: 10px;
            border-radius: 10px;
            display: block;
            max-width: 75%;
            margin: 5px 0;
        }

        .message.received {
            text-align: left;
            color: #000;
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 10px;
            display: block;
            max-width: 75%;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">Laravel Chat</a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        <div class="row">
            <div class="col-4 mt-4">
                <h5>Foydalanuvchilar ro'yxati</h5>
                <ul class="list-group">
                    @foreach ($users as $user)
                        <li class="list-group-item">
                            <a href="{{ route('chat', $user->id) }}">
                                {{ $user->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-8 mt-4">
                @if (isset($messages) && isset($receiver))
                    <h5>Chat: {{ $receiver->name }}</h5>
                    <div class="chat-box" id="messageList">
                        @foreach ($messages as $message)
                            <div>
                                @if ($message->file)
                                    @php
                                        $fileExtension = strtolower(pathinfo($message->file, PATHINFO_EXTENSION));
                                        $filePath = $message->file;
                                    @endphp

                                    @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg']))
                                        <img src="{{ asset($message->file) }}" alt="File Image" class="ms-3"
                                            style="max-width: 150px; height: auto;">
                                    @elseif (in_array($fileExtension, ['mp4', 'mov', 'avi', 'mkv']))
                                        <video controls style="max-width: 150px;" class="ms-3">
                                            <source src="{{ asset($filePath) }}" type="video/{{ $fileExtension }}">
                                        </video>
                                    @elseif (in_array($fileExtension, ['pdf', 'doc', 'docx', 'xls', 'xlsx']))
                                        <a href="{{ asset($filePath) }}" target="_blank">Download File</a>
                                    @endif
                                @endif
                                @if ($message->message)
                                    <div class="message received">
                                        <p>{{ $message->message }}</p>
                                    </div>
                                @endif
                                <p class="small ms-3 mb-3 rounded-3 text-muted">
                                    {{ $message->created_at->format('H:i') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    <form action="{{ route('sendMessage', $chat->id) }}" method="POST" class="mt-3"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <textarea name="message" class="form-control" rows="2" placeholder="Habar yozing..."></textarea>
                            <input type="file" name="file" class="form-control mt-2">
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Yuborish</button>
                    </form>
                @else
                    <h5>Chatni tanlang</h5>
                @endif
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
