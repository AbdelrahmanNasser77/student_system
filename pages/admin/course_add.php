<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course | Add</title>
  <script src="js/customized_alert.js"></script>

</head>

<body>
  <?php
  include("../../includes/identity_nav.php");
  include("menu_nav.html");
  include("../../includes/alerts.php");
  ?>

  <script src="js/customized_alert.js"></script>

  <section class="mx-auto grades text-center min-vh-100 py-5 px-3">

    <div class="container">
      <div class="row justify-content-center">

        <header class="header-adj2 mb-5">
          <h2 class=" f-header fs-1 fw-bold text-white"><?php echo isset($_POST['edit']) ? 'Edit Course' : 'Add Course'; ?></h2>
        </header>

        <form action="" method="post" class="bg-dark col-md-6 text-center text-white p-4 rounded">
          <div class="mb-3">
            <label for="name" class="form-label">Course Name:</label>
            <input type="text" class="form-control text-center" name="name" id="name" value="<?php echo isset($_POST['edit']) ? $_POST['crs_name'] : ''; ?>">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label for="id" class="form-label">Course Code:</label>
              <input type="text" class="form-control text-center" name="id" id="id" value="<?php echo isset($_POST['edit']) ? $_POST['edit'] : ''; ?>">
            </div>

            <div class="col-md-6">
              <label for="credit" class="form-label">Credits:</label>
              <input type="number" class="form-control text-center" name="credit" id="credit" min="0" max="3" step="1" value="<?php echo isset($_POST['edit']) ? $_POST['crs_credit'] : ''; ?>">
            </div>
          </div>

          <div class="mb-3">
            <div class="dropdown">
              <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Select Pre-requisite
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                <?php
                $retrieve = "SELECT name, id FROM course ORDER BY name";
                $result = $conn->query($retrieve);

                while ($row = $result->fetch_assoc()) :
                  $course_name = $row["name"];
                  $course_id = $row["id"];
                ?>

                  <?php if (!isset($_POST["edit"]) || $course_id != $_POST["edit"]) :  ?>
                    <li>
                      <label class='form-check-label' style='font-size: 1.2em;'>
                        <input value=" <?php echo $course_id ?> " type="checkbox" name="pre_req[]" class="form-check-input" style="width: 1.5em; height: 1.5em; margin-right: 0.5em">
                        <?php echo $course_id . " - " . $course_name ?>
                      </label>
                    </li>
                  <?php endif ?>
                <?php endwhile ?>
              </ul>
            </div>
          </div>

          <div class=" mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" name="description" id="description" rows="5"><?php echo isset($_POST['edit']) ? $_POST['crs_description'] : ''; ?></textarea>
          </div>

          <div class="text-center">
            <input type="submit" class="btn btn-primary" value="<?php echo isset($_POST['edit']) ? 'Edit Course' : 'Add Course'; ?>" name="new_course">
          </div>
        </form>
      </div>
    </div>
  </section>

</body>

</html>
<?php

if (isset($_POST["edit"])) {
  $_SESSION["crs_code_edit"] = $_POST["edit"];
}

if (isset($_POST["new_course"])) {
  $name = $_POST["name"];
  $id = $_POST["id"];
  $credits = $_POST["credit"];
  $description = $_POST["description"];
  $pre_requisites = isset($_POST["pre_req"]) ? $_POST["pre_req"] : array();

  if (isset($_SESSION["crs_code_edit"])) {
    $update_Course_query = "UPDATE course
      SET 
        id = '$id',
        name = '$name',
        credit = '$credits',
        description = '$description'
      
      WHERE id = '$_SESSION[crs_code_edit]'

    ";
    $update_Course = $conn->query($update_Course_query);

    header("location: course_view.php?edit=true");
  } else {
    $insert_Course_query = "INSERT into 
    course (
      id,
      name,
      credit,
      description
    )
    VALUES (
      '$id',
      '$name',
      '$credits',
      '$description'
    )
    ";

    $insert_Course = $conn->query($insert_Course_query);
  }

  if (!empty($pre_requisites)) {

    if (isset($_SESSION["crs_code_edit"])) {
      $delete_pre_req_query = "DELETE FROM course_pre_requisit WHERE course_id = '$id'";
      $delete_pre_req = $conn->query($delete_pre_req_query);

      unset($_SESSION["crs_code_edit"]);
    }

    foreach ($pre_requisites as $pre_req_ID) {

      $insert_pre_req_query = "INSERT into 
            course_pre_requisit(
                course_id,
                pre_requisit_id
            )
            VALUES (
                '$id',
                '$pre_req_ID'
            )
            ";
      $insert_pre_req = $conn->query($insert_pre_req_query);
    }

    if ($insert_Course && $insert_pre_req) {
      displayAlert("Courses & Pre-requisits", "Added", "success", "Successful!", "Successfully.\nDatabase Insertion Done!", "OK");
    } elseif ($insert_Course) {
      displayAlert("Course", "Added", "success", "Successful!", "Successfully.\nDatabase Insertion Done!", "OK");
    } else {
      displayAlert("Course", "Added", "error", "Failed!", "\nPlease Try Again", "Try Again");
    }
  }
}

?>