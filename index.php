<?php require 'partials/header.php' ?>


<?php 

$user_name = '';
$user_surname = '';
$age = '';
$role = '';

//pripojenie na datbazu - objektovo
class Database{

    //properies(pemenne)
    public $server_name = '';
    public $login_name = '';
    public $login_password = '';
    public $db_name = '';
    public $connection = null;

    function __construct($server_name, $login_name, $login_password, $db_name){
        $this->server_name = $server_name;
        $this->login_name = $login_name;
        $this->login_password = $login_password;
        $this->db_name = $db_name;
    }

    function connectToDb(){
        $this->connection = mysqli_connect($this->server_name, $this->login_name, $this->login_password, $this->db_name);
        
        if (!$this->connection) {
            echo 'Spojenie s databázou sa nepodarilo nadviazať.';
        }else{
            //echo 'Spojenie s databázou sa podarilo úspešne nadviazať.';
        }
    }
}

$db = new Database('localhost', 'root', '', 'test_db_1');
$db->connectToDb();


//ZACHYTENIE PARAMETROV
if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['user_form']){
        $user_name = $_POST['user_name'];
        $user_surname = $_POST['user_surname'];
        $age = $_POST['age'];
        $role = $_POST['role'];
        
        if(preg_match('#^.{0,50}$#', $user_name) &&
        preg_match('#^.{1,50}$#', $user_surname) &&
        preg_match('#^.{0,20}$#', $role) &&
        preg_match('#^[0-9]{0,3}$#', $age)){
            
            
            //ulozenie do DB

            $sql_query = "INSERT INTO users (user_name, user_surname, age, role) VALUES (";
            // otestovanie či prišli parametre inak vkladame NULL

            if($user_name){
                $sql_query .="'".$user_name."',";
            }else{
                $sql_query .="NULL,";
            }

            $sql_query .= "'".$user_surname."',";
                    
            if($age){
                $sql_query .= $age.",";
            }else{
                $sql_query .= "NULL,";
            }

            if($role){
                $sql_query .= "'".$role."')";
            }else{
                $sql_query .= "NULL)";
            }


           $result = mysqli_query($db->connection, $sql_query);

            if($result){
                echo '<p class="success">Položka bola úspešne uložená.</p>';
            }else{
                echo '<p class="error">Pri ukladaní dát do databzy sa vyskytla chyba.</p>';
            }


            //vycistenie poloziek
            $user_name = '';
            $user_surname = '';
            $age = '';
            $role = '';
        }else{
            //chybne data
            echo '<p class="error">Nová položka nebola uložená - nesprávne vyplnené dáta vo formulári. </p>';
        }
    }

    //MAZANIE POLOZKY
    $id_user = $_GET['id_user'];
    if($id_user){
        $sql_query = "DELETE FROM users WHERE id=".$id_user;
        $result = mysqli_query($db->connection, $sql_query);

        if($result){
            echo '<p class="success">Položka bola úspešne zmazaná.</p>';

            //vycistenie poloziek
            $user_name = '';
            $user_surname = '';
            $age = '';
            $role = '';
        }else{
            echo '<p class="error">Pri mazaní položky sa vyskytla chyba.</p>';
        }
    }

?>

<!-- Formular pre vlozenie noveho usera -->

<h2>Pridanie nového používateľa</h2>

<form class="contact_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <label for="user_name">Meno</label>
    <input type="text" name="user_name" id="user_name" value="<?php echo $user_name;?>">

    <label for="user_surname">Priezvisko</label>
    <input type="text" name="user_surname" id="user_surname" value="<?php echo $user_surname;?>">

    <br>
    <label for="age">Vek</label>
    <input type="text" name="age" id="age" value="<?php echo $age;?>">

    <label for="role">Rola</label>
    <input type="text" name="role" id="role" value="<?php echo $role?>">
    <br>
    <input type="submit" name="user_form" value="Odoslať">
</form>

<br>



<?php
//filtrovanie usera
    $search_keyword = "";
    if($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['search_form']){
        $search_keyword = $_GET['search_keyword'];
    }

    $sql_query = "SELECT * FROM users ";

    if($search_keyword){
        $sql_query .= " WHERE user_name LIKE '%".$search_keyword."%' OR user_surname LIKE '%".$search_keyword."%'";
    }

    //zachytenie parametra pre triedenie v tabulke
    $sort_by = $_GET['sort_by'];
    $sort_type = $_GET['sort_type'];

    if($sort_by){
        $sql_query .= " ORDER BY ".$sort_by;

        if($sort_type){
            $sql_query .=" ".$sort_type;
        }else{
            $sql_query .="ASC";
        }

    }

    $result = mysqli_query($db->connection, $sql_query);

    echo '<h2>Zoznam používateľov</h2>';

?>

<!-- Formular na filtrovanie usera -->
<form class="contact_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="get">
    <label for="search_keyword">Meno</label>
    <input type="text" name="search_keyword" id="search_keyword" value="<?php echo $search_keyword;?>">

    <input type="hidden" name="sort_by" value="<?php echo $sort_by;?>">
    <input type="hidden" name="sort_type" value="<?php echo $sort_type;?>">

    <input type="submit" name="search_form" value="Filtruj">
</form>




<div class="table table-dark">
<?php 

    if(mysqli_num_rows($result) > 0){
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        //echo '<pre>';
        //print_r($data);
        //echo '<pre>';

        echo '<table class="persons">';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th><a href="index.php?sort_by=user_name';
        if($sort_by == 'user_name'){
            if($sort_type == 'ASC'){
                echo '&sort_type=DESC';
            }else{
                echo '&sort_type=ASC';
            }
        }else{
            echo '&sort_type=ASC';
        }

        if($search_keyword){
            echo '&search_keyword='.$search_keyword.'&search_form=Filtruj';
        }
        echo '">Meno</a></th>';

        echo '<th><a href="index.php?sort_by=user_surname';
        if($sort_by == 'user_surname'){
            if($sort_type == 'ASC'){
                echo '&sort_type=DESC';
            }else{
                echo '&sort_type=ASC';
            }
        }else{
            echo '&sort_type=ASC';
        }

        if($search_keyword){
            echo '&search_keyword='.$search_keyword.'&search_form=Filtruj';
        }
        echo '">Priezvisko</a></th>';     
        echo '<th>Vek</th>';
        echo '<th>Rola</th>';
        echo '</tr>';
            
        for($i=0; $i<count($data); $i++){
            echo'<tr>';
            foreach($data[$i] as $index => $value){
                echo '<td>'.$value.'</td>';
            }
            echo '<td><a href ="uprava-zaznamu.php?id-user='.$data[$i]['id'].'" > Upraviť </a></td>';
            echo '<td><a href="index.php?id_user='.$data[$i]['id'].'"onclick="return showConfirm()">Vymazať</a></td>';
            echo'<tr>';
        }
        echo '</table>';
                
    }else{
        echo "0 results were selected.";
    }   

    mysqli_close($db->connection);
?>
</div>


<?php require 'partials/footer.php' ?>