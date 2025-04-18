<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Player Contract</title>
    <link rel="icon" type="image/svg+xml" href="/img/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
</head>

<body>
    <header id="header" class="hide-in-iframe">
        <img src="https://www.hartlandgirlsacademy.co.uk/wp-content/uploads/2024/08/cropped-HARTLAND-LOGO-2048.png"
            alt="Hartland Logo" height="256" width="256" />
        <h1>25/26 - Player Contract</h1>
    </header>

    <div class="container">
        {!! $playerPromise !!}

        <form id="player-contract-form" method="POST" action="{{ route('player.contract.submit', $player->id) }}"
            class="forms-validate forms-form">
            @csrf

            <div class="forms-container">

                <div class="forms-fields-container">
                    <div class="forms-field forms-field-text" data-field="Player Name">
                        <label class="forms-field-label" for="name">
                            Player Name
                            <span class="forms-required-label" aria-hidden="true">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $player->name) }}"
                            aria-errormessage="name-error" class="forms-field-medium forms-field-required" readonly />
                        <em id="name-error" class="forms-error" role="alert" aria-label="Error message" for="name"
                            @readonly(true) aria-hidden="true">
                            This field is required.
                        </em>
                    </div>

                    <div class="forms-field forms-field-text" data-field="fan">
                        <label class="forms-field-label" for="fan">
                            FAN
                            <span class="forms-required-label" aria-hidden="true">*</span>
                        </label>
                        <input type="text" id="fan" name="fan" value="{{ old('fan', $player->fan) }}"
                            aria-errormessage="fan-error" class="forms-field-medium forms-field-required" readonly />
                        <em id="fan-error" class="forms-error" role="alert" aria-label="Error message" for="fan"
                            aria-hidden="true">
                            This field is required.
                        </em>
                    </div>


                </div>

                <div id="player-signature-section" class="forms-field" data-field="player-agreement">
                    <label class="forms-field-label" for="player-signature">
                        Player Signature
                        <span class="forms-required-label" aria-hidden="true">*</span>
                    </label>
                    <div id="player-signature-container">
                        <canvas id="player-signature" class="signature-pad" height="200" width="600"></canvas>
                        <div class="clear-button-container">
                            <button type="button" id="clear-signature" class="forms-clear-signature secondary small"
                                aria-label="Clear signature">
                                Clear
                            </button>
                        </div>
                    </div>

                    <div class="forms-field-description">
                        I agree to adhere and to embrace the terms outlined in the
                        player contract and to the Hartland Girls Academy Player Code of
                        Conduct.
                    </div>
                    <em id="playerAgreement-error" class="forms-error" role="alert" aria-label="Error message"
                        for="playerAgreement" aria-hidden="true">
                        You must sign the player contract.
                    </em>
                </div>
            </div>

            <input type="hidden" name="signature" id="signature">

            <div class="forms-submit-container">
                <button type="submit" name="forms[submit]" class="forms-submit" data-alt-text="Sending…"
                    data-submit-text="Submit" aria-live="assertive" value="forms-submit">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <script>
        const canvas = document.getElementById('player-signature');
        const signaturePad = new SignaturePad(canvas);
        const clearButton = document.getElementById("clear-signature");
        const form = document.querySelector('#player-contract-form');

        function submitForm() {
            if (signaturePad.isEmpty()) {
                alert('Please sign before submitting.');
                return false;
            }

            const dataURL = signaturePad.toDataURL();
            document.getElementById('signature').value = dataURL;
            return true;
        }

        // Ensure canvas size adapts to the container size
        function resizeCanvas() {
            canvas.width = canvas.offsetWidth;
            canvas.height = 200;
            signaturePad.clear(); // clear on resize
        }

        // If the user presses clear, clear the signature
        function onSignatureReset() {
            signaturePad.clear();

            // remove focus from the button
            clearButton.blur();
        }

        // Add validation to the form
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (submitForm()) {
                form.submit();
            }
        });


        // Add event handlers
        window.addEventListener("resize", resizeCanvas);
        window.addEventListener("load", resizeCanvas);
        clearButton.addEventListener("click", onSignatureReset);

        // Add some additional handling when displayed within an iframe
        if (window.self !== window.top) {
            // Page is in an iframe
            document.body.classList.add('in-iframe');

            // Tell the parent frame to resize
            function sendHeightToParent() {
                const height = document.body.scrollHeight + 50;
                window.parent.postMessage({
                    iframeHeight: height
                }, "*");
            }

            window.addEventListener("load", sendHeightToParent);
            window.addEventListener("resize", sendHeightToParent);
        }
    </script>
</body>

</html>
