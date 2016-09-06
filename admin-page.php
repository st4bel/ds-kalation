<div class="wrap">
    <h2>DS Kalation</h2>

    <h3>Gruppe erstellen</h3>
    <?php
        $group_name = $_POST['group'];

        echo $group_name; ?>
    <form method="post" action="">
        <input type="text" name="group">
        <input class="button action" type="submit" value="Gruppe erstellen">
    </form>
    <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "wordpress";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        echo "Connected successfully <br>";

        $sql = "SELECT * FROM `groups` WHERE `id` =1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                echo "id: " . $row["id"]. " - Session: " . $row["sessionid"]. " - Name " . $row["name"]. "<br>";
            }
        } else {
            echo "0 results";
        }


        $conn->close();
    ?>
</div>
