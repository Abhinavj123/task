document.addEventListener('DOMContentLoaded', () => {
    const taskForm = document.getElementById('taskForm');
    const taskList = document.getElementById('taskList');
    const addSubtaskButton = document.getElementById('addSubtask');
    const subtaskContainer = document.getElementById('subtaskContainer');
    const progressBar = document.querySelector('.progress-bar span');
    const progressText = document.getElementById('progressText');
    
    // Summary elements
    const totalTasksEl = document.getElementById('totalTasks');
    const totalSubtasksEl = document.getElementById('totalSubtasks');
    const upcomingDeadlineEl = document.getElementById('upcomingDeadline');
    
    // Prioritized Task elements
    const prioritizedTaskList = document.getElementById('prioritizedTaskList');

    let tasks = [];

    // Function to fetch tasks from the server
    function fetchTasks() {
        fetch('fetch_tasks.php')
            .then(response => response.json())
            .then(data => {
                tasks = data;
                renderTasks();
                updateProgress();
            })
            .catch(error => console.error('Error fetching tasks:', error));
    }

    // Function to render tasks and subtasks
    function renderTasks() {
        taskList.innerHTML = '';
        tasks.sort((a, b) => new Date(a.deadline) - new Date(b.deadline));
        tasks.forEach((task, index) => {
            const hoursLeft = calculateHoursLeft(new Date(task.deadline));
            const taskItem = document.createElement('li');
            taskItem.innerHTML = `
                <div>
                    <input type="checkbox" class="completeTask" ${task.completed ? 'checked' : ''} data-index="${index}">
                    <span style="text-decoration: ${task.completed ? 'line-through' : 'none'};">
                        ${task.title} - ${task.description}
                    </span>
                    <div class="task-deadline">
                        Deadline: ${new Date(task.deadline).toLocaleString()} 
                        (${hoursLeft} hours left)
                    </div>
                    <div class="task-priority">Priority: ${task.priority}</div>
                    <button class="deleteTask" data-index="${index}">Delete</button>
                </div>
                <ul class="subtaskList">
                    ${task.subtasks.map((subtask, subIndex) => `
                        <li class="subtaskItem">
                            <input type="checkbox" class="completeSubtask" ${subtask.completed ? 'checked' : ''} data-task-index="${index}" data-subtask-index="${subIndex}">
                            ${subtask.title}
                        </li>
                    `).join('')}
                </ul>
            `;
            taskList.appendChild(taskItem);
        });
        updateSummary();
        updatePrioritizedTasks();
        updateProgress();
    }    

    // Function to update summary statistics
    function updateSummary() {
        const totalTasks = tasks.length;
        const totalSubtasks = tasks.reduce((acc, task) => acc + task.subtasks.length, 0);
        const upcomingDeadline = tasks.length ? tasks[0].deadline : 'None';

        totalTasksEl.textContent = totalTasks;
        totalSubtasksEl.textContent = totalSubtasks;
        upcomingDeadlineEl.textContent = upcomingDeadline !== 'None' ? new Date(upcomingDeadline).toLocaleString() : 'None';
    }

    // Function to update prioritized tasks
    function updatePrioritizedTasks() {
        prioritizedTaskList.innerHTML = '';
        tasks.forEach(task => {
            const taskItem = document.createElement('li');
            taskItem.innerHTML = `
                <div>
                    <strong>${task.title}</strong>
                    <p>${task.description}</p>
                    <p class="task-deadline">Deadline: ${new Date(task.deadline).toLocaleString()}</p>
                </div>
            `;
            prioritizedTaskList.appendChild(taskItem);
        });
    }

    // Function to update progress bar
    function updateProgress() {
        const totalTasks = tasks.length;
        const totalSubtasks = tasks.reduce((acc, task) => acc + task.subtasks.length, 0);
        const completedTasks = tasks.filter(task => task.completed).length;
        const completedSubtasks = tasks.reduce((acc, task) => acc + task.subtasks.filter(subtask => subtask.completed).length, 0);

        const totalProgress = totalTasks + totalSubtasks;
        const completedProgress = completedTasks + completedSubtasks;
        const progress = totalProgress === 0 ? 0 : (completedProgress / totalProgress) * 100;

        progressBar.style.width = `${progress}%`;
        progressText.textContent = `${Math.round(progress)}%`;
    }

    // Function to add a new task
    function addTask(formData) {
        fetch('add_task.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchTasks();  // Refresh the task list after adding
                taskForm.reset();
                subtaskContainer.innerHTML = '<input type="text" class="subtaskInput" placeholder="Subtask 1">';
                alert('Task added successfully');
            } else {
                alert('Failed to add task: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Function to calculate task priority
    function calculatePriority(deadline) {
        const daysLeft = Math.ceil((deadline - new Date()) / (1000 * 60 * 60 * 24));
        if (daysLeft <= 1) return 'High';
        if (daysLeft <= 3) return 'Medium';
        return 'Low';
    }

    // Function to calculate hours left until deadline
    function calculateHoursLeft(deadline) {
        const now = new Date();
        const hoursLeft = Math.ceil((deadline - now) / (1000 * 60 * 60));
        return hoursLeft > 0 ? hoursLeft : 0;
    }

    // Function to toggle task completion
    function toggleTaskCompletion(index) {
        tasks[index].completed = !tasks[index].completed;
        tasks[index].subtasks.forEach(subtask => subtask.completed = tasks[index].completed);
        saveTasks();
        renderTasks();
    }

    // Function to toggle subtask completion
    function toggleSubtaskCompletion(taskIndex, subtaskIndex) {
        tasks[taskIndex].subtasks[subtaskIndex].completed = !tasks[taskIndex].subtasks[subtaskIndex].completed;
        tasks[taskIndex].completed = tasks[taskIndex].subtasks.every(subtask => subtask.completed);
        saveTasks();
        renderTasks();
    }

    // Function to delete a task
    function deleteTask(index) {
        tasks.splice(index, 1);
        saveTasks();
        renderTasks();
    }

    // Function to save tasks to local storage
    function saveTasks() {
        localStorage.setItem('tasks', JSON.stringify(tasks));
    }

    // Event listener for task form submission
    taskForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(taskForm);
        addTask(formData);
    });

    // Event listener for adding new subtasks
    addSubtaskButton.addEventListener('click', () => {
        const subtaskCount = subtaskContainer.getElementsByClassName('subtaskInput').length;
        const newSubtask = document.createElement('input');
        newSubtask.type = 'text';
        newSubtask.className = 'subtaskInput';
        newSubtask.placeholder = `Subtask ${subtaskCount + 1}`;
        subtaskContainer.appendChild(newSubtask);
    });

    // Event listener for task and subtask interactions
    taskList.addEventListener('change', (e) => {
        if (e.target.classList.contains('completeTask')) {
            const index = e.target.dataset.index;
            toggleTaskCompletion(index);
        }
        if (e.target.classList.contains('completeSubtask')) {
            const taskIndex = e.target.dataset.taskIndex;
            const subtaskIndex = e.target.dataset.subtaskIndex;
            toggleSubtaskCompletion(taskIndex, subtaskIndex);
        }
    });

    // Event listener for task deletion
    taskList.addEventListener('click', (e) => {
        if (e.target.classList.contains('deleteTask')) {
            const index = e.target.dataset.index;
            deleteTask(index);
        }
    });

    // Initial fetch and render of tasks
    fetchTasks();

    function updateProgress() {
        // Calculate total number of tasks and subtasks
        const totalTasks = tasks.length;
        const totalSubtasks = tasks.reduce((acc, task) => acc + task.subtasks.length, 0);
    
        // Calculate completed tasks and subtasks
        const completedTasks = tasks.filter(task => task.completed).length;
        const completedSubtasks = tasks.reduce((acc, task) => acc + task.subtasks.filter(subtask => subtask.completed).length, 0);
    
        // Total progress calculation
        const totalProgress = totalTasks + totalSubtasks;
        const completedProgress = completedTasks + completedSubtasks;
        const progress = totalProgress === 0 ? 0 : (completedProgress / totalProgress) * 100;
    
        // Update progress bar
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }
        if (progressText) {
            progressText.textContent = `${Math.round(progress)}%`;
        }
    }
    
    // Function to delete a task
    function deleteTask(index) {
        const taskId = tasks[index].id; // Assuming tasks have an 'id' field

        fetch('delete_task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: taskId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tasks.splice(index, 1); // Remove task from the local list
                renderTasks(); // Re-render the task list
                updateProgress(); // Update the progress bar
            } else {
                alert('Failed to delete task: ' + data.message);
            }
        })
        .catch(error => console.error('Error deleting task:', error));
    }

});
