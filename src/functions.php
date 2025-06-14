<?php

function getAllTasks() {
    if (!file_exists('tasks.txt')) return [];

    $content = file_get_contents('tasks.txt');
    $tasks = json_decode($content, true);
    return is_array($tasks) ? $tasks : [];
}

function saveTasks($tasks) {
    file_put_contents('tasks.txt', json_encode($tasks, JSON_PRETTY_PRINT));
}

function generateUniqueId() {
    return uniqid();
}

function addTask($task_name) {
    $task_name = trim($task_name);
    if ($task_name === '') return;

    $tasks = getAllTasks();

    foreach ($tasks as $task) {
        if (strtolower($task['name']) === strtolower($task_name)) {
            return;
        }
    }

    $tasks[] = [
        "id" => generateUniqueId(),
        "name" => $task_name,
        "completed" => false
    ];

    saveTasks($tasks);
}

function markTaskAsCompleted($task_id, $is_completed) {
    $tasks = getAllTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
            break;
        }
    }
    saveTasks($tasks);
}

function deleteTask($task_id) {
    $tasks = getAllTasks();
    $tasks = array_filter($tasks, fn($task) => $task['id'] !== $task_id);
    saveTasks(array_values($tasks));
}

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function subscribeEmail($email) {
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return;

    $pending_file = 'pending_subscriptions.txt';
    $subscribers_file = 'subscribers.txt';

    $pending = file_exists($pending_file) ? json_decode(file_get_contents($pending_file), true) : [];
    $subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];

    if (in_array($email, $subscribers)) return;

    $code = generateVerificationCode();
    $pending[$email] = [
        "code" => $code,
        "timestamp" => time()
    ];

    file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT));

    $verify_link = "http://localhost/task-scheduler/verify.php?email=" . urlencode($email) . "&code=" . $code;

    $subject = "Verify subscription to Task Planner";
    $headers = "From: no-reply@example.com\r\nContent-Type: text/html\r\n";

    $body = '
        <p>Click the link below to verify your subscription to Task Planner:</p>
        <p><a id="verification-link" href="' . $verify_link . '">Verify Subscription</a></p>
    ';

    // REAL MAIL FUNCTION – as required by README ✅
    mail($email, $subject, $body, $headers);
}

function verifySubscription($email, $code) {
    $pending_file = 'pending_subscriptions.txt';
    $subscribers_file = 'subscribers.txt';

    $pending = file_exists($pending_file) ? json_decode(file_get_contents($pending_file), true) : [];
    $subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];

    if (!isset($pending[$email])) return false;

    if ($pending[$email]['code'] === $code) {
        unset($pending[$email]);
        if (!in_array($email, $subscribers)) {
            $subscribers[] = $email;
        }

        file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT));
        file_put_contents($subscribers_file, json_encode($subscribers, JSON_PRETTY_PRINT));
        return true;
    }

    return false;
}

function unsubscribeEmail($email) {
    $subscribers_file = 'subscribers.txt';

    $subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];

    $subscribers = array_filter($subscribers, fn($e) => $e !== $email);

    file_put_contents($subscribers_file, json_encode(array_values($subscribers), JSON_PRETTY_PRINT));
}

function sendTaskReminders() {
    $subscribers_file = 'subscribers.txt';
    $subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];

    $tasks = array_filter(getAllTasks(), fn($t) => !$t['completed']);

    foreach ($subscribers as $email) {
        sendTaskEmail($email, $tasks);
    }
}

function sendTaskEmail($email, $pending_tasks) {
    $subject = "Task Planner - Pending Tasks Reminder";
    $headers = "From: no-reply@example.com\r\nContent-Type: text/html\r\n";

    $unsubscribe_link = "http://localhost/task-scheduler/unsubscribe.php?email=" . urlencode($email);

    $body = "<h2>Pending Tasks Reminder</h2>";
    $body .= "<p>Here are the current pending tasks:</p><ul>";

    foreach ($pending_tasks as $task) {
        $body .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }

    $body .= "</ul><p><a id='unsubscribe-link' href='$unsubscribe_link'>Unsubscribe from notifications</a></p>";

    // REAL MAIL FUNCTION
    mail($email, $subject, $body, $headers);
}
