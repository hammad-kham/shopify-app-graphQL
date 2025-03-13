<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ShopifyProductController extends Controller
{

    public function getProducts()
    {
        $shop = Auth::user();
        if (!$shop) {
            return response()->json(['error' => 'Shop is not authenticated.'], 401);
        }

            $query = <<<'GRAPHQL'
        {
            products(first: 10, sortKey: CREATED_AT, reverse: true) {
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

        // Extract products 
        $shopifyProducts = $response['body']['data']['products']['edges'] ?? [];

        return view('Products.index', compact('shopifyProducts'));
    }

    public function create()
    {
        return view('Products.create');
    }


    //create the product..
    public function store(Request $request)
    {
        $shop = Auth::user();

        // Validate input data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:ACTIVE,DRAFT,ARCHIVED',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // GraphQL Mutation
        $mutation = <<<'GRAPHQL'
            mutation productCreate($input: ProductInput!) {
                productCreate(input: $input) {
                    product {
                        id
                        title
                        status
                        variants(first: 5) {
                            edges {
                                node {
                                    id
                                    price
                                }
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

        // Variables
        $variables = [
            'input' => [
                'title' => $validated['title'],
                'status' => $validated['status'],
                'variants' => [
                    ['price' => (string) $validated['price']]
                ]
            ]
        ];
        // Call Shopify API
        $response = $shop->api()->graph($mutation, $variables);
        //get the body from response
        $body = $response['body'];
        //extract creaetProduct from body----actual data..
        $data = $body->container['data']['productCreate'] ?? null;
        // dd($data);


        //handle error if any
        if (!$data || !isset($data['product'])) {
            return response()->json([
                'error' => $data['userErrors'][0]['message'] ?? 'Product creation failed!'
            ], 400);
        }
        //extract the product detail from data
        $product = $data['product'];

        $formattedResponse = [
            'product_id' => $product['id'],
            'title' => $product['title'],
            'status' => $product['status'],
            'variants' => []
        ];

        // Loop through variants (inside 'edges')
        foreach ($product['variants']['edges'] as $variantEdge) {
            // Extract variant data
            $variant = $variantEdge['node'];

            $formattedResponse['variants'][] = [
                'variant_id' => $variant['id'],
                'price' => $variant['price']
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Product create successfully.',
            'product' => $formattedResponse,
        ], 201);
    }


    public function deleteProduct(Request $request)
    {
        $shop = Auth::user();
        if (!$shop) {
            return response()->json(['error' => 'Shop is not authenticated.'], 401);
        }

        $productId = $request->input('product_id');

            $mutation = <<<'GRAPHQL'
                mutation productDelete($id: ID!) {
                    productDelete(input: {id: $id}) {
                        deletedProductId
                        userErrors {
                            field
                            message
                        }
                    }
                }
                GRAPHQL;

        $variables = [
            'id' => $productId,
        ];

        $response = $shop->api()->graph($mutation, $variables);
        $data = $response['body']['data']['productDelete'] ?? null;

        if (isset($data['userErrors']) && count($data['userErrors']) > 0) {
            return response()->json([
                'success' => false,
                'error' => $data['userErrors'][0]['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
            'deleted_product_id' => $data['deletedProductId'],
        ]);
    }
}
