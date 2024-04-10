<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
    <div class="container mt-4">
        <h2>Edit Doctor</h2>
        <form action="{{ route('doctors.update', $doctor->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $doctor->name }}">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $doctor->email }}">
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number"
                    value="{{ $doctor->phone_number }}">
            </div>
            <div class="form-group">
                <label for="clinic_address">Clinic Address</label>
                <input type="text" class="form-control" id="clinic_address" name="clinic_address"
                    value="{{ $doctor->clinic_address }}">
            </div>
            <div class="form-group">
                <label for="schedule">Schedule</label>
                <input type="text" class="form-control" id="schedule" name="schedule"
                    value="{{ $doctor->schedule }}">
            </div>
            <div class="form-group">
                <label for="doctor_image">Doctor Image</label>
                <input type="file" class="form-control-file" id="doctor_image" name="doctor_image">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>

</html>
