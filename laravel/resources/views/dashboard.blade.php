<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <style>
        /* Table wrapper */
        .table-responsive {
            margin-top: 20px;
        }

        /* Table base */
        .table {
            border-collapse: collapse;
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* Header */
        .table thead th {
            background-color: #f8f9fa;
            font-weight: 700;
            font-size: 14px;
            color: #333;
            padding: 12px;
            text-align: left;
        }

        /* Body cells */
        .table tbody td {
            padding: 12px;
            font-size: 14px;
            color: #444;
            vertical-align: middle;
        }

        /* Row hover */
        .table tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Borders */
        .table-bordered > :not(caption) > * {
            border-width: 1px;
            border-color: #e5e7eb;
        }

        /* Action button */
        .table .btn {
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 6px;
        }

        /* Pagination spacing */
        .pagination {
            margin-top: 20px;
            justify-content: center;
        }

        /* Zebra stripe fix */
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #fafafa;
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="padding: 50px;">
                @if($personCount)
                <form method="GET" action="{{ url()->current() }}" class="mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search by name..."
                                value="{{ request('search') }}"
                            >
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">Search</button>
                            <a href="{{ url()->current() }}" class="btn btn-light">Reset</a>
                        </div>
                    </div>
                </form>
                @if ($persons->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Birth Year</th>
                                <th>Address</th>
                                <th>Tree</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($persons as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ ucfirst($item->gender) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->dob)->format('Y') }}</td>
                                    <td>{{ $item->address ?? '-' }}</td>
                                    <td>
                                        <a href="{{ url('/family_tree?root_id=' . $item->id) }}"
                                        class="btn btn-sm btn-secondary">
                                            Update Tree
                                        </a>

                                        <a href="/persons/{{ $item->id }}/tree-view"
                                        class="btn btn-sm btn-secondary">
                                            View Tree
                                        </a>

                                        <a href="{{ route('persons.tree.pdf', $item->id) }}"
                                            class="btn btn-sm btn-dark">
                                            Tree PDF
                                        </a>

                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('persons.edit', $item->id) }}"
                                        class="btn btn-sm btn-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('persons.destroy', $item->id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this person?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $persons->links() }}
                @else
                <p class="text-muted">No persons found.</p>
                @endif
                @else
                <div id="familyForm" class="bg-white text-gray-800 p-4 rounded-lg shadow">

                    <div class="mb-3">
                        <label class="form-label text-gray-700">Name</label>
                        <input
                            type="text"
                            id="name"
                            class="form-control bg-white text-gray-800 border border-gray-300"
                            placeholder="Enter name" value="Karthickraja"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-gray-700">Date of Birth</label>
                        <input
                            type="date"
                            id="dob"
                            class="form-control bg-white text-gray-800 border border-gray-300"
                            value="1996-03-23"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-gray-700">Gender</label>
                        <select
                            id="gender"
                            class="form-select bg-white text-gray-800 border border-gray-300"
                        >
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <button
                        type="button"
                        id="submitBtn"
                        class="btn btn-primary w-100"
                    >
                        Create Root User
                    </button>

                </div>
                @endif
            </div>
            
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#submitBtn').on('click', function () {

            let payload = {
                name: $('#name').val(),
                dob: $('#dob').val(),
                gender: $('#gender').val(),
            };

            $.ajax({
                url: '/root/add_root',   // change later
                method: 'POST',
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log('Success:', response);
                    alert('Submitted successfully');
                    location.reload();
                },
                error: function (xhr) {
                    console.log('Error:', xhr.responseJSON);
                    alert('Error occurred');
                }
            });

            setTimeout(() => {
                document.querySelector('.alert')?.remove();
            }, 3000);
        });
    </script>
</x-app-layout>
