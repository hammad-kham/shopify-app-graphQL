@extends('shopify-app::layouts.default')


@section('content')
<div class="container">
    <h2>Create New Discount</h2>

    <form id="discountForm">
        <div class="mb-3">
            <label class="form-label">Discount Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Start Date</label>
            <input type="datetime-local" name="startsAt" class="form-control"  step="60">
        </div>
        
        <div class="mb-3">
            <label class="form-label">End Date</label>
            <input type="datetime-local" name="endsAt" class="form-control"  step="60">
        </div>

        <div class="mb-3">
            <label class="form-label">Product Variant ID</label>
            <input type="text" name="variantId" class="form-control" required placeholder="Enter Product Variant ID">
        </div>

        

        <div class="mb-3">
            <label class="form-label">Discount Percentage</label>
            <input type="number" step="0.01" name="discountPercentage" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Discount</button>
    </form>
</div>

<script>

            document.addEventListener("DOMContentLoaded", function () {
                function getFormattedDateTime() {
                    const now = new Date();
                    return now.toISOString().slice(0, 16);  // Gets YYYY-MM-DDTHH:MM format
                }

                document.querySelector('input[name="startsAt"]').value = getFormattedDateTime();
                document.querySelector('input[name="endsAt"]').value = getFormattedDateTime();
            });




    document.getElementById("discountForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const data = {
            title: formData.get("title"),
            startsAt: new Date(formData.get("startsAt")).toISOString(),
            endsAt: new Date(formData.get("endsAt")).toISOString(),
            variantId: formData.get("variantId"),
            discountPercentage: formData.get("discountPercentage")
        };

        fetch(`discount/create`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
                if (result.success) {
                    alert("Discount created successfully!");
                    document.getElementById("discountForm").reset();
                } else {
                    alert("Failed to create discount");
                    console.error("Errors:", result.errors);
                }
        })
        .catch(error => console.error("Error:", error));
    });
</script>
@endsection
