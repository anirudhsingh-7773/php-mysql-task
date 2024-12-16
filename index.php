<?php

/**
 * Class TaskLoader
 *
 * Responsible for loading a specific task based on the given task number.
 */
class TaskLoader {

  /**
   * The current task number.
   *
   * @var int
   */
  private $taskNumber;

  /**
   * Constructs a TaskLoader object.
   *
   * @param int $taskNumber
   *   The task number to load.
   */
  public function __construct($taskNumber) {
    $this->taskNumber = $taskNumber;
  }

  /**
   * Loads the task based on the task number.
   */
  public function loadTask() {
    // Map tasks to their respective files.
    $tasks = [
      1 => 'task1.php',
      2 => 'task2.php',
      3 => 'task3.php',
      4 => 'task4.php',
      5 => 'task5.php',
      6 => 'task6.php',
    ];

    // Check if the task number exists in the mapping.
    if (isset($tasks[$this->taskNumber])) {
      include $tasks[$this->taskNumber];
    }
    else {
      // Display an error message if the task number is invalid.
      echo "<h1>Invalid Task</h1>";
    }
  }

}

// Default task number is 4.
// @var int $taskNumber
$taskNumber = isset($_GET['q']) ? (int) $_GET['q'] : 4;

// Create an instance of TaskLoader and load the task.
// @var \TaskLoader $taskLoader
$taskLoader = new TaskLoader($taskNumber);
$taskLoader->loadTask();
