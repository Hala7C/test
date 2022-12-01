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
                            <th scope="col">Created_at</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>

                    <body>
                        @foreach ($groups as $group)
                            <tr>

                                <th scope="row">{{ $group->id }}</th>
                                {{-- <td><a href="{{ route('dashboard.categories.show', $category) }}">{{ $category->name }}</a></td> --}}
                                <td> <a href="{{ route('group.show', $group->id) }}">{{ $group->name }}</a></td>
                                {{-- <td>{{ $group->users()->name }}</td> --}}
                                <td>{{ $group->created_at }}</td>
                                <td> <a href="{{ route('group.show.member', ['id' => $group->id]) }}"><button
                                            class="btn btn-sm btn-outline-danger">Member</button></a> </td>

                                {{-- <td>
                                    <a href="{{ route('group.add', $group->id) }}"
                                        class="btn btn-sm btn-outline-success">add</a>
                                </td> --}}
                                {{--  <td>
                    <form action="{{ route('dashboard.categories.destroy', ['category' => $category->id]) }}"
                        method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td> --}}
                            </tr>
                        @endforeach
                    </body>
                </table>

            </div>
        </div>
    </div>
</body>

</html>
