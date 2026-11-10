<?php

namespace App\Services;

use App\Models\FoundItem;
use App\Models\LostItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiMatchingService
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-3.5-flash-lite');
    }

    /**
     * Compute AI semantic similarity between a lost item and a found item.
     *
     * @param LostItem $lostItem
     * @param FoundItem $foundItem
     * @return array{score: int, confidence: string, reasons: array<string>, ai_analyzed: bool}
     */
    public function match(LostItem $lostItem, FoundItem $foundItem): array
    {
        if (empty($this->apiKey)) {
            return $this->fallbackHeuristic($lostItem, $foundItem, 'API Key Gemini belum dikonfigurasi.');
        }

        try {
            $prompt = $this->buildPrompt($lostItem, $foundItem);

            $response = Http::timeout(10)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.2,
                    ]
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($rawText) {
                    $parsed = json_decode($rawText, true);
                    if (is_array($parsed) && isset($parsed['score'])) {
                        $score = max(0, min(99, (int) $parsed['score']));
                        $confidence = match (true) {
                            $score >= 85 => 'SANGAT_TINGGI',
                            $score >= 70 => 'TINGGI',
                            $score >= 50 => 'SEDANG',
                            default => 'RENDAH',
                        };

                        $reasons = is_array($parsed['reasons'] ?? null) && !empty($parsed['reasons'])
                            ? array_values(array_filter($parsed['reasons'], 'is_string'))
                            : ['Analisis kesamaan semantik oleh Google Gemini AI.'];

                        return [
                            'score' => $score,
                            'confidence' => $confidence,
                            'reasons' => $reasons,
                            'ai_analyzed' => true,
                        ];
                    }
                }
            }

            Log::warning('Gemini API Non-200 or unexpected output: ' . $response->status() . ' ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('Gemini Matching Exception: ' . $e->getMessage());
        }

        return $this->fallbackHeuristic($lostItem, $foundItem, 'Menggunakan analisis pencocokan fallback internal.');
    }

    private function buildPrompt(LostItem $lost, FoundItem $found): string
    {
        return <<<PROMPT
Anda adalah asisten AI pencocok barang hilang dan barang temuan (Lost & Found) untuk kampus Universitas Pancasakti Tegal.
Tugas Anda adalah membandingkan dua deskripsi barang di bawah ini dan menilai tingkat kemungkinan bahwa barang temuan adalah barang hilang yang dilaporkan.

[DATA BARANG HILANG]
- Judul/Nama: {$lost->title}
- Kategori: {$lost->category?->name}
- Merek: {$lost->brand}
- Warna Utama: {$lost->primary_color}
- Deskripsi & Ciri Khusus: {$lost->description}
- Lokasi Terakhir Dilihat: {$lost->last_seen_location}
- Tanggal Kehilangan: {$lost->last_seen_date?->format('Y-m-d')}

[DATA BARANG TEMUAN]
- Judul/Nama: {$found->title}
- Kategori: {$found->category?->name}
- Merek: {$found->brand}
- Warna Utama: {$found->primary_color}
- Deskripsi & Kondisi: {$found->description}
- Lokasi Ditemukan: {$found->found_location}
- Tanggal Ditemukan: {$found->found_date?->format('Y-m-d')}

Berikan analisis dalam format JSON murni tanpa markdown wrapper dengan struktur berikut:
{
  "score": integer antara 0 sampai 98 (tingkat kemiripan dalam persen),
  "reasons": [
    "poin alasan 1 bahasa indonesia singkat",
    "poin alasan 2 bahasa indonesia singkat"
  ]
}
PROMPT;
    }

    private function fallbackHeuristic(LostItem $lost, FoundItem $found, string $note): array
    {
        $score = 50;
        $reasons = [];

        if ($lost->category_id === $found->category_id) {
            $score += 20;
            $reasons[] = 'Kategori barang cocok sempurna: ' . ($lost->category?->name ?? 'Sama');
        }

        if (\Illuminate\Support\Str::contains(strtolower($found->primary_color), strtolower($lost->primary_color)) ||
            \Illuminate\Support\Str::contains(strtolower($lost->primary_color), strtolower($found->primary_color))) {
            $score += 15;
            $reasons[] = 'Warna utama cocok: ' . $lost->primary_color;
        }

        if (\Illuminate\Support\Str::contains(strtolower($found->found_location), strtolower($lost->last_seen_location)) ||
            \Illuminate\Support\Str::contains(strtolower($lost->last_seen_location), strtolower($found->found_location))) {
            $score += 10;
            $reasons[] = 'Lokasi penemuan berdekatan: ' . $found->found_location;
        }

        $score = min($score, 98);
        $confidence = match (true) {
            $score >= 85 => 'SANGAT_TINGGI',
            $score >= 70 => 'TINGGI',
            $score >= 50 => 'SEDANG',
            default => 'RENDAH',
        };

        return [
            'score' => $score,
            'confidence' => $confidence,
            'reasons' => $reasons,
            'ai_analyzed' => false,
        ];
    }
}

