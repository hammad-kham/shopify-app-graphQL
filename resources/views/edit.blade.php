@extends('shopify-app::layouts.default')

@section('content')
    <div class="container" style="padding: 20px; max-width: 600px; margin: auto;">
        <h1 style="text-align: center; margin-bottom: 20px;"> Rule</h1>

        <div class="header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 24px;">update Rule</h1>
            <a href="{{ URL::tokenRoute('home') }}" class="btn primary">Back</a>

        </div>

        <div id="successMessage" style="display: none; color: green; font-weight: bold; margin-top: 10px;"></div>

        <form id="updateForm" data-id="{{ $rule->id }}" style="display: flex; flex-direction: column; gap: 15px;">
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title', $rule->title) }}" required 
                   style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        
                   <label>Thumbnail</label>
                   <input type="file" name="thumbnail" accept="image/*"
                          style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                   
                   @if ($rule->thumbnail)
                       <div style="margin-top: 4px;">
                           <img id="imagePreview" src="{{ $rule->thumbnail_url }}" alt="Thumbnail" width="60" height="60"
                                style="border: 1px solid #ddd; border-radius: 5px;">
                       </div>
                   @endif
                   
        
            <label>Priority</label>
            <input type="number" name="priority" value="{{ old('priority', $rule->priority) }}" required
                   style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        
            <label>Status</label>
            <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="1" {{ old('status', $rule->status) == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $rule->status) == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        
            <button type="submit" class="btn success" style="padding: 10px; font-size: 16px; border-radius: 5px;">
                Save Rule
            </button>
        </form>
        
        
    </div>
@endsection

@section('scripts')
<script>


document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("updateForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const form = document.getElementById("updateForm");
        const formData = new FormData(form);
        const successMessage = document.getElementById("successMessage");
        const ruleId = form.getAttribute("data-id");


        const requestData = {
            query: `
                mutation UpdateRule($id: ID!, $title: String!, $priority: Int!, $status: Boolean!) {
                    updateRule(id: $id, title: $title, priority: $priority, status: $status) {
                        id
                    }
                }
            `,
            variables: {
                id: ruleId,
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
            if (data.data?.updateRule) {
                successMessage.textContent = "Rule updated successfully!";
                successMessage.style.display = "block";
                setTimeout(() => successMessage.style.display = "none", 5000);
            } else {
                alert(data.errors ? data.errors[0].message : "Error updating rule");
            }
        })
        .catch(error => console.error("Error:", error));
    });
});


</script>
@endsection
