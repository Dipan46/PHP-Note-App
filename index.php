<!-- Connecting to DB -->
<?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "phpnoteapp";

        $conn = mysqli_connect($servername, $username, $password, $database);
        
        if(!$conn)
            die("❌Connection was not successful: " . mysqli_error($conn));
        
        if (isset($_GET["delete"])){
            $sl = $_GET["delete"];
            $sql = "DELETE FROM notes WHERE `notes`.`id` = '$sl';";
            $result = mysqli_query($conn, $sql);
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            if (isset($_POST["slEdit"])){

                $sl = $_POST["slEdit"];
                $title = mysqli_real_escape_string($conn, $_POST["titleEdit"]);
                $description = mysqli_real_escape_string($conn, $_POST["descriptionEdit"]);

                $sql = "UPDATE `notes` SET `title` = '$title', `description` = '$description' WHERE `notes`.`id` = '$sl';";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    header("Location: /exp/dir/note_app/index.php");
                    exit();
                }

            } else {

                $title = mysqli_real_escape_string($conn, $_POST["title"]);
                $description = mysqli_real_escape_string($conn, $_POST["description"]);

                $sql = "INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$description');";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    header("Location: /exp/dir/note_app/index.php");
                    exit();
                }
            }
        }


?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>php</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&family=IM+Fell+DW+Pica:ital@0;1&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="./styles.css">
</head>

<body>
    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Note</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action=" /exp/dir/note_app/index.php" method="post">
                        <input type="hidden" name="slEdit" id="slEdit">
                        <div class="mb-3 my-4">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" required="true" name="titleEdit" id="titleEdit"
                                placeholder="Note Title">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" required="true" name="descriptionEdit" id="descriptionEdit"
                                rows="5"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Update Note</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/exp/dir/note_app/index.php">PHP Note App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Add Your Note</h2>
        <form action="/exp/dir/note_app/index.php?update=true" method="post">
            <div class="mb-3 my-4">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" required="true" name="title" id="title"
                    placeholder="Note Title">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" required="true" name="description" id="description" rows="5"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Add Note</button>
        </form>
    </div>

    <div class="container">
        <h2>Notes</h2>
    </div>

    <div class="container">
        <?php
            $sql = "SELECT * FROM `notes`;";
            $result = mysqli_query($conn, $sql);

            $num = mysqli_num_rows($result); // Number of rows in data
            // echo $num;
        ?>

        <table id="myTable" class="table">
            <thead>
                <tr>
                    <th scope="col">SL</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Time</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if($num> 0){
                        $sl = 0;
                        while($row = mysqli_fetch_assoc($result)) // assoc returns the associated array
                        {
                            $sl = $sl + 1;
                            echo
                            '<tr>
                                <th scope="row">' . $sl . '</th>
                                <td>' . $row['title'] . '</td>
                                <td>' . $row['description'] . '</td>
                                <td>' . $row['t_stamp'] . '</td>
                                <td><a class="edit" id='. $row['id'] . '>✏️</a><a class="delete" id=d'. $row['id'] . '>❌</a></td>
                            </tr>';
                        }
                    }
                ?>
        </table>
    </div>

    <footer class=" text-center footer-dark bg-dark">
        <!-- Copyright -->
        <div class="text-center p-3" style="color: white;">
            ©Dipan46
        </div>
        <!-- Copyright -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.js">
    </script>
    <script>
        let table = new DataTable('#myTable');
    </script>
    <script>
        edits = $('.edit');
        Array.from(edits).forEach(element => {
            element.addEventListener("click", (e) => {
                console.log("Edite");
                tr = e.target.parentNode.parentNode;

                title = tr.getElementsByTagName("td")[0].innerText;
                description = tr.getElementsByTagName("td")[1].innerText;

                titleEdit.value = title;
                descriptionEdit.value = description;
                slEdit.value = e.target.id;
                $("#editModal").modal("toggle");
            })
        });

        deletes = $('.delete');
        Array.from(deletes).forEach(element => {
            element.addEventListener("click", (e) => {
                sl = e.target.id.substr(1,);

                if (confirm("Are you sure! You want to delete this note.")){
                    window.location = `/exp/dir/note_app/index.php?delete=${sl}`;
                    //Create a form and use POST req to submit the form
                    
                }
            })
        });
    </script>
</body>

</html>