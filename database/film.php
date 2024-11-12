<?php include "top.php"; ?>
<?php require "connection.php" ?>
<!--
    <div class="alert alert-success">¡Ejemplo mensaje de éxito!</div>
    <div class="alert alert-error">¡Ejemplo mensaje de error!</div>
    -->

<section id="films">
  <h2>Peliculas</h2>
  <form action="film.php" method="get">
    <fieldset>
      <legend>Categorías</legend>
      <select name="category" id="">
        <option selected disabled>Elige una categoría</option>
        <?php
        try {
          $stmtCategories = $conn->prepare("SELECT category_id, name FROM category");
          $stmtCategories->execute();
          $categories = $stmtCategories->fetchAll(PDO::FETCH_OBJ);

          foreach ($categories as $category) {
            printf("<option value='%d'>%s</option>", $category->category_id, $category->name);
          }

          $stmtCategories = null;
        } catch (Exception $e) {
          die('Se jodio ' . $e->getMessage());
        }


        ?>
      </select>
      <input type="submit" name="search" value="buscar">
      <input type="submit" name="delete" value="eliminar">
    </fieldset>
  </form>
  <nav>
    <fieldset>
      <legend>Acciones</legend>
      <a href="create.php">
        <button>Crear Categoria</button>
      </a>
    </fieldset>
  </nav>
  <table>
    <thead>
      <tr>
        <th>Título</th>
        <th>Año</th>
        <th>Duración</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <!-- <tr>
        <td>El tercer hombre</td>
        <td class="center">1949</td>
        <td class="center">108</td>
        <td class="actions">
          <a class="button" href="category_film.php?...">
            <button>Cambiar categorías</button>
          </a>
        </td>
      </tr> -->
      <?php
      try {
        if (isset($_GET['search']) && !empty($_GET['category'])) {
          $categoria = $_GET['category'];

          $stmtTable = $conn->prepare("SELECT film.film_id, film.title, film.release_year, film.length FROM film, film_category WHERE film.film_id = film_category.film_id AND film_category.category_id = :category_id;");
          $stmtTable->bindParam(':category_id', $categoria, PDO::PARAM_INT);
          $stmtTable->execute();

          if ($stmtTable->rowCount() === 0) {
            print("<h1>No hay películas para esta categoría</h1>");
          }
          

          $nombrePeliculasPorIdCategoria =  $stmtTable->fetchAll(PDO::FETCH_OBJ);

          foreach ($nombrePeliculasPorIdCategoria as $pelicula) {
            echo "<tr>";
            printf("<td>%s</td>", $pelicula->title);
            printf("<td class='center'>%d</td>", $pelicula->release_year);
            printf("<td class='center'>%d</td>", $pelicula->length);
            printf(" <td class='actions'><a class='button' href='category_film.php?name=%s'><button>Cambiar categorías</button></a></td>",  $pelicula->title);
            echo "</tr>";
          }
          $nombrePeliculasPorIdCategoria = null;
          $stmtTable = null;
          $categoria = null;
        }


        if (isset($_GET['delete']) &&(!empty($_GET['category']))) {
          $categoria = $_GET['category'];
          $stmtTable = $conn->prepare("SELECT film.film_id, film.title, film.release_year, film.length FROM film, film_category WHERE film.film_id = film_category.film_id AND film_category.category_id = :category_id;");
          $stmtTable->bindParam(':category_id', $categoria, PDO::PARAM_INT);
          echo $categoria;
          $stmtTable->execute();

          if(!$stmtTable->rowCount() === 0){
            echo "<p class=' alert alert-error'>¡No se ha podido borrar la categoria porque ya tiene peliculas AA!</p>";
            echo $stmtTable->rowCount();
          }else{
          $stmtDeleteCategory = $conn->prepare("DELETE FROM category WHERE category.category_id = :category");
          $stmtDeleteCategory->bindParam(':category', $categoria);
          $stmtDeleteCategory->execute();
          $stmtDeleteCategory = null;
        }}
      

      } catch (Exception $e) {
        die('liada: ' . $e->getMessage());
      }


      ?>

    </tbody>
  </table>
</section>
<?php include "bottom.php"; ?>

<!-- SELECT film.title from film_category INNER JOIN film ON film.film_id = film_category.film_id WHERE film_category.category_id = 4; -->