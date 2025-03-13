@extends('shopify-app::layouts.default')

@section('content')
    <div class="container" style="padding: 20px; max-width: 600px; margin: auto;">
        <h1 style="text-align: center; margin-bottom: 20px;"> Rule</h1>

        <div class="header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 24px;">Create Rules</h1>
            <a href="{{ URL::tokenRoute('home') }}" class="btn primary">Back</a>

        </div>

        <div id="successMessage" style="display: none; color: green; font-weight: bold; margin-top: 10px;"></div>

        <form  id="addRuleForm"   style="display: flex; flex-direction: column; gap: 15px;">
            <label>Title</label>
            <input type="text" name="title" required style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">

            <label>Thumbnail</label>
            <input type="file" name="thumbnail" accept="image/*"  style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">

            <label>Priority</label>
            <input type="number" name="priority" required style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">

            <label>Status</label>
            <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>

            <button type="submit" class="btn success" style="padding: 10px; font-size: 16px; border-radius: 5px;">Save Rule</button>
        </form>
    </div>
@endsection

@section('scripts')
<script>

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("addRuleForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const form = document.getElementById("addRuleForm");
        const formData = new FormData(form);
        const successMessage = document.getElementById("successMessage");

        // Create GraphQL mutation request
        const requestData = {
            query: `
                mutation CreateRule($title: String!, $priority: Int!, $status: Boolean!) {
                    createRule(title: $title, priority: $priority, status: $status) {
                        id
                    }
                }
            `,
            variables: {
                title: formData.get("title"),
                priority: parseInt(formData.get("priority")),
                status: formData.get("status") === "1"
            }
        };

        fetch(`/graphql`, {
            method: "POST",
            headers: { 
                "Content-Type": "application/json",
                "Accept": "application/json"            
            },
            body: JSON.stringify(requestData),
        })
        .then(response => response.json())
        .then(data => {
            if (data.data?.createRule) {
                successMessage.textContent = "Rule created successfully!";
                successMessage.style.display = "block";
                form.reset();
                setTimeout(() => successMessage.style.display = "none", 5000);
            } else {
                alert(data.errors ? data.errors[0].message : "Error creating rule");
            }
        })
        .catch(error => console.error("Error:", error));
    });
});


</script>
@endsection
