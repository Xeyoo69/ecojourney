<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendChatMessageRequest;
use App\Support\EcoChatAssistant;
use Illuminate\Http\JsonResponse;
use Throwable;

class ChatController extends Controller
{
    public function __construct(private readonly EcoChatAssistant $assistant) {}

    public function send(SendChatMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $reply = $this->assistant->reply(
                $validated['message'],
                $validated['history'] ?? [],
                $validated['locale'] ?? app()->getLocale(),
            );
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'The assistant is unavailable right now. Please try again in a moment.',
            ], 503);
        }

        return response()->json([
            'data' => [
                'reply' => $reply,
            ],
        ]);
    }
}
