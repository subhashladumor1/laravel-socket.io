<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\UserOnlineStatus;
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
                'delivered' => false,
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

            return response()->json(['success' => true, 'message_id' => $message->id]);
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
            $messages = Message::with('from')->where('to_id', $userId)->where('delivered', false)->get();
            foreach ($messages as $msg) {
                $msg->delivered = true;
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

            $messages->where('to_id', $userId)->where('delivered', false)->each(fn($msg) => $msg->update(['delivered' => true]));

            return response()->json($messages->map(fn($msg) => [
                'id' => $msg->id,
                'from' => $msg->from_id,
                'from_name' => $msg->from->name,
                'message' => $msg->message,
                'sent_at' => $msg->created_at,
                'is_me' => $msg->from_id == $userId,
                'delivered' => $msg->delivered,
                'read_at' => $msg->read_at,
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
            $message->delivered = true;
            $message->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('MarkDelivered endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            $messageIds = $request->input('message_ids', []);
            $userId = Auth::id();
            
            if (empty($messageIds)) {
                return response()->json(['error' => 'No message IDs provided'], 400);
            }

            Message::whereIn('id', $messageIds)
                ->where('to_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('MarkAsRead endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getOnlineUsers()
    {
        try {
            // Return empty array initially - let Socket.IO handle real-time status
            // This prevents showing all users as online initially
            return response()->json([]);
        } catch (\Exception $e) {
            \Log::error('GetOnlineUsers endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}