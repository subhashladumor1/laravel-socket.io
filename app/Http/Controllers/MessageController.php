<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        try {
            $from = Auth::id();
            $to = $request->input('to');
            $messageText = $request->input('message');

            if (!$to || !User::find($to) || $to == $from) {
                return response()->json(['error' => 'Invalid recipient'], 400);
            }

            $message = Message::create([
                'from_id' => $from,
                'to_id' => $to,
                'message' => $messageText,
                'delivered' => 0,
            ]);

            $data = [
                'room' => 'user_' . $to,
                'event' => 'new-message',
                'data' => [
                    'id' => $message->id,
                    'from' => $from,
                    'from_name' => Auth::user()->name,
                    'message' => $messageText,
                ],
            ];

            $ch = curl_init('http://127.0.0.1:3000/push');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);

            return response()->json(['success' => true, 'id' => $message->id]);
        } catch (\Exception $e) {
            \Log::error('Send endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function pending()
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                \Log::error('No authenticated user for /api/pending');
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            $messages = Message::with('from')->where('to_id', $userId)->where('delivered', 0)->get();
            foreach ($messages as $msg) {
                $msg->delivered = 1;
                $msg->save();
            }
            return response()->json($messages->map(fn($msg) => [
                'id' => $msg->id,
                'from' => $msg->from_id,
                'from_name' => $msg->from->name,
                'message' => $msg->message,
                'sent_at' => $msg->created_at,
            ]));
        } catch (\Exception $e) {
            \Log::error('Pending endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function messages(Request $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                \Log::error('No authenticated user for /api/messages');
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            $with = $request->query('with');
            if (!$with || !User::find($with)) {
                return response()->json(['error' => 'Invalid user'], 400);
            }

            // Explicitly set the table to avoid from() error
            $messages = Message::with('from')
                ->where(function ($query) use ($userId, $with) {
                    $query->where('from_id', $userId)->where('to_id', $with);
                })
                ->orWhere(function ($query) use ($userId, $with) {
                    $query->where('from_id', $with)->where('to_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            $messages->where('to_id', $userId)->where('delivered', 0)->each(fn($msg) => $msg->update(['delivered' => 1]));

            return response()->json($messages->map(fn($msg) => [
                'id' => $msg->id,
                'from' => $msg->from_id,
                'from_name' => $msg->from->name,
                'message' => $msg->message,
                'sent_at' => $msg->created_at,
                'is_me' => $msg->from_id == $userId,
            ]));
        } catch (\Exception $e) {
            \Log::error('Messages endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function markDelivered($id)
{
    try {
        $message = Message::findOrFail($id);
        if ($message->to_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $message->delivered = 1;
        $message->save();

        // Emit Socket.IO event to notify the sender
        $data = [
            'room' => 'user_' . $message->from_id, // Notify the sender
            'event' => 'message-delivered',
            'data' => [
                'message_id' => $message->id,
                'from' => $message->from_id,
                'to' => $message->to_id,
            ],
        ];

        $ch = curl_init('http://127.0.0.1:3000/push');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('MarkDelivered endpoint error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}

    public function markRead($id)
{
    try {
        $message = Message::findOrFail($id);
        if ($message->to_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $message->read_at = now();
        $message->save();

        // Emit Socket.IO event to notify the sender
        $data = [
            'room' => 'user_' . $message->from_id, // Notify the sender
            'event' => 'message-read',
            'data' => [
                'message_id' => $message->id,
                'from' => $message->from_id,
                'to' => $message->to_id,
            ],
        ];

        $ch = curl_init('http://127.0.0.1:3000/push');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('MarkRead endpoint error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}

    public function lastMessage($userId)
    {
        try {
            $currentUserId = Auth::id();
            if (!$currentUserId) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $lastMessage = Message::where(function ($query) use ($currentUserId, $userId) {
                $query->where('from_id', $currentUserId)->where('to_id', $userId);
            })
            ->orWhere(function ($query) use ($currentUserId, $userId) {
                $query->where('from_id', $userId)->where('to_id', $currentUserId);
            })
            ->orderBy('created_at', 'desc')
            ->first();

            if ($lastMessage) {
                return response()->json([
                    'message' => $lastMessage->message,
                    'created_at' => $lastMessage->created_at,
                    'is_me' => $lastMessage->from_id == $currentUserId
                ]);
            }

            return response()->json(['message' => null]);
        } catch (\Exception $e) {
            \Log::error('LastMessage endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function markConversationRead($userId)
{
    try {
        $currentUserId = Auth::id();
        if (!$currentUserId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Mark all messages from this user as read
        $messages = Message::where('from_id', $userId)
            ->where('to_id', $currentUserId)
            ->whereNull('read_at')
            ->get();

        foreach ($messages as $message) {
            $message->read_at = now();
            $message->save();

            // Emit Socket.IO event for each message
            $data = [
                'room' => 'user_' . $message->from_id, // Notify the sender
                'event' => 'message-read',
                'data' => [
                    'message_id' => $message->id,
                    'from' => $message->from_id,
                    'to' => $message->to_id,
                ],
            ];

            $ch = curl_init('http://127.0.0.1:3000/push');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }

        // Emit conversation-read event
        $data = [
            'room' => 'user_' . $userId,
            'event' => 'conversation-read',
            'data' => [
                'from' => $userId,
                'to' => $currentUserId,
                'conversation_id' => $userId,
            ],
        ];

        $ch = curl_init('http://127.0.0.1:3000/push');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('Mark conversation read error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}
}