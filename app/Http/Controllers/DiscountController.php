<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{

    public function listDiscounts()
    {
        $shop = Auth::user();
        if (!$shop) {
            return response()->json(['error' => 'Shop is not authenticated.'], 401);
        }

        // GraphQL query to fetch latest 5 discounts
        $query = <<<'GRAPHQL'
        {
            discountNodes(first: 5, reverse: true) {
                edges {
                    node {
                        id
                        discount {
                            __typename
                            ... on DiscountAutomaticBasic {
                                title
                                startsAt
                                endsAt
                                status
                            }
                            ... on DiscountCodeBasic {
                                title
                                startsAt
                                endsAt
                                status
                            }
                        }
                    }
                }
            }
        }
        GRAPHQL;

        // Make API call
        $response = $shop->api()->graph($query);

        // Extract discounts from response
        $discountEdges = $response['body']['data']['discountNodes']['edges'] ?? [];

        $discounts = [];
        foreach ($discountEdges as $edge) {
            $node = $edge['node'];
            $discountData = $node['discount'] ?? [];

            $discounts[] = [
                'id' => $node['id'],
                'title' => $discountData['title'] ?? 'N/A',
                'startsAt' => $discountData['startsAt'] ?? 'N/A',
                'endsAt' => $discountData['endsAt'] ?? 'N/A',
                'status' => $discountData['status'] ?? 'N/A',
            ];
        }

        // dd($discounts);

        return view('discounts.discountListing', compact('discounts'));
    }

    public function create()
    {
        return view('discounts.createDiscount');
    }

    public function store(Request $request) {
        $shop = Auth::user();
        $accessToken = $shop->password;
    
        // Validate the request
        $request->validate([
            'title' => 'required|string',
            'startsAt' => 'required|date',
            'endsAt' => 'required|date|after:startsAt',
            'discountPercentage' => 'required|numeric|min:1|max:100',
        ]);
    
        // Convert dates to ISO 8601 format
        $startsAt = \Carbon\Carbon::parse($request->startsAt)->toIso8601String();
        $endsAt = \Carbon\Carbon::parse($request->endsAt)->toIso8601String();
    
        // GraphQL Mutation
        $query = <<<'GRAPHQL'
        mutation discountCodeBasicCreate($basicCodeDiscount: DiscountCodeBasicInput!) {
            discountCodeBasicCreate(basicCodeDiscount: $basicCodeDiscount) {
                codeDiscountNode {
                    id
                    codeDiscount {
                        ... on DiscountCodeBasic {
                            title
                            status
                        }
                    }
                }
                userErrors {
                    field
                    message
                }
            }
        }
        GRAPHQL;
    
        // Mutation variables
        $variables = [
            'basicCodeDiscount' => [
                'title' => $request->title,
                'code' => strtoupper(str_replace(' ', '_', $request->title)) . '_' . time(),
                'startsAt' => $startsAt,
                'endsAt' => $endsAt,
                'customerSelection' => [
                    'all' => true
                ],
                'appliesOncePerCustomer' => true,
                'usageLimit' => 1,
                'combinesWith' => [
                    'orderDiscounts' => false,
                    'productDiscounts' => false,
                    'shippingDiscounts' => false
                ],
                'customerGets' => [
                    'value' => [
                        'percentage' => (float) $request->discountPercentage / 100
                    ],
                    'items' => [
                        'all' => true
                    ]
                ],
                'minimumRequirement' => [
                    'greaterThanOrEqualToQuantity' => 2
                ]
            ]
        ];
    
        // Send request to Shopify GraphQL API
        $response = $shop->api()->graph($query, $variables);
    
        // Check for errors
        $errors = $response['body']['data']['discountCodeBasicCreate']['userErrors'] ?? [];
    
        if (!empty($errors)) {
            Log::error('Shopify Discount Error', ['errors' => $errors, 'response' => $response]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to create discount.',
                'errors' => $errors,
            ], 422);
        }
    
        // Get the created discount details
        $discountData = $response['body']['data']['discountCodeBasicCreate']['codeDiscountNode'] ?? null;
    
        return response()->json([
            'success' => true,
            'message' => 'Discount created successfully!',
            'discount' => $discountData
        ], 201);
    }
    
    
}
