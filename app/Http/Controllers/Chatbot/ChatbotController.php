<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chatbot\ChatbotMessageRequest;
use App\Services\Chatbot\ChatbotReplyService;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    public function __construct(private readonly ChatbotReplyService $replyService) {}

    public function message(ChatbotMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $branchId = isset($validated['branch_id']) ? (int) $validated['branch_id'] : null;

        return response()->json(
            $this->replyService->reply((string) $validated['message'], $branchId)
        );
    }
}
