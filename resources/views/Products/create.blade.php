@extends('shopify-app::layouts.default')

@section('content')
<div class="container">

    <div class="header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="margin: 0; font-size: 24px;">Create Product</h1>
        <a href="{{ URL::tokenRoute('home') }}" class="btn primary">Back</a>

    </div>

    <div id="successMessage" class="alert alert-success" style="display: none;"></div>


    <form id="addProductForm">

        <label for="title">Product Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" required step="0.01">

        
        <label for="image">Product Image:</label>
        <input type="file" name="image" id="productImage">

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="ACTIVE">Active</option>
            <option value="DRAFT">Draft</option>
            <option value="ARCHIVED">Archived</option>
        </select>

        <button type="submit">Create Product</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const shop = "{{ request('shop') }}";
    const host = "{{ request('host') }}";
    const form = document.getElementById("addProductForm");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); 

        const formData = new FormData(form);

        fetch(`/create?shop=${shop}&host=${host}`, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successMessage.innerText = data.message;
        successMessage.classList.add("alert", "alert-success");
        successMessage.style.display = "block";

        setTimeout(() => {
            successMessage.style.display = "none";
        }, 3000);
            form.reset();
            } else {
               
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>
@endsection
