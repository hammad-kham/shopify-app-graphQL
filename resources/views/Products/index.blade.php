@extends('shopify-app::layouts.default')
{{ Auth::user()->name; }}


    <section class="section">
            <div class="card-content">
                <div class="card columns twelve">
                    <div class="table-responsive">
                        <div class="header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h1 style="margin: 0; font-size: 24px;">Shopify Products</h1>
                            <a href="{{ URL::tokenRoute('create.product') }}" class="btn primary">+ Add Rule</a>
                        </div>
                        <div id="successMessage" class="alert alert-success" style="display: none;"></div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    {{-- <th style="padding: 10px 15px; text-align: left;">ID</th> --}}
                                    <th style="padding: 6px 30px; text-align: left;">Title</th>
                                    <th style="padding: 10px 15px; text-align: left;">Inventory</th>
                                    <th style="padding: 10px 15px; text-align: left;">Image</th>
                                    <th style="padding: 10px 15px; text-align: left;">Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shopifyProducts as $product)
                                    <tr>
                                        {{-- <td style="padding: 12px 15px;">{{ $product['node']['id'] }}</td> --}}
                                        <td style="padding: 12px 15px;">{{ $product['node']['title'] }}</td>
                                        <td style="padding: 12px 15px;">{{ $product['node']['totalInventory'] }}</td>
                                        <td style="padding: 12px 15px;">
                                            @if (!empty($product['node']['images']['edges']))
                                                {{-- <img src="{{ $product['node']['images']['edges'][0]['node']['src'] }}" width="50" class="img-fluid" alt="Product Image"> --}}
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 15px;">
                                            <button class="badge primary">Edit</button>
                                            <button class="delete-btn" data-id="{{ $product['node']['id'] }}">Delete</button>                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </article>
    </section>
    @section('scripts')
<script>

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function () {
                const productId = this.getAttribute("data-id");
    
                if (!confirm("Are you sure you want to delete this product?")) {
                    return;
                }
    
                fetch(`/delete-product`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        successMessage.innerText = data.message; 
                        successMessage.style.display = "block";
                        setTimeout(() => {
                            successMessage.style.display = "none";
                        }, 3000);
                        this.closest("tr").remove();
                    }else {
                        alert("Error: " + data.error);
                    }
                })
                .catch(error => console.error("Error:", error));
            });
        });
    });
</script>
@endsection
