<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\ReadingTime;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class ReadprogressController extends Controller
{ 

    public function startReading(Request $request)
    {
        $userId = auth()->user()->id;

        $readingSession = ReadingTime::create([
            'user_id' => Auth::id(),
            'start_time' => now(),
            'end_time' => null , // It can be set later when the reading is finished
    ]);

        return response()->json([
            'ReadingTime'=> $readingSession-> id,
            'user_id' => $userId
          //  'message' => 'Reading session started'
          ]);
    }
    
    
    public function endReading(Request $request, $sessionId)
    {
        $userId = auth()->user()->id;
        $readingSession = ReadingTime::findOrFail($sessionId);

        if ($readingSession->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized to end this reading session'], 403);
        }
         // Check if the reading session has already ended
    if ($readingSession->end_time) {
        return response()->json(['message' => 'Reading session has already ended'], 400);
    }

        $readingSession->update([
            'end_time' => now(),
        ]);
    
        // Calculate the duration in seconds
        $durationInSeconds = $readingSession->end_time->diffInSeconds($readingSession->start_time);
    
        // Calculate the duration in minutes and seconds
        $minutes = floor($durationInSeconds / 60);
      $formattedDuration = $minutes. ' minutes';
        
        $readingSession->update(['duration' => $formattedDuration]);
    
        return response()->json([
            //'ReadingTime'=> $readingSession-> id,
            'user_id' => $userId,
            'message' => 'Reading session ended',
            'duration' => $formattedDuration]);
    }


public function getAllReadingSessions($userId)
{
    $user = User::findOrFail($userId);
    $readingSessions = ReadingTime::where('user_id', $userId)
                                 ->get(['start_time', 'end_time', 'duration']);

    // Calculate the total duration in seconds using the integrated parseDuration logic
    $totalDurationInSeconds = $readingSessions->reduce(function ($carry, $session) {
        if ($session->end_time) {
            // Directly parse the duration string into seconds here
            $durationParts = explode(' ', $session->duration);
            $minutes = isset($durationParts[0])? intval($durationParts[0]) : 0;
            $seconds = isset($durationParts[1])? intval(str_replace('seconds', '', $durationParts[1])) : 0;
            $durationInSeconds = $minutes * 60 + $seconds;
            return $carry + $durationInSeconds;
        }
        return $carry;
    }, 0); // Start with a total of 0 seconds

    // Convert total duration in seconds to hours and minutes
    $totalHours = floor($totalDurationInSeconds / 3600);
    $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);

    // Format the total duration as a string in the format "X hours and Y minutes"
    $formattedTotalDuration = $totalHours. ' hours and '. $totalMinutes. ' minutes';

    $formattedSessions = $readingSessions->map(function ($session) {
        // Explicitly cast start_time and end_time to DateTime objects
        $startTime = Carbon::parse($session->start_time);
        $endTime = $session->end_time? Carbon::parse($session->end_time) : null;

        return [
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => $endTime? $endTime->format('Y-m-d H:i:s') : null,
            'duration' => $session->duration,
        ];
    });

    // Return both the formatted reading sessions and the total duration in the desired format
    return response()->json([
        'user_id' => $userId,
        'sessions' => $formattedSessions,
        'total_duration' => $formattedTotalDuration // Total duration in the format "X hours and Y minutes"
    ]);
}


}

