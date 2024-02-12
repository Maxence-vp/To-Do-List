<?php

//session_start();

abstract class AbstractTaskManager
{
    abstract public function addTask(string $task); // Ajout d’une tache
    abstract public function delTask(int $id); // Supprime la tâche en base
    abstract public function updateTaskStatus(int $id, string $status); //adapte une classe en fonction de son état
    abstract public function getAllTasks(); // Récupère ts les tâches en cours
}

class TaskManager extends AbstractTaskManager
{
    private $_id; // l’identifiant unique de la tâche
    private $_name; // le nom de la tâche
    private $_status; // le status de la tâche 
    private $_db; // l’instance de la connexion à la base de données

    // $dataBase = new DataBase 
    public function __construct()
    {
        $this->_db = new DataBase;
    }

    public function setDb($db)
    {
        $this->_db = $db;
    }

    public function getDb()
    {
        return $this->_db;
    }

    public function setId(int $id)
    {
        if (is_int($id) && $id > 0) {
            $this->_id = $id;
        }
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setName(string $name)
    {
        if (is_string($name) && strlen($name) > 0) {
            $this->_name = $name;
        }
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setStatus(int $id, string $status)
    {
        if (is_int($id) && $id > 0 && is_string($status) && strlen($status) > 0) {
            $this->_id = $id;
            $this->_status = $status;
        }
    }

    public function getStatus()
    {
        return $this->_status;
    }


    public function addTask(string $name)
    {
        if (!empty($name)) {
            try {
                $addTask = "INSERT INTO `to-do-list` (`task`) VALUES (:task)";
                $stmtAdd = $this->_db->prepare($addTask);
                $stmtAdd->bindParam(':task', $name, PDO::PARAM_STR);
                $stmtAdd->execute();

                if ($stmtAdd->rowCount() > 0) {
                    $_SESSION['addTaskMsg'] = "👍 The task was added successfully!";
                } else {
                    $_SESSION['addTaskMsg'] = "😬 Error, task was not added, please try again!";
                }
            } catch (PDOException $e) {
                $errorMessage = "😬 Database error : " . $e->getMessage();
                $_SESSION['addTaskMsg'] = $errorMessage;
                // Enregistrer l'erreur dans un fichier journal
                error_log($errorMessage);
            }
        } else {
            $_SESSION['addTaskMsg'] = "😬 Please specify a task to add!";
        }
        // Afficher le message à l'utilisateur
        echo "<script>alert('" . $_SESSION['addTaskMsg'] . "');</script>";
    }

    public function updateTaskStatus(int $id, string $status)
    {
        if (is_int($id) && $id > 0 && is_string($status) && strlen($status) > 0) {
            try {
                $updateTaskStatus = "UPDATE `to-do-list` SET status=:status WHERE id=:id";
                $stmtUpdate = $this->_db->prepare($updateTaskStatus);
                $stmtUpdate->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':status', $status, PDO::PARAM_STR);

                if ($stmtUpdate->execute()) {
                    $_SESSION['updateTaskStatusMsg'] = "👍 The task status has been successfully updated!";
                } else {
                    $_SESSION['updateTaskStatusMsg'] = "😬 Error, task status was not updated, please try again!";
                }

                // Mettre à jour la propriété locale après la mise à jour réussie dans la base de données
                $this->_status = $status;

            } catch (PDOException $e) {
                $errorMessage = "😬 Database error: " . $e->getMessage();
                $_SESSION['updateTaskStatusMsg'] = $errorMessage;
                // Enregistrer l'erreur dans un fichier journal
                error_log($errorMessage);
            }
        }

        // Afficher le message à l'utilisateur
        echo "<script>alert('" . $_SESSION['updateTaskStatusMsg'] . "');</script>";
    }


    public function delTask(int $id)
    {
        try {
            $deleteTask = "DELETE FROM `to-do-list` WHERE id=:id";
            $stmtDelete = $this->_db->prepare($deleteTask);
            $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmtDelete->execute()) {
                $_SESSION['deleteTaskMsg'] = "👍 The task was successfully deleted!";
            } else {
                $_SESSION['deleteTaskMsg'] = "😬 Error, task was not deleted, please try again!";
            }
        } catch (PDOException $e) {
            $errorMessage = "😬 Database error: " . $e->getMessage();
            $_SESSION['deleteTaskMsg'] = $errorMessage;
            // Enregistrer l'erreur dans un fichier journal
            error_log($errorMessage);
        }
        // Afficher le message à l'utilisateur
        echo "<script>alert('" . $_SESSION['deleteTaskMsg'] . "');</script>";
    }



    public function getAllTasks()
    {
        $showTasks = "SELECT * FROM `to-do-list`";
        $stmtShow = $this->_db->prepare($showTasks);
        $stmtShow->execute();
        $arrResult = $stmtShow->fetchAll(PDO::FETCH_OBJ);

        $arrTasks = [];
        foreach ($arrResult as $elem) {
            $obj = new TaskManager();
            $obj->setId($elem->id);
            $obj->setName($elem->task);
            $obj->setStatus($elem->id, $elem->status);
            $arrTasks[] = $obj;
        }

        return $arrTasks;
    }
}
