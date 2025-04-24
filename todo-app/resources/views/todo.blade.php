<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <!-- Using Bootstrap for quick styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body style="background-color:rgb(21, 44, 70);">
<div class="container py-5">
    <h2 class="mb-4 text-white text-center">To-Do List-SoftLenz</h2>
    <!-- Add Todo Form -->
    <form id="add-todo-form" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" id="todo-title" class="form-control" placeholder="Add new to-do..." required>
            </div>
            <div class="col-md-4">
                <input type="datetime-local" id="todo-reminder" class="form-control" placeholder="Reminder (optional)">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" type="submit">Add</button>
            </div>
        </div>
    </form>
    <!-- Todo List -->
    <ul id="todo-list" class="list-group mb-4"></ul>
</div>
<script>
// Get all todos from backend and render them
function fetchTodos() {
    $.get('/api/todos', function(todos) {
        let html = '';
        todos.forEach(todo => {
            // Build each todo item as a list element
            html += `<li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start">
                <div class="d-flex align-items-center flex-grow-1 mb-2 mb-md-0">
                    <input type="checkbox" class="form-check-input me-2 toggle-completed" data-id="${todo.id}" ${todo.completed ? 'checked' : ''}>
                    <span class="todo-title flex-grow-1" data-id="${todo.id}" style="${todo.completed ? 'text-decoration: line-through;' : ''}">${todo.title}</span>
                    <input type="text" class="form-control form-control-sm edit-title-input d-none ms-2" data-id="${todo.id}" value="${todo.title}" style="max-width: 200px;">
                </div>
                <div class="d-flex flex-column flex-md-row align-items-md-center">
                    <small class="text-info me-3">${todo.reminder_at ? `Reminder: ${todo.reminder_at.replace('T', ' ').slice(0, 16)}` : ''}</small>
                    <small class="text-success me-3">${todo.completed_at ? `Completed: ${todo.completed_at.replace('T', ' ').slice(0, 16)}` : ''}</small>
                    <button class="btn btn-secondary btn-sm edit-todo me-1" data-id="${todo.id}">Edit</button>
                    <button class="btn btn-success btn-sm save-todo d-none me-1" data-id="${todo.id}">Save</button>
                    <button class="btn btn-danger btn-sm delete-todo" data-id="${todo.id}">Delete</button>
                </div>
            </li>`;
        });
        $('#todo-list').html(html);
    });
}

$(document).ready(function() {
    fetchTodos(); // Initial load

    // Add todo handler
    $('#add-todo-form').submit(function(e) {
        e.preventDefault();
        let title = $('#todo-title').val();
        let reminder = $('#todo-reminder').val(); // can be empty
        $.post('/api/todos', { title: title, reminder_at: reminder, _token: '{{ csrf_token() }}' }, function() {
            $('#todo-title').val('');
            $('#todo-reminder').val('');
            fetchTodos();
        });
    });

    // Delete todo handler
    $('#todo-list').on('click', '.delete-todo', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/api/todos/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: fetchTodos
        });
    });

    // Edit button click
    $('#todo-list').on('click', '.edit-todo', function() {
        let id = $(this).data('id');
        let $li = $(this).closest('li');
        $li.find('.todo-title').addClass('d-none');
        $li.find('.edit-title-input').removeClass('d-none').focus();
        $li.find('.edit-todo').addClass('d-none');
        $li.find('.save-todo').removeClass('d-none');
    });

    // Save button click
    $('#todo-list').on('click', '.save-todo', function() {
    let id = $(this).data('id');
    let $li = $(this).closest('li');
    let newTitle = $li.find('.edit-title-input').val();
let reminder = $li.find('.edit-reminder-input').val() || $li.find('.edit-reminder-input').attr('data-original');
$.ajax({
    url: `/api/todos/${id}`,
    type: 'PUT',
    data: { title: newTitle, reminder_at: reminder, _token: '{{ csrf_token() }}' },
    success: fetchTodos
});
});

// Enter key in edit input
$('#todo-list').on('keydown', '.edit-title-input', function(e) {
    if (e.key === 'Enter') {
        let id = $(this).data('id');
        let $li = $(this).closest('li');
        let newTitle = $(this).val();
        $.ajax({
            url: `/api/todos/${id}`,
            type: 'PUT',
            data: { title: newTitle, _token: '{{ csrf_token() }}' },
            success: fetchTodos
        });
    }
});

    $('#todo-list').on('change', '.toggle-completed', function() {
        let id = $(this).data('id');
        let completed = $(this).is(':checked');
        $.ajax({
            url: `/api/todos/${id}`,
            type: 'PUT',
            data: { completed: completed ? 1 : 0, _token: '{{ csrf_token() }}' },
            success: fetchTodos
        });
    });
});
</script>
</body>
</html>
