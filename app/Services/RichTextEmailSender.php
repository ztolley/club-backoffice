<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class RichTextEmailSender
{
    public function sendToMany(Collection $recipients, string $subject, string $rawBody): void
    {
        foreach ($recipients as $recipient) {
            $this->sendToSingle($recipient->email, $subject, $rawBody);
        }
    }

    protected function sendToSingle(string $email, string $subject, string $rawBody): void
    {
        Mail::send([], [], function ($message) use ($email, $subject, $rawBody) {
            $emailBody = $this->processBody($rawBody, $message);

            $message->to($email)
                ->subject($subject)
                ->html($emailBody);
        });
    }

    protected function processBody(string $body, $message): string
    {
        $crawler = new Crawler($body);
        $replacements = [];

        $crawler->filter('img')->each(function ($img) use (&$replacements, $message) {
            $src = $img->attr('src');
            $srcPath = parse_url($src, PHP_URL_PATH);

            if ($srcPath && str_starts_with($srcPath, '/storage/')) {
                $filePath = public_path($srcPath);

                if (file_exists($filePath)) {
                    $cid = $message->embed($filePath);
                    $replacements[$src] = $cid;
                } else {
                    Log::warning("Image not found for email: $filePath");
                }
            }
        });

        foreach ($replacements as $original => $cid) {
            $body = str_replace("src=\"$original\"", "src=\"$cid\"", $body);
            $body = str_replace("href=\"$original\"", "href=\"$cid\"", $body);
        }

        // Clean Trix markup
        $body = preg_replace('/<figure[^>]*>(.*?)<\/figure>/is', '$1', $body);
        $body = preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/is', '', $body);
        $body = preg_replace('/<a href="cid:[^"]+">(.*?)<\/a>/is', '$1', $body);
        $body = preg_replace('/class="[^"]*"/i', '', $body);
        $body = preg_replace('/<p>\s*<\/p>/', '', $body);

        return $body;
    }
}
