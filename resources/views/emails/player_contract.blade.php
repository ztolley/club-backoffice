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
    <h2>Hartland Girls Academy - Player Code of Coduct Contract</h2>

    <p>Dear {{ $player->name }}</p>

    <p>As part of the joining process for Hartland Girls Academy we require all players to review and sign the Hartland
        Player Code of Coduct contract.</p>

    <p><a href="{{ $url }}" target="_blank">Click here to view and sign the contract</a></p>
</div>
