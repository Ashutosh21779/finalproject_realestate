<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Events\ChatMessageSent;
use App\Models\Property;

class ChatController extends Controller
{
    public function SendMsg(Request $request){
        try {
            // Basic validation
            if (empty($request->msg) || empty($request->receiver_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message and receiver are required'
                ], 400);
            }

            $senderId = Auth::user()->id;
            $receiverId = $request->receiver_id;

            // Verify the receiver exists
            $receiver = User::find($receiverId);
            if (!$receiver) {
                return response()->json([
                    'success' => false,
                    'message' => 'The specified receiver does not exist'
                ], 400);
            }

            // Create the message
            $message = ChatMessage::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'property_id' => $request->property_id,
                'msg' => $request->msg,
                'is_read' => 0, // Mark as unread initially
                'created_at' => Carbon::now(),
            ]);

            // Load the user relationship for the broadcast event
            $message->load('user');

            // Try to broadcast the message
            try {
                broadcast(new ChatMessageSent($message));
                \Log::info('Message broadcast successfully: ' . $message->id);
            } catch (\Exception $e) {
                // Just log the error but continue
                \Log::error('Error broadcasting message: ' . $e->getMessage());
            }

            // Return the message with the sender information
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message->toArray()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending message: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }// End Method


    public function GetAllUsers(){
        try {
            $loggedInUserId = auth()->id();

            \Log::info('GetAllUsers called by user: ' . $loggedInUserId);

            // For regular users, show only agents and admins they've chatted with
            if (Auth::user()->role === 'user') {
                // Get IDs of agents/admins the user has chatted with
                $chatPartnerIds = ChatMessage::where(function($q) use ($loggedInUserId) {
                        $q->where('sender_id', $loggedInUserId)
                          ->orWhere('receiver_id', $loggedInUserId);
                    })
                    ->pluck('sender_id')
                    ->merge(ChatMessage::where(function($q) use ($loggedInUserId) {
                        $q->where('sender_id', $loggedInUserId)
                          ->orWhere('receiver_id', $loggedInUserId);
                    })
                    ->pluck('receiver_id'))
                    ->unique()
                    ->filter(function($id) use ($loggedInUserId) {
                        return $id != $loggedInUserId;
                    })
                    ->values()
                    ->toArray();

                // Get the users with those IDs who are agents or admins
                $users = User::whereIn('id', $chatPartnerIds)
                            ->whereIn('role', ['agent', 'admin'])
                            ->where('status', 'active')
                            ->get();

                \Log::info('Returning agents/admins with chat history: ' . $users->count() . ' agents');

                // Add last_message and unread_count properties
                $users->each(function($user) use ($loggedInUserId) {
                    // Find the last message between these users
                    $lastMessage = ChatMessage::where(function($q) use ($user, $loggedInUserId) {
                                $q->where(function($subq) use ($user, $loggedInUserId) {
                                    $subq->where('sender_id', $loggedInUserId)
                                         ->where('receiver_id', $user->id);
                                })
                                ->orWhere(function($subq) use ($user, $loggedInUserId) {
                                    $subq->where('sender_id', $user->id)
                                         ->where('receiver_id', $loggedInUserId);
                                });
                            })
                            ->orderBy('created_at', 'desc')
                            ->first();

                    if ($lastMessage) {
                        $user->last_message = $lastMessage;

                        // Store property_id if available
                        if ($lastMessage->property_id) {
                            $user->property_id = $lastMessage->property_id;
                        }
                    } else {
                        // Create a dummy last message object
                        $user->last_message = null;
                    }

                    // Count unread messages
                    $unreadCount = ChatMessage::where('sender_id', $user->id)
                                            ->where('receiver_id', $loggedInUserId)
                                            ->where('is_read', 0)
                                            ->count();
                    $user->unread_count = $unreadCount;
                });

                // Sort users by last message time (most recent first)
                $users = $users->sortByDesc(function($user) {
                    return $user->last_message ? $user->last_message->created_at : '1970-01-01';
                })->values();

                return response()->json($users);
            }

            // For agents/admins, show only users they've chatted with
            else {
                // Get IDs of users the agent/admin has chatted with
                $chatPartnerIds = ChatMessage::where(function($q) use ($loggedInUserId) {
                        $q->where('sender_id', $loggedInUserId)
                          ->orWhere('receiver_id', $loggedInUserId);
                    })
                    ->pluck('sender_id')
                    ->merge(ChatMessage::where(function($q) use ($loggedInUserId) {
                        $q->where('sender_id', $loggedInUserId)
                          ->orWhere('receiver_id', $loggedInUserId);
                    })
                    ->pluck('receiver_id'))
                    ->unique()
                    ->filter(function($id) use ($loggedInUserId) {
                        return $id != $loggedInUserId;
                    })
                    ->values()
                    ->toArray();

                // Get the users with those IDs who are regular users
                $users = User::whereIn('id', $chatPartnerIds)
                            ->where('role', 'user')
                            ->where('status', 'active')
                            ->get();

                \Log::info('Agent/Admin view: Returning users with chat history: ' . $users->count());

                // Add last_message and unread_count properties
                $users->each(function($user) use ($loggedInUserId) {
                    // Find the last message between these users (if any)
                    $lastMessage = ChatMessage::where(function($q) use ($user, $loggedInUserId) {
                                $q->where(function($subq) use ($user, $loggedInUserId) {
                                    $subq->where('sender_id', $loggedInUserId)
                                         ->where('receiver_id', $user->id);
                                })
                                ->orWhere(function($subq) use ($user, $loggedInUserId) {
                                    $subq->where('sender_id', $user->id)
                                         ->where('receiver_id', $loggedInUserId);
                                });
                            })
                            ->orderBy('created_at', 'desc')
                            ->first();

                    if ($lastMessage) {
                        $user->last_message = $lastMessage;

                        // Store property_id if available
                        if ($lastMessage->property_id) {
                            $user->property_id = $lastMessage->property_id;
                        }
                    } else {
                        // Create a dummy last message object
                        $user->last_message = null;
                    }

                    // Count unread messages
                    $unreadCount = ChatMessage::where('sender_id', $user->id)
                                            ->where('receiver_id', $loggedInUserId)
                                            ->where('is_read', 0)
                                            ->count();
                    $user->unread_count = $unreadCount;
                });

                // Sort users by last message time (most recent first)
                $users = $users->sortByDesc(function($user) {
                    return $user->last_message ? $user->last_message->created_at : '1970-01-01';
                })->values();

                return response()->json($users);
            }

        } catch (\Exception $e) {
            \Log::error('Error in GetAllUsers: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load conversations',
                'message' => $e->getMessage()
            ], 500);
        }
    }// End Method


    public function UserMsgById($userId){
        try {
            $loggedInUserId = auth()->id();
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'error' => 'User not found'
                ], 404);
            }

            // Get all messages between the two users
            $messages = ChatMessage::where(function($q) use ($userId, $loggedInUserId){
                $q->where(function($subq) use ($userId, $loggedInUserId){
                    $subq->where('sender_id', $loggedInUserId);
                    $subq->where('receiver_id', $userId);
                });
                $q->orWhere(function($subq) use ($userId, $loggedInUserId){
                    $subq->where('sender_id', $userId);
                    $subq->where('receiver_id', $loggedInUserId);
                });
            })->with('user')->orderBy('created_at', 'asc')->get();

            // Mark all unread messages from this user as read
            $updated = ChatMessage::where('sender_id', $userId)
                ->where('receiver_id', $loggedInUserId)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

            \Log::info("Marked {$updated} messages as read for user {$loggedInUserId} from sender {$userId}");

            // Get property details if this conversation has a property context
            $propertyContext = null;
            $propertyMessage = ChatMessage::where(function($q) use ($userId, $loggedInUserId){
                $q->where(function($subq) use ($userId, $loggedInUserId){
                    $subq->where('sender_id', $loggedInUserId);
                    $subq->where('receiver_id', $userId);
                });
                $q->orWhere(function($subq) use ($userId, $loggedInUserId){
                    $subq->where('sender_id', $userId);
                    $subq->where('receiver_id', $loggedInUserId);
                });
            })->whereNotNull('property_id')
              ->with('property')
              ->first();

            if ($propertyMessage && isset($propertyMessage->property)) {
                $propertyContext = $propertyMessage->property;
            }

            // Log the response for debugging
            \Log::info("UserMsgById response for user {$userId}: " . json_encode([
                'user_found' => !is_null($user),
                'message_count' => $messages->count(),
                'has_property_context' => !is_null($propertyContext)
            ]));

            return response()->json([
                'user' => $user,
                'messages' => $messages,
                'property_context' => $propertyContext
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in UserMsgById: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load messages',
                'message' => $e->getMessage()
            ], 500);
        }
    }// End Method


    // New method for direct message loading with HTML response
    public function GetChatMessages($userId) {
        try {
            $loggedInUserId = auth()->id();
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'error' => 'User not found'
                ], 404);
            }

            // Get all messages between the two users
            $messages = ChatMessage::where(function($q) use ($userId, $loggedInUserId){
                $q->where(function($subq) use ($userId, $loggedInUserId){
                    $subq->where('sender_id', $loggedInUserId);
                    $subq->where('receiver_id', $userId);
                });
                $q->orWhere(function($subq) use ($userId, $loggedInUserId){
                    $subq->where('sender_id', $userId);
                    $subq->where('receiver_id', $loggedInUserId);
                });
            })->with('user')->orderBy('created_at', 'asc')->get();

            // Mark messages as read
            ChatMessage::where('sender_id', $userId)
                ->where('receiver_id', $loggedInUserId)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

            // Generate HTML for messages
            $html = '';

            if ($messages->count() > 0) {
                foreach ($messages as $message) {
                    $isSent = $message->sender_id == $loggedInUserId;
                    $messageTime = \Carbon\Carbon::parse($message->created_at)->format('M d, g:i A');
                    $messageId = 'msg_' . $message->id;
                    $senderName = $isSent ? 'You' : $user->name;

                    if ($isSent) {
                        $html .= '<div class="message-bubble message-sent" id="' . $messageId . '">';
                        $html .= '<div>' . $message->msg . '</div>';
                        $html .= '<div class="message-meta text-start">' . $senderName . ' - ' . $messageTime . '</div>';
                        $html .= '</div>';
                    } else {
                        $html .= '<div class="message-bubble message-received" id="' . $messageId . '">';
                        $html .= '<div>' . $message->msg . '</div>';
                        $html .= '<div class="message-meta text-end">' . $senderName . ' - ' . $messageTime . '</div>';
                        $html .= '</div>';
                    }
                }
            } else {
                $html = '<div class="text-center text-muted p-3">No messages yet. Start the conversation!</div>';
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'user' => $user,
                'message_count' => $messages->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in GetChatMessages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load messages',
                'message' => $e->getMessage()
            ], 500);
        }
    }// End Method

    public function AgentLiveChat(){
        return view('agent.message.live_chat');
    }// End Method

    public function UserLiveChat(){
        // Fetch agents/admins the user has chatted with
        // We can potentially call GetAllUsers logic directly here or rely on JS AJAX call
        // For simplicity, let the JS call /user-all to load the list dynamically
        return view('frontend.dashboard.live_chat'); // Pass no specific user initially
    }// End Method

    // Handle User starting chat from a specific property page
    public function UserPropertyChat($propertyId){
        try {
            $property = Property::with('user')->where('id', $propertyId)->firstOrFail();

            if (!$property->agent_id) {
                // Handle case where property has no assigned agent
                return redirect()->back()->with('error', 'This property does not have an assigned agent to chat with.');
            }

            // Get the agent user from the relationship
            $agentUser = User::find($property->agent_id);

            if (!$agentUser) {
                return redirect()->back()->with('error', 'The agent for this property could not be found.');
            }

            // Get the property ID
            $propId = $property->id;

            // Check if there's an existing conversation with this agent
            $loggedInUserId = auth()->id();
            $existingConversation = ChatMessage::where(function($q) use ($loggedInUserId, $agentUser) {
                    $q->where(function($subq) use ($loggedInUserId, $agentUser) {
                        $subq->where('sender_id', $loggedInUserId)
                             ->where('receiver_id', $agentUser->id);
                    })
                    ->orWhere(function($subq) use ($loggedInUserId, $agentUser) {
                        $subq->where('sender_id', $agentUser->id)
                             ->where('receiver_id', $loggedInUserId);
                    });
                })
                ->exists();

            // If no existing conversation, create a welcome message from the agent
            if (!$existingConversation) {
                // Create a welcome message from the agent
                ChatMessage::create([
                    'sender_id' => $agentUser->id,
                    'receiver_id' => $loggedInUserId,
                    'property_id' => $propId,
                    'msg' => "Hello! I'm " . $agentUser->name . ". How can I help you with this property?",
                    'is_read' => 0,
                    'created_at' => now(),
                ]);

                \Log::info("Created welcome message from agent {$agentUser->id} to user {$loggedInUserId} for property {$propId}");
            }

            // Add a script to auto-select the agent when the page loads
            $autoSelectAgent = true;

            // Pass the specific agent, property ID, and auto-select flag to the view
            return view('frontend.dashboard.live_chat', compact('agentUser', 'propId', 'autoSelectAgent'));
        } catch (\Exception $e) {
            \Log::error('Error in UserPropertyChat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while trying to chat with the agent. Please try again later.');
        }
    }

    public function AdminLiveChat(){
         return view('admin.chat.live_chat');
    }// End Method

    /**
     * Handle user typing indicator
     */
    public function UserTyping(Request $request){
        try {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'is_typing' => 'required|boolean'
            ]);

            $userId = Auth::id();
            $receiverId = $request->receiver_id;
            $isTyping = $request->is_typing;

            // Broadcast typing event to the receiver
            broadcast(new \App\Events\UserTyping($userId, $receiverId, $isTyping));

            return response()->json([
                'success' => true,
                'message' => 'Typing status updated'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating typing status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update typing status',
                'error' => $e->getMessage()
            ], 500);
        }
    }// End Method

    /**
     * Mark a message as read
     */
    public function MarkMessageRead(Request $request){
        try {
            $request->validate([
                'message_id' => 'required|exists:chat_messages,id',
            ]);

            $userId = Auth::id();
            $messageId = $request->message_id;

            // Find the message
            $message = ChatMessage::find($messageId);

            // Only mark as read if the current user is the receiver
            if ($message && $message->receiver_id == $userId) {
                $message->is_read = 1;
                $message->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Message marked as read'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to mark this message as read'
            ], 403);

        } catch (\Exception $e) {
            \Log::error('Error marking message as read: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark message as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }// End Method

}
