<?php

namespace App\Http\Controllers;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Group;


class GroupController extends Controller
{

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'devise' => 'nullable|string|size:3',
            'members' => 'required|array',
            'members.*' => 'exists:users,id'
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'devise' => $validated['devise'] ?? 'MAD',
        ]);

        $group->users()->attach($request->user()->id);

        foreach ($validated['members'] as $memberId) {
            if ($memberId != $request->user()->id) {
                $group->users()->attach($memberId);
            }
        }

        return response()->json([
            'message' => 'Group created successfully',
            'group' => $group->load('users')
        ], 201);
    }

    public function index(Request $request){
        $groups = $request->user()->groups()->with('users')->get();
        
        return response()->json($groups);
    }

    public function show(Request $request, $id){

        $group = Group::with([
            'users',
            'sharedExpenses.payments', 
            'sharedExpenses.shares'])->findOrFail($id);
                      
        if (!$group->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'You do not have access to this group'
            ], 403);
        }

        $balances = $group->calculateBalances();
        
        $response = [
            'id' => $group->id,
            'name' => $group->name,
            'currency' => $group->currency,
            'description' => $group->description,
            'created_at' => $group->created_at,
            'members' => $group->users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => (bool)$user->pivot->is_admin
                ];
            }),
            'expenses' => $group->sharedExpenses->map(function($expense) {
                return [
                    'id' => $expense->id,
                    'title' => $expense->title,
                    'amount' => $expense->amount,
                    'date' => $expense->date,
                    'created_by' => $expense->user->name,
                    'split_type' => $expense->split_type
                ];
            }),
            'balances' => $balances
        ];

        return response()->json($response);
    }

    public function destroy(Request $request, $id): JsonResponse{
        $group = Group::findOrFail($id);
        
        // Check if user is admin of the group
        if (!$group->users()->where('user_id', $request->user()->id)->where('is_admin', true)->exists()) {
            return response()->json(['message' => 'You are not authorized to delete this group'], 403);
        }
        
        // Check if there are remaining balances
        if ($group->hasRemainingBalances()) {
            return response()->json([
                'message' => 'Cannot delete group with remaining balances',
                'balances' => $group->calculateBalances()
            ], 422);
        }
        
        $group->delete();
        
        return response()->json(['message' => 'Group deleted successfully']);
    }
}