<style>
    h2 {
        font-size: 24px;
        margin-bottom: 20px;
    }

    p {
        font-size: 16px;
        border-bottom: 1px solid #ccc;
        padding: 15px 0;
        margin: 15px 0;
    }

    strong {
        display: block;
        padding: 10px 0;
    }

    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container">
    <h2>New Player Application</h2>

    <p><strong>Name:</strong> {{ $applicant->name }}</p>
    <p><strong>Email:</strong> {{ $applicant->email }}</p>
    <p><strong>Phone:</strong> {{ $applicant->phone }}</p>
    <p><strong>Address:</strong>{{ $applicant->address }}</p>
    <p><strong>DOB:</strong> {{ $applicant->dob }}</p>
    <p><strong>School:</strong> {{ $applicant->school }}</p>
    <p><strong>Current Saturday Club:</strong> {{ $applicant->saturday_club }}</p>
    <p><strong>Current Sunday Club:</strong> {{ $applicant->sunday_club }}</p>
    <p><strong>Previous Clubs:</strong> {{ $applicant->previous_clubs }}</p>
    <p><strong>Playing Experience:</strong> {{ $applicant->playing_experience }}</p>
    <p><strong>Preferred Position:</strong> {{ $applicant->preferred_position }}</p>
    <p><strong>Other Positions:</strong> {{ $applicant->other_positions }}</p>
    <p><strong>Applicable Age Groups:</strong> {{ $applicant->age_groups }}</p>
    <p><strong>How Did You Hear About Us:</strong> {{ $applicant->how_hear }}</p>
    <p><strong>Relevant Medical Conditions:</strong> {{ $applicant->medical_conditions }}</p>
    <p><strong>Injuries:</strong> {{ $applicant->injuries }}</p>
    <p><strong>Additional Comments:</strong> {{ $applicant->additional_info }}</p>
</div>
