<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class RichTextEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $rawBody;

    public function __construct(string $subjectLine, string $htmlBody)
    {
        $this->subjectLine = $subjectLine;
        $this->rawBody = $htmlBody;
    }

    public function build(): static
    {
        $body = $this->rawBody;
        $crawler = new Crawler($body);
        $replacements = [];

        $crawler->filter('img')->each(function ($img) use (&$replacements) {
            $srcRaw = $img->attr('src');
            $srcPath = parse_url($srcRaw, PHP_URL_PATH);

            if ($srcPath && str_starts_with($srcPath, '/storage/')) {
                $filePath = public_path($srcPath);

                if (file_exists($filePath)) {
                    $cid = $this->embed($filePath); // ✅ embed is available here
                    $replacements[$srcRaw] = $cid;
                } else {
                    Log::warning("Email image not found: $filePath");
                }
            }
        });

        foreach ($replacements as $src => $cid) {
            $body = str_replace("src=\"$src\"", "src=\"$cid\"", $body);
            $body = str_replace("href=\"$src\"", "href=\"$cid\"", $body);
        }

        // Clean Trix
        $body = preg_replace('/<figure[^>]*>(.*?)<\/figure>/is', '$1', $body);
        $body = preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/is', '', $body);
        $body = preg_replace('/<a href="cid:[^"]+">(.*?)<\/a>/is', '$1', $body);
        $body = preg_replace('/class="[^"]*"/i', '', $body);
        $body = preg_replace('/<p>\s*<\/p>/', '', $body);

        return $this
            ->subject($this->subjectLine)
            ->html($body);
    }
}
