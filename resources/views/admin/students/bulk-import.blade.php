
@extends('layouts.admin')

@section('title', 'Bulk Import Students')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bulk Import Students</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Instructions</h5>
                                <ul>
                                    <li>Download the CSV template below</li>
                                    <li>Fill in the student data following the format</li>
                                    <li>Upload the completed CSV file</li>
                                    <li>Select the department and level for all students</li>
                                    <li>Choose whether to update existing students or skip them</li>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <a href="{{ route('admin.students.download-template') }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-download"></i> Download CSV Template
                                </a>
                            </div>

                            <form method="POST" action="{{ route('admin.students.process-bulk-import') }}" 
                                  enctype="multipart/form-data">
                                @csrf
                                
                                <div class="form-group">
                                    <label for="csv_file">CSV File <span class="text-danger">*</span></label>
                                    <input type="file" name="csv_file" id="csv_file" 
                                           class="form-control-file @error('csv_file') is-invalid @enderror" 
                                           accept=".csv,.txt" required>
                                    @error('csv_file')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select name="department_id" id="department_id" 
                                            class="form-control @error('department_id') is-invalid @enderror" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }} ({{ $department->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="level">Level <span class="text-danger">*</span></label>
                                    <select name="level" id="level" 
                                            class="form-control @error('level') is-invalid @enderror" required>
                                        <option value="">Select Level</option>
                                        <option value="100" {{ old('level') == '100' ? 'selected' : '' }}>100 Level</option>
                                        <option value="200" {{ old('level') == '200' ? 'selected' : '' }}>200 Level</option>
                                        <option value="300" {{ old('level') == '300' ? 'selected' : '' }}>300 Level</option>
                                        <option value="400" {{ old('level') == '400' ? 'selected' : '' }}>400 Level</option>
                                        <option value="500" {{ old('level') == '500' ? 'selected' : '' }}>500 Level</option>
                                    </select>
                                    @error('level')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="update_existing" id="update_existing" 
                                               class="form-check-input" value="1" {{ old('update_existing') ? 'checked' : '' }}>
                                        <label for="update_existing" class="form-check-label">
                                            Update existing students (if student ID already exists)
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Import Students
                                </button>
                            </form>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>CSV Format Requirements</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Column</th>
                                                <th>Required</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>student_id</td>
                                                <td><span class="text-danger">Yes</span></td>
                                            </tr>
                                            <tr>
                                                <td>first_name</td>
                                                <td><span class="text-danger">Yes</span></td>
                                            </tr>
                                            <tr>
                                                <td>last_name</td>
                                                <td><span class="text-danger">Yes</span></td>
                                            </tr>
                                            <tr>
                                                <td>email</td>
                                                <td><span class="text-danger">Yes</span></td>
                                            </tr>
                                            <tr>
                                                <td>username</td>
                                                <td><span class="text-danger">Yes</span></td>
                                            </tr>
                                            <tr>
                                                <td>phone</td>
                                                <td><span class="text-success">No</span></td>
                                            </tr>
                                            <tr>
                                                <td>gender</td>
                                                <td><span class="text-success">No</span></td>
                                            </tr>
                                            <tr>
                                                <td>date_of_birth</td>
                                                <td><span class="text-success">No</span></td>
                                            </tr>
                                            <tr>
                                                <td>address</td>
                                                <td><span class="text-success">No</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
