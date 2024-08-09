<?php

namespace App\Http\Controllers;

use App\Models\Lane;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpParser\Node\Stmt\TryCatch;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validateData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|string|max:255',
                'link_issue' => 'required|string|url',
            ]);
            $laneId = 1;
            $position_ticket = 0;
            $ticket = new Ticket();
            $ticket->title =  $validateData['title'];
            $ticket->author = $request->input('author') ?  $request->input('author') : 'HienLV';
            $ticket->priority = $validateData['priority'];
            $ticket->description = $validateData['description'];
            $ticket->link_issue = $validateData['link_issue'];
            $ticket->position = $position_ticket;
            Ticket::where('lane_id', 1)->increment('position');
            $ticket->lane_id =  $laneId;
            $ticket->save();
            $lanes = Lane::with(['tickets' => function ($query) {
                $query->orderBy('position', 'asc');
            }])->get();
            return response()->json(['success' => true, 'data' => $lanes, 'message' => 'Ticket created successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The request object containing the updated ticket data.
     * @param int $laneId The ID of the lane containing the ticket.
     * @param int $ticketId The ID of the ticket to be updated.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the updated lane data.
     */
    public function update(Request $request, int $laneId, int $ticketId)
    {
        // Try to find the ticket with the given ID and lane ID.
        try {
            $ticket = Ticket::where('id', $ticketId)->where('lane_id', $laneId)->firstOrFail();

            // If the ticket is not found, return a JSON response with an error message.
            if (!$ticket) {
                return response()->json(['success' => false, 'message' => 'Ticket not found'], 404);
            }

            // Update the ticket's fields with the values from the request.
            $ticket->title = $request->input('title');
            $ticket->author = $request->input('author');
            $ticket->description = $request->input('description');
            $ticket->priority = $request->input('priority');
            $ticket->link_issue = $request->input('link_issue');

            // Save the updated ticket to the database.
            $ticket->save();

            // Retrieve the updated lane data with its tickets ordered by position.
            $Lanes = Lane::with(['tickets' => function ($query) {
                $query->orderBy('position', 'asc');
            }])->get();

            // Return a JSON response with the updated lane data and a success message.
            return response()->json(['success' => true, 'data' => $Lanes, 'message' => 'Ticket updated successfully'], 200);
        } catch (ValidationException $e) {
            // If there is a validation error, return a JSON response with the error details.
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket',
                'error' => $e->errors()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteTicket(Request $request, int $laneId, int $ticketId)
    {
        //
        try {
            $ticket = Ticket::where('id', $ticketId)->where('lane_id', $laneId)->firstOrFail();
            if (!$ticket) {
                return response()->json(['success' => false, 'message' => 'Ticket not found'], 404);
            }
            $ticket->delete();
            $Lanes = Lane::with(['tickets' => function ($query) {
                $query->orderBy('position', 'asc');
            }])->get();
            return response()->json(['success' => true, 'data' => $Lanes, 'message' => 'Ticket deleted successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deleted ticket',
                'error' => $e->errors()
            ], 500);
        }
    }
    public function moveTicket(Request $request)
    {
        $fromLaneId = $request->input('fromLaneId');
        $toLaneId = $request->input('toLaneId');
        $ticketId = $request->input('ticketId');
        $oldPosition = $request->input('oldIndex');
        $newPosition = $request->input('newIndex');
        DB::transaction(function () use ($ticketId, $toLaneId, $oldPosition, $newPosition) {

            $ticket = Ticket::find($ticketId);

            if (empty($ticket)) {
                return response()->json(['success' => false, 'message' => 'Ticket not found'], 404);
            }

            $currentLaneId = $ticket->lane_id;

            if ($currentLaneId == $toLaneId && $newPosition == $oldPosition) {
                return response()->json(['success' => false, 'message' => 'Ticket does not move'], 404);
            }

            if ($currentLaneId != $toLaneId) {
                Ticket::where('lane_id', $currentLaneId)
                    ->where('position', '>', $ticket->position)
                    ->decrement('position');

                Ticket::where('lane_id', $toLaneId)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');
            } else {
                if ($newPosition < $oldPosition) {
                    Ticket::where('lane_id', $currentLaneId)
                        ->where('position', '>=', $newPosition)
                        ->where('position', '<', $oldPosition)
                        ->increment('position');
                } else {
                    Ticket::where('lane_id', $currentLaneId)
                        ->where('position', '>', $newPosition)
                        ->where('position', '<=', $oldPosition)
                        ->decrement('position');
                }
            }
            $ticket->lane_id = $toLaneId;
            $ticket->position = $newPosition;
            $ticket->save();
        });
        $lanes = Lane::with(['tickets' => function ($query) {
            $query->orderBy('position', 'asc');
        }])->get();
        return response()->json(['success' => true, 'data' => $lanes, 'message' => 'Lanes retrieved successfully'], 200);
    }
}