<h2>Formular</h2>
<form action="add-new.php" method="post">
    <label for="user_name">Meno</label>
    <input type="text" name="user_name" id="user_name" value="<?php echo $_POST['user_name'];?>"><br><br>

    <label for="user_surname">Priezvisko</label>
    <input type="text" name="user_surname" id="user_surname" value="<?php echo $_POST['user_surname'];?>"><br><br>
    <input type="submit" value="Odoslat">
</form>

<?php

if($_POST['user_name'] || $_POST['user_surname']){
    echo 'Meno pouzivatela je : <b>'.$_POST['user_name']. '</b> a priezvisko : <b>'.$_POST['user_surname'].'</b>.';
}
?>