<?php
session_start();
require_once "functions.php";

connectDB();
$stmt = $conn->stmt_init();

// add a task
if (isset($_POST["addtask"]) ) {
    $taskName = sanitizeMySql($conn, $_POST["taskname"]);
    $prio = $_POST["prio"];
    $query = "INSERT INTO tasks VALUES ('', '{$taskName}', 0, '{$prio}')";
    if ($stmt->prepare($query) ) {
        $stmt->execute();
        // förhindrar att uppgiften läggs till flera ggr
        $sort = $_SESSION["sort"];
        header("Location: index.php?taskAdded".$_SESSION['sort']);
    }
}

// complete/uncomplete a task
if (isset($_GET["complete"]) ) {
    $taskToComplete = $_GET["complete"];
    $getStat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT complete FROM tasks WHERE id = '{$taskToComplete}'"));
    $completeStatus = $getStat["complete"];
    // change the status
    $completeStatus =! $completeStatus;
    $query = "UPDATE tasks SET complete = '{$completeStatus}' WHERE id = '{$taskToComplete}'";
    if ($stmt->prepare($query) ) {
        $stmt->execute();
        $task = "?taskComplete=true";
        $sort = $_SESSION["sort"];
        header ("Location: index.php"."$task"."$sort");
    }
}
include_once "includes/header.html";

// delete a task
if (isset($_GET["delete"]) ) {
    $taskToDelete = $_GET["delete"];
    $query = "DELETE FROM tasks WHERE id = '{$taskToDelete}'";
    if ($stmt->prepare($query) ) {
        $stmt->execute(); ?>
        <div class="hideMe">
          <p>Task deleted</p>
        </div> <?php
    }
}

if (isset($_GET["taskAdded"]) ) { ?>
  <div class="hideMe">
    <p>Task added</p>
  </div> <?php
}

// sort the list
$sort = "";
if (isset($_GET["sort"]) ) {
  $sort = $_GET["sort"];
}
$qm = "?";
if (isset($_GET["delete"]) || isset($_GET["taskAdded"]) || isset($_GET["taskComplete"])) {
    $qm = "";
}
if ($sort == "name") {
        $query = "SELECT * FROM tasks ORDER BY taskName";
        $_SESSION["sort"] = $qm."&sort=name";
    } elseif ($sort == "desc") {
        $query = "SELECT * FROM tasks ORDER BY priority DESC";
        $_SESSION["sort"] = $qm."&sort=desc&order=desc";
    } elseif ($sort == "asc") {
        $query = "SELECT * FROM tasks ORDER BY priority ASC";
        $_SESSION["sort"] = $qm."&sort=asc&order=asc";
    } elseif ($sort == "done") {
        $query = "SELECT * FROM tasks ORDER BY complete";
        $_SESSION["sort"] = $qm."&sort=done";
    } else {
        $query = "SELECT * FROM tasks";
}


if ($stmt->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($id, $taskName, $complete, $priority);
}

// save the list in an array and count tasks
$todo = 0;
$done = 0;
$todoList = NULL;
while (mysqli_stmt_fetch($stmt)) {
    if ($complete == 0) {
      $todo++;
    } else {
      $done++;
    }
    $todoList[] = array("id" => $id, "taskname" => $taskName, "complete" => $complete, "priority" => $priority);
}
// close db connections
$stmt->close();
$conn->close();

include_once "./includes/header.html";

// link-menu to sort the list
 ?>

  <div class="container">
    <p class="sort">Sort:
        <a href="index.php?sort=name">Name</a> |
        <a href="index.php?sort=asc&order=asc">Most important</a> |
        <a href="index.php?sort=desc&order=desc">Least important</a> |
        <a href="index.php?sort=none">None</a>
    </p>

    <form action="index.php" method="post" class="addtask">
      <input type="text" name="taskname" placeholder="New task">
      <select name="prio">
        <option value="1">High</option>
        <option value="2" selected="selected">Normal prio</option>
        <option value="3">Low</option>
      </select>
      <input type="submit" name="addtask" value="Add" class="btn">
    </form>

  <?php
    $count = 0;
    // loop through the todo list!
    echo "<h3>To do: $todo</h3>";
    printList(0, $todoList, $count);
    echo "<h3>Done: $done</h3>";
    printList(1, $todoList, $count); ?>

  </div>

</body>
</html>
