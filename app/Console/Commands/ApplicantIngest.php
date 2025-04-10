<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use Webklex\IMAP\Facades\Client;
use App\Models\Applicant;


class ApplicantIngest extends Command
{
    protected $signature = 'ingest:applicant';
    protected $description = 'Ingest applicants from mail inbox';
    protected $fieldMapping = [
        'name' => 'name',
        'address_postcode' => 'address',
        'email' => 'email',
        'phone' => 'phone',
        'dob' => 'dob',
        'school' => 'school',
        'current_saturday_club' => 'saturday_club',
        'current_sunday_club' => 'sunday_club',
        'previous_clubs' => 'previous_clubs',
        'other_playing_experience' => 'playing_experience',
        'preferred_position' => 'preferred_position',
        'other_positions' => 'other_positions',
        'applicable_age_groups_25_26_' => 'age_groups',
        'how_did_you_hear_about_us_' => 'how_hear',
        'relevant_medical_conditions' => 'medical_conditions',
        'injuries' => 'injuries',
        'additional_comments' => 'additional_info',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Connecting to the IMAP server...');
        $client = Client::account('default');

        try {
            $client->connect();
            $this->info('Connected to the IMAP server.');

            $folder = $client->getFolder('INBOX');
            $messages = $folder->messages()
                ->subject('New Player Application Form Entry')
                ->get();

            $this->info("Found " . count($messages) . " messages.");

            foreach ($messages as $message) {
                $this->processMessage($message);
            }

            $client->disconnect();
            $this->info('Disconnected from the IMAP server.');
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Process a single email message.
     *
     * @param  \Webklex\IMAP\Message  $message
     * @return void
     */
    protected function processMessage($message)
    {
        $receivedDate = $message->getDate();
        $html = $message->getHTMLBody();
        $fields = $this->parseApplicantFields($html);

        $email = $fields['email'] ?? null;
        if (!$email) {
            $this->warn("No email found in the message body.");
            return;
        }

        if (Applicant::where('email', $email)->exists()) {
            $this->warn("Applicant $email already exists.");
            return;
        }

        $data = $this->mapFieldsToApplicant($fields);
        $data['application_date'] = \Carbon\Carbon::parse($message->getDate());

        if (empty($data['name']) || empty($data['email'])) {
            $this->warn("Missing required fields for email: $email");
            return;
        }

        $applicant = Applicant::create($data);
        $this->info("Applicant created: {$applicant->email}");
    }

    /**
     * Parse the HTML body of an email into an array of fields.
     *
     * @param  string  $html
     * @return array
     */
    protected function parseApplicantFields(string $html): array
    {
        $crawler = new Crawler($html);
        $fields = [];

        $crawler->filter('td.field-name')->each(function ($node, $i) use (&$fields, $crawler) {
            $fieldName = trim($node->text());
            $fieldValueNode = $crawler->filter('td.field-value')->eq($i);

            $key = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $fieldName)));
            $value = trim($fieldValueNode->text());

            if ($key === 'dob') {
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                $value = $date ? $date->format('Y-m-d') : null;
            }

            if ($key === 'address_postcode') {
                $value = preg_replace('/\s+/', ' ', $value);
            }

            $fields[$key] = $value;
        });

        return $fields;
    }

    /**
     * Map parsed fields to the Applicant model's attributes.
     *
     * @param  array  $fields
     * @return array
     */
    protected function mapFieldsToApplicant(array $fields): array
    {
        $data = [];
        foreach ($this->fieldMapping as $fieldKey => $dbColumn) {
            $data[$dbColumn] = $fields[$fieldKey] ?? null;
        }

        return $data;
    }
}
