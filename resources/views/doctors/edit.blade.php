@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Doctor</h1>
        <form action="{{ route('doctors.update', $doctor->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $doctor->name }}" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ $doctor->phone }}"
                    required>
            </div>
            <div class="mb-3">
                <label for="fee" class="form-label">Fee</label>
                <input type="text" class="form-control" id="fee" name="fee" value="{{ $doctor->fee }}"
                    required>
            </div>
            <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-select" id="department_id" name="department_id" required>
                    <option selected disabled>Select department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}"
                            {{ $department->id == $doctor->department_id ? 'selected' : '' }}>{{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
