<?php include "top.php"; ?>
<?php require "connection.php" ?>

<nav>
  <p><a href="film.php">Volver</a></p>
</nav>
<section id="films">
  <form action="category_film.php" method="post">
    <?php
    if (isset($_GET['name'])) {
      $name = $_GET['name'];
    } elseif (isset($_POST['name'])) {
      $name = $_POST['name'];
    }
    printf("<input type='hidden' name='name' value='%s'>", $name);
    printf("<h2>Categorías de la pelicula: %s</h2>", $name);
    ?>

    <?php
    if (isset($_POST['name'])) {
      try {
        $conn->beginTransaction();

        // Obtener el film_id de la película
        $stmtIds = $conn->prepare("SELECT film_id FROM film WHERE title = :title");
        $stmtIds->bindParam(':title', $name, PDO::PARAM_STR);
        $stmtIds->execute();
        $film = $stmtIds->fetchObject();
        $film_id = $film->film_id;

        // Eliminar todas las categorías asociadas con la película antes de insertar las nuevas
        $stmtDeleteCategories = $conn->prepare("DELETE FROM film_category WHERE film_id = :film_id");
        $stmtDeleteCategories->bindParam(':film_id', $film_id, PDO::PARAM_INT);
        $stmtDeleteCategories->execute();

        // Insertar las nuevas categorías seleccionadas
        $stmtShowCategory = $conn->prepare("SELECT category_id, name FROM category;");
        $stmtShowCategory->execute();
        $categorias = $stmtShowCategory->fetchAll(PDO::FETCH_OBJ);

        foreach ($categorias as $categoria) {
          if (isset($_POST[$categoria->name])) {
            $stmtInsertCategory = $conn->prepare("INSERT INTO film_category (film_id, category_id) VALUES (:film_id, :category_id)");
            $stmtInsertCategory->bindParam(':film_id', $film_id, PDO::PARAM_INT);
            $stmtInsertCategory->bindParam(':category_id', $categoria->category_id, PDO::PARAM_INT);
            $stmtInsertCategory->execute();
          }
        }

        $conn->commit();
        // Mostrar mensaje de éxito solo si se procesa correctamente
        echo "<div class='alert alert-success'>¡Categorias actualizadas!</div>";
      } catch (PDOException $e) {
        $conn->rollBack();
        echo "<div class='alert alert-error'>Error al actualizar las categorías: " . $e->getMessage() . "</div>";
      } catch (Exception $e) {
        $conn->rollBack();
        echo "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
      }
    }
    ?>
    <ul>
      <?php
      try {
        $stmtShowCategory = $conn->prepare("SELECT category_id, name FROM category;");
        $stmtShowCategory->execute();
        $categorias = $stmtShowCategory->fetchAll(PDO::FETCH_OBJ);

        $stmtShowChecked = $conn->prepare("SELECT category.name FROM film INNER JOIN film_category On film_category.film_id = film.film_id INNER JOIN category On category.category_id = film_category.category_id WHERE film.title = :name;");
        $stmtShowChecked->bindParam(':name', $name, PDO::PARAM_STR);
        $stmtShowChecked->execute();
        $categoriasYaExistentes =  $stmtShowChecked->fetchAll(PDO::FETCH_OBJ);

        //creamos un array de las categorias existentes
        $categoriasExistentesPorNombre = [];

        foreach ($categoriasYaExistentes as $checked) {
          //metes los datos en ese array
          $categoriasExistentesPorNombre[$checked->name] = $checked;
        }


        foreach ($categorias as $categoria) {
          //comprobamos si esa pelicula tiene esa categoria, si es asi checked si no nada 
          $checkedAttr = isset($categoriasExistentesPorNombre[$categoria->name]) ? 'checked' : '';
          echo "<li>";
          printf("<label><input type='checkbox' name='%s' id='%d' %s>%s</label>", $categoria->name, $categoria->category_id,  $checkedAttr, $categoria->name);
          echo "</li>";
        }

        $stmtShowCategory = null;
        $categorias = null;
      } catch (Exception $e) {
        die('Se jodio: ' . $e->getMessage());
      }
      ?>
    </ul>
    <p>
      <input type="submit" value="Actualizar">
    </p>
  </form>
  <section>
    <?php include "bottom.php"; ?>