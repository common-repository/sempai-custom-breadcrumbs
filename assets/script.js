document.addEventListener('DOMContentLoaded', function() {
    var addButton = document.getElementById('add-field');
    var fieldsContainer = document.getElementById('sempai-breadcrumbs-fields');

    addButton.addEventListener('click', function() {
        var row = document.createElement('tr');
        row.className = 'sempai-breadcrumbs-field';
        row.innerHTML = `
            <td>
                <select name="sempai_breadcrumbs_fields[page][]">
                    ${fieldsContainer.querySelector('select').innerHTML}
                </select>
            </td>
            <td>
                <input type="text" name="sempai_breadcrumbs_fields[name][]" placeholder="Page name">
            </td>
            <td>
                <button type="button" class="remove-field components-button editor-post-trash is-next-40px-default-size is-secondary is-destructive">Delete</button>
            </td>
        `;
        fieldsContainer.querySelector('tbody').appendChild(row);
    });

    fieldsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-field')) {
            e.target.closest('tr').remove();
        }
    });
});
