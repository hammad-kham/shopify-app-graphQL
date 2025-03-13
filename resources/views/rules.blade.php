@extends('shopify-app::layouts.default')
{{ Auth::user()->name; }}
@section('content')
    <div class="container" style="padding: 20px; max-width: 1200px; margin: auto;">
        <div class="header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 24px;">Rules</h1>
            <a href="{{ URL::tokenRoute('create.rule') }}" class="btn primary">+ Add Rule</a>
        </div>



        <div id="successMessage" style="display: none; color: green; font-weight: bold; margin-bottom: 10px;"></div>

        <div class="table-container" style="overflow-x: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
            <table class="uptown-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background: #f4f4f4;">
                    <tr>
                        <th style="padding: 15px;">Title</th>
                        <th style="padding: 15px; text-align: center;">Thumbnail</th>
                        <th style="padding: 15px; text-align: center;">Priority</th>
                        <th style="padding: 15px; text-align: center;">Status</th>
                        <th style="padding: 15px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="rulesTableBody" style="font-size: 16px;">
                    @foreach ($rules as $rule)
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 15px;">{{ $rule->title }}</td>
                        <td style="padding: 15px; text-align: center;">
                            <img src="{{ $rule->thumbnail_url }}" alt="Rule Thumbnail" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;">
                        </td>
                        <td style="padding: 15px; text-align: center;">{{ $rule->priority }}</td>
                        <td style="padding: 15px; text-align: center;">
                            @if($rule->status)
                                <span style="background: #28a745; color: white; padding: 6px 12px; border-radius: 5px;">Active</span>
                            @else
                                <span style="background: #dc3545; color: white; padding: 6px 12px; border-radius: 5px;">Inactive</span>
                            @endif
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ URL::tokenRoute('rule.edit', ['rule' => $rule->id]) }}" class="btn primary" style="margin-right: 10px;">Edit</a>
                            <button class="delete-btn" data-id="{{ $rule->id }}" style="background-color: red; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 5px;">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>



document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-btn")) {
            const ruleId = event.target.getAttribute("data-id");

            if (!confirm("Are you sure to delete this?")) {
                return;
            }

            fetch(`/graphql`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    query: `
                        mutation DeleteRule($id: ID!) {
                            deleteRule(id: $id) {
                                id
                            }
                        }
                    `,
                    variables: {
                        id: ruleId
                    }
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.data?.deleteRule) {
                    const successMessage = document.getElementById("successMessage");
                    successMessage.textContent = "Rule deleted successfully!";
                    successMessage.style.display = "block";
                    event.target.closest("tr").remove();
                    setTimeout(() => { successMessage.style.display = "none"; }, 5000);
                } else {
                    alert(data.errors ? data.errors[0].message : "Error deleting rule");
                }
            })
            .catch(error => console.error("Error:", error));
        }
    });
});

</script>
@endsection
