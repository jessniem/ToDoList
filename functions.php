<?php

/**
* The function create connection to db, utf8_encoded.
*
* @return string Returns error message if connection not ok.
**/
function connectDB() {
  global $conn;
  $conn = new mysqli("localhost", "root", "root", "inl2");
  $conn->set_charset("utf8");

  if ($conn->connect_errno) {
      return "<p>Failed to connect to database";
      die();
  }
}

/**
* The function prevent escape characters to be injected in the strings presented to MySQL.
*
* @param string $var The string from the user input. TODO ????
* @param string $conn TODO ???
* @return string $var Return a safe sanitized string.
**/
function sanitizeMySql($conn, $var) {
  $var = $conn->real_escape_string($var);
  $var = sanitizeString($var);
  return $var;
}
/**
* The function removes unwanted slashes and HTML from user input.
*
* @param string $var The string from the user input.
* @return string $var Return a safe sanitized string.
**/
function sanitizeString($var) {
  $var = stripslashes($var);
  $var = strip_tags($var);
  $var = htmlentities($var);
  return $var;
}

/**
* The function takes the todoList-array and presents it in a table and sets the class done on completed tasks
*
* @param bool $complete If the task is done or not
* @param array $todoList Read the array and print out the tasks
* @return int $count Return the number of tasks
**/
function printList($complete, $todoList, $count) {
  if ($todoList == NULL)  {
      exit;
  }?>
  <table> <?php
  foreach ($todoList as $item) {
      $class = "";
      if ($complete == 1) {
          $class = "done";
      }
      if ($complete == $item["complete"]) { ?>
          <tr class="<?php echo $class; ?> prio<?php echo $item["priority"]; ?>">
            <td><?php echo $item["taskname"]; ?></td>
            <td class="button"><a href="index.php?complete=<?php echo $item["id"] . $_SESSION["sort"]; ?>"><i class="fa fa-check" aria-hidden="true"></i></a></td>
            <td class="button"><a href="index.php?delete=<?php echo $item["id"] . $_SESSION["sort"]; ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>
          </tr> <?php
      }
  }
  ?>
  </table> <?php
  return $count;
 }


 ?>
