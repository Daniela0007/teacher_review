<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Recenziile Profesorilor</title>
  <link rel="stylesheet" href="./css/admin.css">
  <!-- Add your font and icon library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
      <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
</head>
<body>
  <!-- Header Section -->
  <header class="admin-header">
    <div class="container">
     <img src="./assets/faculty_logo.svg" alt="Faculty Logo" class="logo" />
        <div class="btn">
          <form action="?controller=Login&action=logout" method="POST">
              <button type="submit" class="logout-button">
                <i class="fas fa-sign-out-alt"></i> Logout
              </button>
          </form>
        </div>
    </div>
  </header>

  <!-- Main Section -->
  <main>
    <!-- Search Section -->
    <section class="welcome-text">
      <p>Bun venit, Admin </p>
    </section>

    <div class="admin-filter">
        <!-- Year Dropdown -->
        <label for="year-select">An de studiu</label>
        <select id="year-select" onchange="updateCourses()">
            <option value="">An de studiu</option>
        </select>

        <!-- Course Dropdown -->
        <label for="course-select">Disciplină</label>
        <select id="course-select" onchange="updateProfessors()">
            <option value="">Disciplină</option>
        </select>

        <!-- Professor Dropdown -->
        <label for="professor-select">Profesor</label>
        <select id="professor-select">
            <option value="">Profesor</option>
        </select>

        <!-- Type Dropdown -->
        <label for="type-select">Format</label>
        <select id="type-select">
            <option value="">Toate</option>
            <option value="curs">Curs</option>
            <option value="seminar">Seminar</option>
        </select>

        <button onclick="applyFilters()">Aplică filtre</button>
    </div>


    <!-- Reviews Section -->
    <section class="reviews-section">
      <div class="container">
        <div class="review-container" id="review-container">
          <!-- Dynamic content will be inserted here using JavaScript -->
        </div>
      </div>
    </section>

  </main>

  <!-- JavaScript -->
  <script>
    function generateStars(rating) {
      return '★'.repeat(rating) + '☆'.repeat(5 - rating);
    }

    function displayMessage(message, type) {
        const messageContainer = document.createElement('div');
  
        messageContainer.className = `${type}-message`;
        messageContainer.textContent = message;

        document.body.appendChild(messageContainer);

        setTimeout(() => {
            messageContainer.remove();
        }, 3000);
    }

    // All the filtering logic here -->
    let jsonData = {};

    function fetchJsonData() {
        fetch('../app/utils/data.json') 
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch JSON data');
                    }
                    return response.json(); 
                })
                .then((data) => {
                    jsonData = data;
                    populateYears();
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
    }
    const courseSelect = document.getElementById("course-select");
    const professorSelect = document.getElementById("professor-select");
    const typeSelect = document.getElementById("type-select");

    function populateYears() {
        console.log("jsonData", jsonData);
        const yearSelect = document.getElementById('year-select');

        const years = Object.keys(jsonData).sort(); 

        years.forEach((year) => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        });
    }

    function updateCourses() {
      const yearSelect = document.getElementById('year-select');
      const courseSelect = document.getElementById('course-select');
      const selectedYear = yearSelect.value;

      courseSelect.innerHTML = '<option value="">Disciplină</option>';

      if (selectedYear && jsonData[selectedYear]) {
          const courses = jsonData[selectedYear].courses;

          for (const course in courses) {
              const option = document.createElement('option');
              option.value = course;
              option.textContent = course;
              courseSelect.appendChild(option);
          }
      }
    }

    function updateProfessors() {
      const courseSelect = document.getElementById('course-select');
      const professorSelect = document.getElementById('professor-select');
      const selectedCourse = courseSelect.value;

      professorSelect.innerHTML = '<option value="">Profesor</option>';

      for (const year in jsonData) {
          const courses = jsonData[year].courses;
          if (selectedCourse in courses) {
              const professors = courses[selectedCourse];
              professors.forEach((professor) => {
                  const option = document.createElement('option');
                  option.value = professor;
                  option.textContent = professor;
                  professorSelect.appendChild(option);
              });
              break;
          }
      }
    }

    
    function applyFilters() {
        const year = document.getElementById('year-select').value;
        const course = document.getElementById('course-select').value;
        const professor = document.getElementById('professor-select').value;
        const type = document.getElementById('type-select').value;

        const selectedType = type === 'toate' ? null : type;

        const filterParams = {
          year: year || null,
          course: course || null,
          professor: professor || null,
          type: selectedType || null, 
        };

        console.log('Selected Filters:', filterParams);

        if (filterParams.year && filterParams.course && filterParams.professor) {
            getFeedbacks(filterParams); 
        } else {
            displayMessage('Please select all required filters (year, course, and professor) before applying.', "info");
        }
    }

    function getFeedbacks(filters) {
        console.log('Fetching feedbacks with filters:', filters);

        fetch('?controller=Admin&action=getFeedbacks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(filters),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Failed to fetch feedbacks');
                }
                return response.json();
            })
            .then((data) => {
                console.log('Received Feedbacks:', data);

                const feedbackContainer = document.getElementById("review-container");
                feedbackContainer.innerHTML = "";

                if (data.feedbacks.length === 0) {
                    displayMessage('No feedbacks found for the selected filters.', 'info');
                    return;
                }

                data.feedbacks.forEach((feedbackData) => {
                    const feedbackCard = document.createElement("div");

                    feedbackCard.className = "review-card";

                    feedbackCard.innerHTML = `
                      <div class="card-body">
                        <h6 class="card-subtitle">
                          <i class="fas fa-book"></i> ${feedbackData.course} 
                          <span class="badge">${feedbackData.type === 'curs' ? 'Curs' : 'Seminar'}</span>
                        </h6>
                        <p class="card-year">
                          <i class="fas fa-calendar"></i> ${feedbackData.year}
                        </p>
                        <p class="card-text">
                          <i class="fas fa-thumbs-up"></i> ${feedbackData.positive_feedback}
                        </p>
                        <p class="card-text">
                          <i class="fas fa-thumbs-down"></i> ${feedbackData.negative_feedback}
                        </p>
                        <ul class="feedback-answers">
                          ${feedbackData.feedback_answers
                            .map(
                              (feedback) => `
                              <li>
                                <strong>${feedback.question}</strong> ${generateStars(feedback.answer)}
                              </li>
                            `
                            )
                            .join("")}
                        </ul>
                        <label>
                          <input type="checkbox" class="show-email-checkbox" /> Show Student Email
                        </label>
                        <p class="student-email" style="display: none;">
                          <i class="fas fa-envelope"></i> ${feedbackData.student_mail}
                        </p>
                      </div>
                    `;

                    feedbackContainer.appendChild(feedbackCard);

                    const checkbox = feedbackCard.querySelector(".show-email-checkbox");
                    const emailParagraph = feedbackCard.querySelector(".student-email");

                    checkbox.addEventListener("change", () => {
                      if (checkbox.checked) {
                        emailParagraph.style.display = "block"; 
                      } else {
                        emailParagraph.style.display = "none"; 
                      }
                    });
                });
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchJsonData();
    });
  </script>
</body>
</html>