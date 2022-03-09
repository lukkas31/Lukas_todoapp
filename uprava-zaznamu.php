<?php include "partials/header.php" ?>
<h2> Uprava dát </h2>
<div class="edit-table">
    <?php 
    
    $id_user = $_GET['id-user'];


    //PRIPOJENIE DO DB
    $server_name = "localhost";
    $db_user_name = "root";
    $password = "";
    $dbname = "test_db_1";

    $connection = mysqli_connect($server_name, $db_user_name, $password, $dbname);

    if (!$connection) {
        echo '<p class="error">Spojenie s databázou sa nepodarilo nadviazať.</p>';
    }

    //ZACHYTENIE PARAMETROV
if($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['edit_user_form']){
    $user_name = $_GET['user_name'];
    $user_surname = $_GET['user_surname'];
    $age = $_GET['age'];
    $role = $_GET['role'];
    
    if(preg_match('#^.{0,50}$#', $user_name) &&
    preg_match('#^.{1,50}$#', $user_surname) &&
    preg_match('#^.{0,20}$#', $role) &&
    preg_match('#^[0-9]{0,3}$#', $age)){
        
        
        //ulozenie do DB

        $sql_query = "UPDATE users SET user_name=";
        // otestovanie či prišli parametre inak vkladame NULL

        if($user_name){
            $sql_query .="'".$user_name."',";
        }else{
            $sql_query .="NULL,";
        }

        $sql_query .= " user_surname='".$user_surname."',";
                
        if($age){
            $sql_query .=" age='".$age."',";
        }else{
            $sql_query .= " age='NULL',";
        }
        

        if($role){
            $sql_query .=" role='".$role."'";
        }else{
            $sql_query .= " role='NULL'";
        }

        $sql_query .=" WHERE id = ".$id_user.";";

       $result = mysqli_query($connection, $sql_query);


    

        if($result){
            echo '<p class="success">Položka bola úspešne uložená.</p>';
        }else{
            echo '<p class="error">Pri ukladaní dát do databzy sa vyskytla chyba.</p>';
        }

    }else{
        //CHYBNE DATA
        
        echo '<p class="error">Nová položka nebola uložená - nesprávne vyplnené dáta vo formulári. </p>';
    }
}


//NACITANIE DAT O USEROVI
    if($id_user){
        $sql_query = "SELECT * FROM users WHERE id=".$id_user.";";

        $result = mysqli_query($connection, $sql_query);

        if (mysqli_num_rows($result) > 0) {

            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $user_name = $data[0]['user_name'];
            $user_surname = $data[0]['user_surname'];
            $age = $data[0]['age'];
            $role = $data[0]['role'];

        }else{
            echo '<p class="error">Nepodarilo sa načítať dáta pre editovanie.</p>';
        }


    }else{
        echo '<p class="error">Parameter používateľa pre editáciu sa nepodarilo načítať.</p>';
    }
?>
<!-- Formular pre editovanie usera -->
<form class="edit_user_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <label for="user_name">Meno</label>
    <input type="text" name="user_name" id="user_name" value="<?php echo $user_name;?>">

    <label for="user_surname">Priezvisko</label>
    <input type="text" name="user_surname" id="user_surname" value="<?php echo $user_surname;?>">

    <label for="age">Vek</label>
    <input type="text" name="age" id="age" value="<?php echo $age;?>">

    <label for="role">Rola</label>
    <input type="text" name="role" id="role" value="<?php echo $role;?>">

    <input type="hidden" name="id_user" value="<?php echo $id_user;?>">

    <input type="submit" name="edit_user_form" value="Odoslať">
</form>
</div>