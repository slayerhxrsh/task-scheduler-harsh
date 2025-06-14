<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name'])) {
        addTask($_POST['task-name']);
    }

    if (isset($_POST['toggle-task'])) {
        markTaskAsCompleted($_POST['toggle-task'], isset($_POST['completed']));
    }

    if (isset($_POST['delete-task'])) {
        deleteTask($_POST['delete-task']);
    }

    if (isset($_POST['email'])) {
        subscribeEmail($_POST['email']);
    }
}

$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Planner</title>
  <style>
    body {
      font-family: sans-serif;
      padding: 20px;
      background: #f2f2f2;
    }
    .task-list {
      list-style: none;
      padding: 0;
    }
    .task-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fff;
      padding: 10px;
      margin-bottom: 8px;
      border-radius: 4px;
    }
    .completed {
      text-decoration: line-through;
      opacity: 0.6;
    }
    .delete-task {
      background: red;
      color: white;
      border: none;
      padding: 5px 8px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <h1>Task Planner 📝</h1>

  <!-- ADD TASK FORM -->
  <form method="POST">
    <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
    <button type="submit" id="add-task">Add Task</button>
  </form>

  <!-- TASK LIST -->
  <ul class="task-list">
    <?php foreach ($tasks as $task): ?>
      <li class="task-item<?= $task['completed'] ? ' completed' : '' ?>">
        <form method="POST" style="display:inline;">
          <input type="hidden" name="toggle-task" value="<?= htmlspecialchars($task['id']) ?>">
          <input type="checkbox" class="task-status" name="completed" <?= $task['completed'] ? 'checked' : '' ?> onchange="this.form.submit()">
        </form>
        <?= htmlspecialchars($task['name']) ?>
        <form method="POST" style="display:inline;">
          <button class="delete-task" name="delete-task" value="<?= htmlspecialchars($task['id']) ?>">Delete</button>
        </form>
      </li>
    <?php endforeach; ?>
  </ul>

  <!-- EMAIL SUBSCRIPTION FORM -->
  <h2>Subscribe for Reminders 📧</h2>
  <form method="POST">
    <input type="email" name="email" required />
    <button type="submit" id="submit-email">Submit</button>
  </form>

</body>
</html>
