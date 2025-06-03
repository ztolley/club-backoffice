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
    <h2>New Signing</h2>
    <p><strong>Name:</strong> {{ $player->name }}</p>
    <p><strong>FAN:</strong> {{ $player->fan }}</p>
    <p><strong>DOB:</strong> {{ $player->dob }}</p>
    <p><strong>Name:</strong> {{ $email ?? 'No Email' }}</p>
    <p><strong>Team:</strong> {{ $team?->name ?? 'Team not selected' }}</p>
</div>
