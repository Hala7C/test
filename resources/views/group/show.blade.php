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
                            <th scope="col">PATH</th>
                            <th scope="col">STATUS</th>
                            <th scope="col">User_ID</th>
                            <th scope="col">Group_ID</th>
                            <th scope="col" colspan="2">Actions</th>
                        </tr>
                    </thead>

                    <body>
                        @foreach ($files as $file)
                            <tr>

                                <th scope="row">{{ $file->id }}</th>
                                {{-- <td><a href="{{ route('dashboard.categories.show', $category) }}">{{ $category->name }}</a></td> --}}
                                <td>{{ $file->name }}</td>
                                {{-- <td>{{ $file->users()->name }}</td> --}}
                                <td>{{ $file->path }}</td>
                                <td>{{ $file->status }}</td>
                                <td>{{ $file->user_id }}</td>
                                <td>{{ $file->group_id }}</td>
                                <td>
                                    <form
                                        action="{{ route('delete.file.group', ['group' => $group, 'id' => $file->id]) }}"
                                        method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </body>
                </table>
                <form action="{{ route('add.file.group', $group) }}" class="form" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Add File </label>
                        <select type="text" name="id" class="form-control form-select">
                            @foreach ($other_files as $file)
                                <option value="{{ $file->id }}">
                                    {{ $file->name }}</option>
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
