<?php
namespace App\Http\Controllers;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller{

    public function store(Request $request){

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $expense = Expense::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return response()->json($expense);
    }

    public function index(Request $request){
        $expenses = $request->user()->expenses()->latest()->get();

        if (!$expenses) {
            return response()->json([
                'message' => 'DB empty',
                'You want to create one?'=> url("/api/expenses")
            ], 404);
        }
        return response()->json($expenses);
    }

    public function show(Request $request, $id){

        $expense = $request->user()->expenses()->find($id);

        if (!$expense) {
            return response()->json([
                'message' => 'no expense with the fucking id '.$id,
                'Go to Show All Expenses'=> url("/api/expenses"),
                'dont forget the GET methode loool ^_0'], 404);
        }

        return response()->json($expense);
    }

    public function update(Request $request, $id){
        
        $expense = $request->user()->expenses()->find($id);

        if (!$expense) {
            return response()->json([
                'message' => 'No expense with ID ' . $id,
                'Go to Show All Expenses'=> url("/api/expenses"),
                'dont forget the GET methode loool ^_0'
            ], 404);
        }

        $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'amount' => 'numeric|min:0',
            'date' => 'date',
        ]);

        $expense->update($request->all());

        return response()->json($expense);
    }

    public function destroy(Request $request, $id){

        $expense = $request->user()->expenses()->find($id);

        if (!$expense) {
            return response()->json([
                'message' => 'No expense with ID ' . $id,
                'Go to Show All Expenses'=> url("/api/expenses"),
                'dont forget the GET methode loool ^_0'
            ], 404);
        }

        $expense->delete();
        return response()->json(['message' => 'you deleted it']);
    }

    public function addTags(Request $request, $expenseId){
        $expense = $request->user()->expenses()->findOrFail($expenseId);
        $validated = $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);
        $expense->tags()->sync($validated['tag_ids'], false);
        return response()->json([
            'message' => 'Tags attached successfully',
            'expense' => $expense->load('tags')
        ]);
    }
    
}