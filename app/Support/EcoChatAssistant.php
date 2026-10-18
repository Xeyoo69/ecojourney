<?php

namespace App\Support;

use App\Models\Destination;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Grounds the EcoJourney chat assistant in real destination data and calls
 * the OpenAI chat completions API. Kept framework-light so it is easy to swap
 * providers later.
 */
class EcoChatAssistant
{
    private const CONTEXT_LIMIT = 10;

    private const HISTORY_LIMIT = 6;

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function reply(string $message, array $history, string $locale): string
    {
        $apiKey = config('services.openai.key');

        if (blank($apiKey)) {
            throw new RuntimeException('OpenAI API key is not configured.');
        }

        $payload = [
            'model' => config('services.openai.model'),
            'messages' => $this->buildMessages($message, $history, $locale),
            'temperature' => 0.6,
            'max_tokens' => (int) config('services.openai.max_tokens', 800),
        ];

        $reasoningEffort = config('services.openai.reasoning_effort');

        if (filled($reasoningEffort)) {
            $payload['reasoning_effort'] = $reasoningEffort;
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout((int) config('services.openai.timeout', 30))
            ->post(rtrim((string) config('services.openai.base_url'), '/').'/chat/completions', $payload);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI request failed with status '.$response->status().'.');
        }

        $reply = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($reply) || trim($reply) === '') {
            throw new RuntimeException('OpenAI returned an empty reply.');
        }

        return trim($reply);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array<int, array{role: string, content: string}>
     */
    private function buildMessages(string $message, array $history, string $locale): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt($message, $locale)],
        ];

        foreach (array_slice($history, -self::HISTORY_LIMIT) as $turn) {
            if (! isset($turn['role'], $turn['content'])) {
                continue;
            }

            if (! in_array($turn['role'], ['user', 'assistant'], true)) {
                continue;
            }

            $messages[] = [
                'role' => $turn['role'],
                'content' => Str::limit((string) $turn['content'], 2000, ''),
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        return $messages;
    }

    private function systemPrompt(string $message, string $locale): string
    {
        $defaultLanguage = $locale === 'en' ? 'English' : 'Bahasa Indonesia';

        return <<<PROMPT
        You are EcoJourney's travel assistant. EcoJourney is a smart sustainable tourism platform for Indonesia that helps travellers find low-impact destinations and plan lighter-footprint trips.

        Rules:
        - Recommend only destinations from the CONTEXT list below. Never invent places, prices, or Eco Scores.
        - When you recommend a destination, mention its Eco Score (out of 100) from the context.
        - Be warm, practical, and encouraging. Never preachy or guilt-tripping.
        - Avoid clichés such as "save the planet", "zero impact", "perfectly sustainable", or "authentic experience".
        - Keep answers concise (roughly 180 words max). Prefer short paragraphs or a compact list.
        - Always reply in the same language the traveller uses in their latest message (for example Bahasa Indonesia, English, or any other language). Match their language naturally. Only if their message is too short or ambiguous to identify a language, default to {$defaultLanguage}. Never switch to a different language than the one they wrote in.

        CONTEXT (curated destinations):
        {$this->contextFor($message)}
        PROMPT;
    }

    private function contextFor(string $message): string
    {
        $destinations = $this->relevantDestinations($message);

        if ($destinations->isEmpty()) {
            return 'No destinations are available right now. Encourage the traveller to explore the EcoJourney destination list.';
        }

        return $destinations->map(function (Destination $destination): string {
            $category = $destination->category?->name ?? 'General';
            $location = collect([$destination->location, $destination->region])->filter()->implode(', ');
            $score = (int) $destination->sustainability_score;
            $summary = Str::limit((string) $destination->description, 160, '…');

            return sprintf(
                '- %s (%s) — Category: %s. Eco Score: %d/100.%s %s',
                $destination->name,
                $location !== '' ? $location : 'Indonesia',
                $category,
                $score,
                $destination->slug ? " Slug: {$destination->slug}." : '',
                $summary,
            );
        })->implode("\n");
    }

    /**
     * @return Collection<int, Destination>
     */
    private function relevantDestinations(string $message): Collection
    {
        $terms = collect(preg_split('/[^\p{L}\p{N}]+/u', Str::lower($message)) ?: [])
            ->filter(fn (string $term): bool => Str::length($term) >= 4)
            ->unique()
            ->take(8)
            ->values();

        $base = Destination::with('category');

        if ($terms->isNotEmpty()) {
            $matches = (clone $base)
                ->where(function ($query) use ($terms): void {
                    foreach ($terms as $term) {
                        $like = '%'.$term.'%';
                        $query->orWhere('name', 'like', $like)
                            ->orWhere('location', 'like', $like)
                            ->orWhere('region', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    }
                })
                ->orderByDesc('sustainability_score')
                ->limit(self::CONTEXT_LIMIT)
                ->get();

            if ($matches->isNotEmpty()) {
                return $matches;
            }
        }

        return (clone $base)
            ->orderByDesc('sustainability_score')
            ->orderBy('name')
            ->limit(self::CONTEXT_LIMIT)
            ->get();
    }
}
