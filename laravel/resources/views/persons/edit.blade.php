<x-app-layout>
<div class="container mt-4" style="max-width: 500px;">
    <h4 class="mb-3">Edit Person</h4>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('persons.update', $person->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ old('name', $person->name) }}"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select" required>
                <option value="male" {{ $person->gender == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $person->gender == 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="text"
                   name="dob"
                   class="form-control"
                   value="{{ old('dob', date('Y',strtotime($person->dob))) }}"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text"
                   name="address"
                   class="form-control"
                   value="{{ old('address',$person->address) }}"
                   >
        </div>

        <div class="d-flex justify-content-between">
            <a href="/dashboard" class="btn btn-warning">
                Cancel
            </a>
            <button class="btn btn-success">
                Update
            </button>
        </div>
    </form>
</div>
</x-app-layout>
