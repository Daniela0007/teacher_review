<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Feedback</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
  </head>
  <body>
    <div class="container my-5">
      <!-- Header -->
      <div class="text-center mb-4">
        <h1>Admin Panel</h1>
        <p class="text-muted">Filter feedback by professor.</p>
      </div>
      
      <div class="mb-4">
        <label for="professorDropdown" class="form-label">Select Professor:</label>
        <select id="professorDropdown" class="form-select">
            <option value="all" selected>All Professors</option>
            <?php
            foreach ($professors as $professor) {
                echo "<option value=\"" . htmlspecialchars($professor) . "\">" . htmlspecialchars($professor) . "</option>";
            }
            ?>
        </select>
    </div>

      <!-- Container for Feedback -->
      <div id="reviewsContainer" class="row">
        <?php
        // Predefined array of feedbacks
        $feedbacks = [
          [
            "student_mail" => "student1@example.com",
            "course" => "Computer Architecture",
            "feedback_text" => "Great class! Learned a lot about pipelining.",
            "submitted_at" => "2023-11-10",
            "professor_id" => "alice",
          ],
          [
            "student_mail" => "student2@example.com",
            "course" => "Operating Systems",
            "feedback_text" => "The lectures were engaging and informative.",
            "submitted_at" => "2023-11-12",
            "professor_id" => "mark",
          ],
          [
            "student_mail" => "student3@example.com",
            "course" => "Computer Architecture",
            "feedback_text" => "Struggled with the assembly section, but the professor was very helpful.",
            "submitted_at" => "2023-11-14",
            "professor_id" => "sarah",
          ],
        ];
        ?>

        <!-- Render all feedback initially -->
        <?php foreach ($feedbacks as $feedback): ?>
          <div class="col-12 mb-4 feedback-card" data-professor-id="<?php echo $feedback['professor_id']; ?>">
            <div class="card review-card">
              <div class="card-body">
                <h5 class="card-title"><i class="fas fa-book"></i> Course: <?php echo $feedback['course']; ?></h5>
                <p class="card-text"><i class="fas fa-comment"></i> <?php echo $feedback['feedback_text']; ?></p>
                <p class="card-text">
                  <small class="text-muted"><i class="fas fa-calendar-alt"></i> Date: <?php echo $feedback['submitted_at']; ?></small>
                </p>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input show-email-checkbox" id="showEmail<?php echo $feedback['student_mail']; ?>">
                  <label for="showEmail<?php echo $feedback['student_mail']; ?>" class="form-check-label">Show Student Email</label>
                </div>
                <p class="student-email text-muted" style="display: none;"><i class="fas fa-envelope"></i> <?php echo $feedback['student_mail']; ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const professorDropdown = document.getElementById("professorDropdown");
        const feedbackCards = document.querySelectorAll(".feedback-card");

        // Filter feedback when a professor is selected
        professorDropdown.addEventListener("change", function () {
          const selectedProfessor = this.value;

          feedbackCards.forEach(card => {
            const professorId = card.getAttribute("data-professor-id");

            // Show or hide card based on selection
            if (selectedProfessor === "all" || selectedProfessor === professorId) {
              card.style.display = "block";
            } else {
              card.style.display = "none";
            }
          });
        });

        // Toggle email visibility on checkbox change
        document.addEventListener("change", function (e) {
          if (e.target.classList.contains("show-email-checkbox")) {
            const emailField = e.target.closest(".card").querySelector(".student-email");
            emailField.style.display = e.target.checked ? "block" : "none";
          }
        });
      });
    </script>
  </body>
</html>
