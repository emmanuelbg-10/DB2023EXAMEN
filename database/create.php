<?php include "top.php"; ?>
<?php require "connection.php" ?>
<section id="create">
    <h2>Nueva categoría</h2>
    <nav>
        <p><a href="film.php">Volver</a></p>
    </nav>
    <?php

    if (!empty($_GET['name'])) {
        try {
            $name = $_GET['name'];
            $stmtInsertCategory = $conn->prepare("INSERT INTO category (category_id, name, last_update) VALUES (NULL, :name,CURRENT_TIMESTAMP)");
            $stmtInsertCategory->bindParam(':name', $name, PDO::PARAM_STR);
            $stmtInsertCategory->execute();
            echo "<p class='alert alert-success'>Usuario creado satisfactoriamente!</p>";
        } catch (Exception $e) {
            echo "<p class=' alert alert-error'>¡No se ha podido crear al usuario!</p>";
            die('Se jodio ' . $e->getMessage());
        }

        include "bottom.php";
    } else {

    ?>
        <form action="create.php" autocomplete="off">
            <fieldset>
                <legend>Datos de la categoría</legend>
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" required>
                <?php
                ?>
                <input type="reset" value="Limpiar">
                <input type="submit" value="Crear">
            </fieldset>
        </form>
</section>
<?php include "bottom.php";
    } ?>