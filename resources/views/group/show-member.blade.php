<!DOCTYPE html>
<html>

<head>
    <title>Laravel 9 File Upload Example - ItSolutionStuff.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>Laravel 9 File Upload Example - ItSolutionStuff.com</h2>
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Role</th>
                        </tr>
                    </thead>

                    <body>
                        @foreach ($members as $member)
                            <tr>

                                <th scope="row">{{ $member->id }}</th>
                                <td> {{ $member->name }}</td>
                                <td>{{ $member->role }}</td>
                            </tr>
                        @endforeach
                    </body>
                </table>
                <form action="{{ route('add.member.group', $group) }}" class="form" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Add member </label>
                        <select type="text" name="id" class="form-control form-select">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}</option>
                            @endforeach

                        </select>
                        <button type="submit">Add</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>

</html>
