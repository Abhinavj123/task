<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Task Management</title>
    <link rel="stylesheet" href="feature1.css">
</head>
<body>
    <div class="container">
        <h1>Advanced Task Management</h1>
        <div class="grid">
            <div class="form-container">
                <form id="taskForm">
                    <input type="text" id="title" placeholder="Task Title" required>
                    <textarea id="description" placeholder="Task Description"></textarea>
                    <input type="date" id="deadline" required>
                    <div id="subtaskContainer">
                        <input type="text" class="subtaskInput" placeholder="Subtask 1">
                    </div>
                    <button type="button" id="addSubtask">Add Subtask</button>
                    <button type="submit">Add Task</button>
                </form>
            </div>
            <div class="task-list-container">
                <ul id="taskList">
                    <?php
                        include 'display_task.php';
                    ?>
                </ul>
            </div>
        </div>
        <div class="progress-container">
            <div class="progress-bar">
                <span id="progressText">0%</span>
            </div>
        </div>
    </div>

    <script src="feature1.js"></script>
</body>
</html>
