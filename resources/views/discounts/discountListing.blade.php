@extends('shopify-app::layouts.default')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="title">Latest Product-Specific Discounts</h2>
        <a class="btn btn-primary" href="{{ URL::tokenRoute('create.discount') }}">+ Create Discount</a>
    </div>

    @if(!empty($discounts))
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Valid From</th>
                        <th>Valid To</th>
                        <th>Discount</th>
                        <th>Products</th>
                        <th>Collections</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discounts as $discount)
                        <tr>
                            <td>{{ $discount['title'] }}</td>
                            <td>
                                <span class="badge {{ $discount['status'] == 'ACTIVE' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst(strtolower($discount['status'])) }}
                                </span>
                            </td>
                            <td>
                                {{ !empty($discount['startsAt']) ? \Carbon\Carbon::parse($discount['startsAt'])->format('d M Y') : '' }}
                            </td>
                            <td>
                                {{ !empty($discount['endsAt']) ? \Carbon\Carbon::parse($discount['endsAt'])->format('d M Y') : '' }}
                            </td>
                            
                            <td>
                                @if(isset($discount['discountValue']['amount']))
                                    {{ $discount['discountValue']['amount']['amount'] }} {{ $discount['discountValue']['amount']['currencyCode'] }}
                                @elseif(isset($discount['discountValue']['percentage']))
                                    {{ $discount['discountValue']['percentage'] }}%
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if(!empty($discount['products']))
                                    <ul>
                                        @foreach($discount['products'] as $product)
                                            <li>{{ $product['title'] }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    No Products
                                @endif
                            </td>
                            <td>
                                @if(!empty($discount['collections']))
                                    <ul>
                                        @foreach($discount['collections'] as $collection)
                                            <li>{{ $collection['title'] }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    No Collections
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info"><i class="icon-edit"></i> Edit</button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $discount['id'] }}">
                                    <i class="icon-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-center text-muted">No discounts available.</p>
    @endif
</div>

{{-- Delete Confirmation Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const discountId = this.getAttribute('data-id');
                if (confirm("Are you sure you want to delete this discount?")) {
                    fetch(`/discount/delete/${discountId}`, {
                        method: 'DELETE',
                        headers: { "Content-Type": "application/json" },
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert("Discount deleted successfully!");
                            location.reload(); // Refresh page
                        } else {
                            alert("Failed to delete discount");
                        }
                    })
                    .catch(error => console.error("Error:", error));
                }
            });
        });
    });
</script>

@endsection
