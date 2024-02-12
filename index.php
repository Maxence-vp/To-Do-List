 <?php

session_start(); // Initialiser les sessions

function chargerClasse($classe)
{
  require $classe . '.class.php';
}
spl_autoload_register('chargerClasse');

$dataBase = new DataBase();

$taskManager = new TaskManager();

if (isset($_POST["add-task"])) {
  $taskManager->addTask($_POST["task"]);
}

if (isset($_GET['done-task']) && isset($_GET['status'])) {
  $id = $_GET['done-task'];
  $status = $_GET['status'];
  $taskManager->updateTaskStatus($id, $status);
}


if (isset($_GET['delete-task'])) {
  $id = $_GET['delete-task'];

  $taskManager->delTask($id);
}

$tasks = $taskManager->getAllTasks();
error_log(print_r($tasks, 1));

?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-nU14brUcp6StFntEOOEBvcJm4huWjB0OcIeQ3fltAfSmuZFrkAif0T+UtNGlKKQv" crossorigin="anonymous">

  <link rel="stylesheet" href="style.css">

  <title>to do list</title>

</head>

<body>
  <section id="section" class="vh-100">
    <div class="container py-5 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col col-lg-9 col-xl-7">
          <div id="shadow" class="card rounded-3">
            <div class="card-body p-4">

              <h4 class="text-center my-3 pb-3">MY SUPER TO DO LIST</h4>

              <form action="index.php" method="post" class="row mb-3 pb-3">
                <div class="text-center">
                  <div>
                    <input type="text" id="input-add-task" class="form-control" name="task" placeholder="Enter a task here" />
                    <br>
                    <label class="form-label visually-hidden">Enter a task here</label>
                    <button id="btn-add-task" type="submit" name="add-task" class="btn btn-primary">&#9997;</button>
                  </div>
                  <br>
              </form>
              <table class="table mb-4">
                <thead>
                  <tr>
                    <th scope="col">No.</th>
                    <th scope="col">TASK</th>
                    <th scope="col">STATUS</th>
                    <th scope="col">ACTIONS</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 0;
                  $allTasks = $tasks;
                  if(!empty($allTasks)) {
                    foreach ($allTasks as $task) {
                      $i++; // incr du numéro de la tâche
                    // Définition de la classe CSS en fonction du statut de la tâche
                    $statusClass = '';
                    if ($task->getStatus() == 'in progress') {
                      $statusClass = 'text-warning'; // Utilisation de la classe Bootstrap pour la couleur orange
                    } elseif ($task->getStatus() == 'completed') {
                      $statusClass = 'text-success'; // Utilisation de la classe Bootstrap pour la couleur verte
                    }
                    // Définition de la classe CSS pour le texte rayé si la tâche est complétée
                    $completedClass = ($task->getStatus() == 'completed') ? 'text-decoration-line-through' : '';
                    // Détermination de la visibilité du lien success
                    $successLinkVisibility = ($task->getStatus() == 'completed') ? 'd-none' : '';
                  ?>
                    <tr>
                      <th scope="row"><?php echo $i ?></th>
                      <td class="task <?php echo $completedClass ?>"><?php echo $task->getName() ?></td>
                      <td class="status <?php echo $statusClass ?>"><?php echo $task->getStatus() ?></td>
                      <td>
                        <a class="badge bg-success link-light link-offset-2 link-underline-opacity-0 <?php echo $successLinkVisibility ?>" style="width:40%" href="index.php?done-task=<?php echo $task->getId(); ?>&status=completed">✓</a>&nbsp;&nbsp;
                        <a class="badge bg-danger link-light link-offset-2 link-underline-opacity-0" style="width:40%" href="index.php?delete-task=<?php echo $task->getId(); ?>">✗</a>
                      </td>
                    </tr>
                  <?php 
                  }
                }
                  ?>  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>