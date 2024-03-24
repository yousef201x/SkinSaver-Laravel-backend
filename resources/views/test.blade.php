<!-- resources/views/doctors/create.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Doctor</title>
</head>
<body>
<h1>Create Doctor</h1>

@php
$doctors = \App\Models\Doctor::all()
    @endphp

@foreach($doctors as $doctor)
    <img src="{{ $doctor->doctor_image }}" alt="Doctor Image">
@endforeach

<form action="{{ route('doctors.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="phone_number">Phone Number:</label>
    <input type="text" id="phone_number" name="phone_number" required><br>

    <label for="clinic_address">Clinic Address:</label>
    <input type="text" id="clinic_address" name="clinic_address" required><br>

    <label for="schedule">Schedule:</label>
    <input type="text" id="schedule" name="schedule" required><br>

    <label for="doctor_image">Doctor Image:</label>
    <input type="file" id="doctor_image" name="doctor_image" required><br>

    <button type="submit">Create Doctor</button>
</form>
</body>
</html>
