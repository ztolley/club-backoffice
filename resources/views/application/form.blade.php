<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Player Application</title>
    <link rel="icon" type="image/svg+xml" href="/img/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
</head>

<body>
    <div class="container">
        <p>Hartland Girls Academy are actively inviting applications for the 25/26 season. Please provide your details
            below and we will be in contact in the coming weeks with trial dates and locations.</p>
        <form method="POST" action="{{ route('application.submit') }}" class="forms-form forms-validate">
            @csrf

            <div id="forms-fields-container">
                {{-- Display validation errors --}}
                @if ($errors->any())
                    <div class="forms-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <x-text-field label="Player Name" name="name" value="{{ old('name') }}" :required="true" />
                <x-text-area label="Address" name="address" value="{{ old('address') }}" :required="true" />
                <x-email-field label="Email" name="email" value="{{ old('email') }}" :required="true" />
                <x-text-field label="Phone" name="phone" value="{{ old('phone') }}" :required="false" />
                <x-date-field label="Date of Birth" name="dob" value="{{ old('dob') }}" :required="true" />

                <x-text-field label="School" name="school" value="{{ old('school') }}" :required="false" />

                <x-text-field label="Current Saturday Club" name="saturday_club" value="{{ old('saturday_club') }}"
                    :required="false"
                    description="The club you currently play for on a Saturday or have recently played for" />
                <x-text-field label="Current Sunday Club" name="sunday_club" value="{{ old('sunday_club') }}"
                    :required="false"
                    description="The club you currently play for on a Sunday or have recently played for" />

                <x-text-area label="Previous Clubs" name="previous_clubs" value="{{ old('previous_clubs') }}"
                    :required="false"
                    description="If you are not playing at any clubs currently or recently but have previously played at." />

                <x-text-area label="Playing Experience" name="playing_experience"
                    value="{{ old('playing_experience') }}" :required="false"
                    description="e.g. School, County, District" />

                <x-text-field label="Preferred Position" name="preferred_position"
                    value="{{ old('preferred_position') }}" :required="false" />
                <x-text-field label="Other Positions" name="other_positions" value="{{ old('other_positions') }}"
                    :required="false" />


                <x-text-field label="Applicable Age Groups (25/26)" name="age_groups" value="{{ old('age_groups') }}"
                    :required="false" description="U10,11,12,13,14,15,16,18 or open age" />

                <x-text-area label="How Did You Hear About Us" name="how_hear" value="{{ old('how_hear') }}"
                    :required="false" />

                <x-text-area label="Relevant Medical Conditions" name="medical_conditions"
                    value="{{ old('medical_conditions') }}" :required="false"
                    description="Are there any medical conditions that the club needs to be aware of during the trial?" />

                <x-text-area label="Injuries" name="injuries" value="{{ old('injuries') }}" :required="false"
                    description="For the purpose of the trial, should the club be aware of any relevant injuries either current or historic?" />

                <x-text-area label="Additional Comments" name="additional_info" value="{{ old('additional_info') }}"
                    description="Is there anything else you feel the club should be aware of?" :required="false" />
            </div>

            <div id="forms-submit-container">
                <button type="submit" name="forms[submit]" class="forms-submit" data-alt-text="Sending…"
                    data-submit-text="Submit" aria-live="assertive" value="forms-submit">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <script>
        function sendHeightToParent() {
            const height = document.body.scrollHeight + 50;
            window.parent.postMessage({
                iframeHeight: height
            }, "*");
        }

        window.addEventListener("load", sendHeightToParent);
        window.addEventListener("resize", sendHeightToParent);
    </script>

</body>

</html>
