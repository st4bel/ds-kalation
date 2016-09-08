<div class="wrap">
    <script src="admin_page.js"></script>
    <script>
        function test(){
            alert('Hello');
        }
    </script>
    <h2>DS Kalation</h2>

    <h3>Gruppe erstellen</h3>
    <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "wordpress";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $group_name = $_POST['group'];
        $sql = "INSERT INTO groups (`sessionid`, `name`) VALUES ('new', '".$group_name."')";
        if($group_name!=""){
            if ($conn->query($sql) === TRUE) {
                sleep(1);

                $sql = "INSERT INTO members (user_id,group_id) VALUES (".get_current_user_id().",".$conn->insert_id.")";
                if ($conn->query($sql) === TRUE) {
                    echo "<script>
                        alert('Gruppe erstellt!');
                        window.location.reload();
                    </script>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }


    ?>
    <form method="post" action="">
        <input type="text" name="group">
        <input class="button action" type="submit" value="Gruppe erstellen">
    </form>
    <?php


        // Create connection

        echo "Connected successfully"."<br>";

        echo "<h3>Die haben Zugang zu den Gruppen:</h3><br>";
        $sql = "SELECT * FROM groups,members WHERE groups.id = members.group_id and members.user_id = ".get_current_user_id();
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $member_count[$row["group_id"]] = 0;
                $sql = "SELECT * FROM members,wp_ds_kalationusers WHERE members.user_id = wp_ds_kalationusers.ID and members.group_id = ".$row["group_id"];
                $result2 = $conn->query($sql);
                echo "Name <b>" . $row["name"]. "</b><br>
                Spieler:
                <ol>";
                while($row2 = $result2->fetch_assoc()){
                    echo "<li><b>".$row2["user_login"]."</b></li>";
                    $member_count[$row["group_id"]] = $member_count[$row["group_id"]]+1;
                }
                echo "</ol>
                <form method='post' action=''>
                    <button name='action' value='leave".$row["id"]."'>Austreten</button>
                    <input type='text' name='invite_user_name' value='Loginname'>
                    <button name='action' value='invite".$row["id"]."'>Einladen</button>
                </form>";
            }
        } else {
            echo "0 results";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // collect value of input field
            if(!empty($_POST["action"])){
                $action = $_POST['action'];
                //echo "Pos".(strrpos($action,"leave")+1);
                if(strrpos($action,"leave")=="FALSE"){ // WHAT THE ACTUAL FUCK?
                    // User Wants To LEAVE the group
                    $group = substr($action,5);
                    $sql = "DELETE FROM members WHERE  members.group_id = ".$group." and members.user_id = ".get_current_user_id();
                    if ($conn->query($sql) === TRUE) {
                        //echo "Gruppe erfolgreich verlassen! Um erneut beizutreten, müssen Sie von einem Mitglied eingeladen werden!";
                        //wenn letzter in Gruppe, diese Löschen!!
                        if($member_count[$group]==1){
                            sleep(1);
                            $sql = "DELETE FROM groups WHERE groups.id = ".$group;
                            $conn->query($sql);
                            if ($conn->query($sql) === TRUE){
                                echo "<script>
                                    alert('Gruppe erfolgreich verlassen!Sie waren das Letzte Mitgleid, Gruppe gelöscht!');
                                    window.location.reload();
                                </script>";
                            }else{
                                echo "Error deleting record: " . $conn->error;
                            }

                        }else{
                            echo "<script>
                                alert('Gruppe erfolgreich verlassen! Um erneut beizutreten, müssen Sie von einem Mitglied eingeladen werden!');
                                window.location.reload();
                            </script>";
                        }
                    } else {
                        echo "Error deleting record: " . $conn->error;
                    }
                }
                if(strrpos($action,"invite")=="FALSE"){
                    $group = substr($action,6);
                    $user_name = $_POST['invite_user_name'];
                    $sql = "SELECT * FROM wp_ds_kalationusers WHERE wp_ds_kalationusers.user_login = '".$user_name."'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        // output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "Spieler <b>" . $row["user_login"]. "</b> gefunden!<br>";
                            $sql = "INSERT INTO group_invites (user_id,group_id) VALUES (".$row["ID"].",".$group.")";
                            if ($conn->query($sql) === TRUE) {
                                echo 'Spieler Eingeladen!';
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }
                    } else {
                        echo "Es konnte niemand gefunden werden!";
                    }
                }
            }
        }
    ?>
    <h3>Einladungen</h3>
    Sie haben Einladungen, um folgenden Gruppen beizutreten:<br>
    <ol>
    <?php
        $sql = "SELECT * FROM group_invites,groups WHERE groups.id=group_invites.group_id and group_invites.user_id=".get_current_user_id();
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<b>".$row["name"]."</b>
                <form method='post' action=''>
                    <button name='invites_action' value='show_members".$row["id"]."'>Mitglieder anzeigen</button>
                    <button name='invites_action' value='join".$row["id"]."'>Beitreten</button>
                    <button name='invites_action' value='reject".$row["id"]."'>Löschen</button>
                </form>
                ";
            }
        } else {
            echo "Keine Gruppeneinladungen. Bitte aktualisieren Sie diese Seite! (F5)<br>";
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            if(!empty($_POST["invites_action"])){
                $action = $_POST["invites_action"];
                if(strrpos($action,"join")=="FALSE"){
                    $group = substr($action,4);
                    $sql = "INSERT INTO members (user_id,group_id) VALUES (".get_current_user_id().",".$group.")";
                    if ($conn->query($sql) === TRUE) {
                        $sql = "DELETE FROM group_invites WHERE user_id = ".get_current_user_id()." and group_id = ".$group;
                        $conn->query($sql);
                        echo "<script>
                            alert('Erfolgreich der Gruppe beigetreten!');
                            window.location.reload();
                        </script>";
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                }
                if(strrpos($action,"reject")=="FALSE"){
                    $group = substr($action,6);
                    $sql = "DELETE FROM group_invites WHERE user_id = ".get_current_user_id()." and group_id = ".$group;
                    $conn->query($sql);
                    echo "<script>
                        window.location.reload();
                    </script>";
                }
                if(strrpos($action,"show_members")=="FALSE")
                $group = substr($action,12);
                $sql = "SELECT * FROM wp_ds_kalationusers,members WHERE wp_ds_kalationusers.ID=members.user_id and members.group_id = ".$group;
                $result = $conn->query($sql);
                if ($result->num_rows > 0){
                    $text = " ";
                    while($row = $result->fetch_assoc()){
                        $text = $text." ".$row["user_login"].",";
                    }
                    echo "<script>
                        alert('Mitglieder:".substr($text,0,strlen($text)-1)."');
                    </script>";
                }else{
                    echo "Keine Mitgleider mehr in dieser Gruppe.";
                }
            }
        }
        $conn->close();
    ?>
    </ol>

</div>
