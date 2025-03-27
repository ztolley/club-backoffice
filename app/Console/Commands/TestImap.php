<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use Webklex\IMAP\Facades\Client;

function parseApplicantFields(string $html): array
{
    $crawler = new Crawler($html);
    $fields = [];

    $crawler->filter('td.field-name')->each(function ($node, $i) use (&$fields, $crawler) {
        $fieldName = trim($node->text());
        $fieldValueNode = $crawler->filter('td.field-value')->eq($i);

        // Clean up both key and value
        $key = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $fieldName)));
        $value = trim($fieldValueNode->text());

        $fields[$key] = $value;
    });

    return $fields;
}


class TestImap extends Command
{
    protected $signature = 'imap:test';
    protected $description = 'Test IMAP connection and parse emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = Client::account('default');
        $client->connect();

        $folder = $client->getFolder('INBOX');
        $messages = $folder->messages()
            ->unseen()  // only unseen messages
            ->subject('New Player Application Form Entry') // filter by subject
            ->since(now()->subDays(7))    // only messages from last 7 days
            ->get();

        foreach ($messages as $message) {
            $subject = $message->getSubject();
            $from = $message->getFrom()[0]->mail;

            // Get the HTML body from the email and parse it into a collection of fields
            $html = $message->getHTMLBody();
            $fields = parseApplicantFields($html);


            $this->info("From: $from | Subject: $subject");
            $this->line("Fields: " . json_encode($fields, JSON_PRETTY_PRINT));


            // Get the email from the body fields and use that to see if the record has already been processed
            $email = $fields['email'];
            if (!$email) {
                $this->warn("No email found in body");
                continue;
            }

            if (\App\Models\Applicant::where('email', $email)->exists()) {
                $this->warn("Already processed: $email");
                continue;
            }

            $applicant = \App\Models\Applicant::create([
                'name' => $fields['name'],
                'address' => $fields['address_postcode'],
                'email' => $email,
                'phone' => $fields['phone'],
                'dob' => $fields['DOB'],
                'school' => $fields['school'],
                'saturday_club' => $fields['current_saturday_club'],
                'sunday_club' => $fields['current_sunday_club'],
                'previous_clubs' => $fields['previous_clubs'],
                'playing_experience' => $fields['other_playing_experience'],
                'preferred_position' => $fields['preferred_position'],
                'other_positions' => $fields['other_positions'],
                'age_groups' => $fields['applicable_age_groups'],
                'how_hear' => $fields['how_did_you_hear_about_us'],
                'medical_conditions' => $fields['relevant_medical_conditions'],
                'injuries' => $fields['injuries'],
                'additional_info' => $fields['additional_comments'],
            ]);

            dd($applicant);

            // Log that the record was processed to the console
            $this->info("Processed: $applicant");
        }

        $client->disconnect();
    }
}
