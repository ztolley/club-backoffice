<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Player Signup</title>
    <link rel="icon" type="image/svg+xml" href="/img/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
</head>

<body>
    <div class="container">
        <div id="introduction">
            <p>
                Welcome to Hartland Girls Academy! We are excited to have you here. As part of the joining process, we
                need you to provide a few details about yourself. This will inform us how we can contact you and make us
                aware of any health issues or injuries your player may have, so we can ensure everyone’s safety during
                training and matches.
            </p>
            <p>
                Once the player profile is created, we can use it during your time at Hartland to record any Player
                Development Plans (PDP) and information and notes related to the player. This will help us to ensure you
                are getting the most out of your time with us.
            </p>
        </div>

        <form id="player-signup-form" method="POST" action="{{ route('player.signup.submit') }}"
            class="forms-form forms-validate ">
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
                <x-text-field label="Player FAN" name="fan" value="{{ old('fan') }}"
                    description="Please provide your FAN provided by the FA. If you are unsure of your number please login to http://wholegame.thefa.com to obtain your FAN"
                    :required="true" />
                <x-date-field label="Player Date of Birth" name="dob" value="{{ old('dob') }}"
                    :required="true" />

                <x-select-field name="team_id" :options="$teams" label="Team" :required="false"
                    placeholder="Select a team" />

                <x-text-field label="Preferred Position" name="preferred_position"
                    value="{{ old('preferred_position') }}"
                    description="Does your player have a preffered playing position at the time of starting the season?"
                    :required="false" />

                <x-text-field label="Other Positions" name="other_positions" value="{{ old('other_positions') }}"
                    description="Does your player have experience, or a desire, to play other positions? If so which?"
                    :required="false" />


                <x-text-area label="Relevant Medical Conditions" name="medical_conditions"
                    value="{{ old('medical_conditions') }}" :required="false"
                    description="Are there any medical conditions that the club should to be aware of (e.g. Asthma, ASD) ?" />

                <x-text-area label="Injuries" name="injuries" value="{{ old('injuries') }}" :required="false"
                    description="Should the club be aware of any relevant injuries either current or historic? Is the player currently receiving receiving treatment (e.g. Physiotherapy)?" />

                <x-text-area label="Additional Comments" name="additional_info" value="{{ old('additional_info') }}"
                    description="Is there anything else you feel the club should be aware of of that the club can do to help support the player?"
                    :required="false" />


                <h3 class="sub-heading">Parent/Carer Contact Details</h3>
                <p>
                    Please provide the contact details of each parent or carer associated with this player. These
                    details are used in case of emergency and for important communications from the club.
                </p>

                @for ($i = 0; $i < 3; $i++)
                    <div class="contact-section">
                        <h4>Contact {{ $i + 1 }}</h4>
                        <x-text-field id="contacts-{{ $i }}-name" label="Name"
                            name="contacts[{{ $i }}][name]" value="{{ old('contacts.' . $i . '.name') }}"
                            :required="false" />
                        <div class="split">
                            <x-text-field id="contacts-{{ $i }}-name" label="Email"
                                name="contacts[{{ $i }}][email]"
                                value="{{ old('contacts.' . $i . '.email') }}" :required="false" />
                            <x-text-field id="contacts-{{ $i }}-name" label="Phone"
                                name="contacts[{{ $i }}][phone]"
                                value="{{ old('contacts.' . $i . '.phone') }}" :required="false" />
                        </div>
                        <x-text-area id="contacts-{{ $i }}-name" label="Address"
                            name="contacts[{{ $i }}][address]"
                            value="{{ old('contacts.' . $i . '.address') }}" :required="false" />
                        <hr>
                    </div>
                @endfor


                <div id="agreements">
                    <fieldset>
                        <legend>Marketing/Photography/Social Media</legend>
                        <div class="forms-field-checkbox">
                            <ul>
                                <li>
                                    <input type="checkbox" id="allowed_marketing" name="allowed_marketing"
                                        value="1" {{ old('allowed_marketing') ? 'checked' : '' }}>
                                    <label for="allowed_marketing">
                                        As parent, legal guardian or authorised carer, I agree the player can
                                        participate visually in marketing materials, social media posts and/or events
                                        established by the club which have the purpose of promoting and advertising the
                                        football club and/or girls football.
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Parent Code of Conduct</legend>
                        <div class="forms-field-checkbox">
                            <ul>
                                <li>
                                    <input type="checkbox" id="agreed_parent_code" name="agreed_parent_code"
                                        value="1" {{ old('agreed_parent_code') ? 'checked' : '' }} required>
                                    <label for="agreed_parent_code">
                                        I agree to adhere and to embrace the terms outlined in the Hartland Girls
                                        Academy
                                        <a href="https://www.hartlandgirlsacademy.co.uk/club-welfare/player-parent-code-of-conduct/#PARENT"
                                            target="_blank">Parent Code of Conduct.</a>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </fieldset>
                </div>

            </div>

            <div id="forms-submit-container">
                <button type="submit" name="forms[submit]" id="forms-submit" data-alt-text="Sending…"
                    data-submit-text="Submit" aria-live="assertive" value="forms-submit">
                    Submit
                </button>
            </div>

            <p class="privacy-notice">
                Your data will be stored securely on a private database accessible only to authorised Hartland Girls
                Academy Officials. Your data will not be shared with any 3rd parties and used solely for the purpose of
                managing your player and recording information and notes related to activities within Hartland Girls
                Academy. If at any point you wish your data to be deleted please email a request to
                enquiries@hartlandgirlsacademy.co.uk and we will delete your data within 30 days.
            </p>
        </form>
    </div>

    <script src="{{ asset('js/player-signup.js') }}"></script>
</body>

</html>
