<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Validated;

class RuleController extends Controller
{
    public function index()
{
    $shop = Auth::user();
    if (!$shop) {
        return response()->json(['error' => 'Shop is not authenticated.'], 401);
    }

    // $query = <<<'GRAPHQL'
    // {
    //     products(first: 10) {
    //         edges {
    //             node {
    //                 id
    //                 title
    //                 status
    //                 totalInventory
    //             }
    //         }
    //     }
    // }
    // GRAPHQL;

    $query = <<<'GRAPHQL'
    {
        products(first: 10) {
            edges {
                node {
                    id
                    title
                    totalInventory
                    images(first: 1) {
                        edges {
                            node {
                                src
                            }
                        }
                    }
                }
            }
        }
    }
    GRAPHQL;
    

    // Make API call
    $response = $shop->api()->graph($query);

    // Extract products safely
    $shopifyProducts = $response['body']['data']['products']['edges'] ?? [];
    // Fetch rules
    $rules = Rule::all();

    return view('welcome', compact('rules', 'shopifyProducts'));
}

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'priority' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $imagePath = null;

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'rules/images/' . $imageName;        
            $image->move(public_path('rules/images'), $imageName);        
        }

        $rule = Rule::create([
            'title' => $request->title,
            'thumbnail' => $imagePath,
            'priority' => $request->priority,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Rule created successfully', 'rule' => $rule]);
    }


    public function edit($id)
    {
        $rule = Rule::findOrFail($id);
        return view('edit', compact('rule'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'priority' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $rule = Rule::findOrFail($id);

        if ($request->hasFile('thumbnail')) {
            // Delete old image if exists
            if ($rule->thumbnail && file_exists(public_path($rule->thumbnail))) {
                unlink(public_path($rule->thumbnail));
            }

            // Save new image
            $image = $request->file('thumbnail');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'rules/images/' . $imageName;

            $image->move(public_path('rules/images'), $imageName);

            $rule->thumbnail = $imagePath;
        }

        // Update other fields
        $rule->update([
            'title' => $request->title,
            'priority' => $request->priority,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Rule updated successfully', 'rule' => $rule]);
    }




    public function destroy($id)
{
    $rule = Rule::find($id);

    if (!$rule) {
        return [
            'id' => null,
            'message' => 'Rule not found.'
        ];
    }

    $rule->delete();

    return [
        'id' => $id,
        'message' => 'Rule deleted successfully!'
    ];
}

}
