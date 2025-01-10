<?php
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Feedback Page</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./css/student.css" />
  </head>
  <body>
    <main>
      <!-- Hero Section -->
      <div class="header">
        <img src="./assets/faculty_logo.svg" alt="Faculty Logo" style="height: 50px" />
        <div class="buton">
          <form action="?controller=Login&action=logout" method="POST" style="display: inline;">
              <button type="submit" class="logout-button">
                <i class="fas fa-sign-out-alt"></i> Logout
              </button>
          </form>
        </div>
      </div>

      <section class="hero">
        <div class="hero-content">
          <h1>Părerea ta contează pentru noi!</h1>
          <p>
            Ajută-ne să îmbunătățim experiența educațională prin opiniile tale
            sincere și constructive.
          </p>
        </div>
      </section>

      <!-- Feedback Form Section -->
      <section class="feedback-form">
        <div class="feedback-container">
          <h2 class="mb-4">Lasă-ne recenzia ta!</h2>

          <form action="" method="post" id="feedbackForm">
            <!-- Year Dropdown -->
            <div class="row">
              <label for="year" class="form-label"
                ><i class="fas fa-calendar-alt"></i> Anul de studiu</label
              >
              <select
                id="year"
                name="year"
                class="form-select"
                onchange="updateCourses(this.value)"
              >
                <option value="" disabled selected>Anul de studiu</option>
                <?php
                  ksort($data);
                  
                   foreach ($data as $year => $details) {
                    echo "<option value='" . htmlspecialchars($year) . "'>$year</option>";
                }
                ?>
              </select>
            </div>

            <!-- Courses Dropdown -->
            <div class="row">
              <label for="courses" class="form-label"
                ><i class="fas fa-book"></i> Cursul</label
              >
              <select
                id="courses"
                name="course"
                class="form-select"
                onchange="updateProfessors(this.value)"
                disabled
              >
                <option value="" disabled selected>Curs</option>
              </select>
            </div>

            <!-- Professors Dropdown -->
            <div class="row">
              <label for="professors" class="form-label"
                ><i class="fas fa-chalkboard-teacher"></i> Profesorul</label
              >
              <select
                id="professors"
                name="professor"
                class="form-select"
                disabled
              >
                <option value="" disabled selected>Profesor</option>
              </select>
            </div>

            <!-- Type Dropdown -->
            <div class="row">
              <label for="type" class="form-label">
                <i class="fas fa-list-alt"></i> Formatul activității
              </label>
              <select id="type" name="type" class="form-select" required>
                <option value="" disabled selected>Selectează tipul</option>
                <option value="curs">Curs</option>
                <option value="seminar">Seminar</option>
              </select>
            </div>

            <!-- Questions Container -->
            <div class="row" id="questions-container">
            </div>

            <!-- Feedback Text: Good Aspects -->
            <div class="row">
              <label for="positive-feedback" class="form-label">
                <i class="fas fa-thumbs-up"></i> Aspecte pozitive
              </label>
              <textarea
                id="positive-feedback"
                name="positive_feedback"
                class="form-control"
                rows="4"
                placeholder="Scrie aici aspectele pozitive..."
              ></textarea>
            </div>

            <!-- Feedback Text: Negative Aspects -->
            <div class="row">
              <label for="negative-feedback" class="form-label">
                <i class="fas fa-thumbs-down"></i> Aspecte negative
              </label>
              <textarea
                id="negative-feedback"
                name="negative_feedback"
                class="form-control"
                rows="4"
                placeholder="Scrie aici aspectele negative..."
              ></textarea>
            </div>

            <!-- Submit Button -->
            <div class="row">
              <button
                class="btn btn-primary w-100"
                id="submitEvaluation"
                type="submit"
                disabled
              >
                <i class="fas fa-paper-plane"></i> Trimite recenzia
              </button>
            </div>
            
          </form>
        </div>
      </section>

      <!-- Reviews Section -->
      <div class="my-reviews" id="my-reviews">
        <h2>Răsfoiește recenziile tale anterioare</h2>
        <div class="review-container" id="review-container">
            <!-- Review cards -->
        </div>
      </div>
    </main>
    
    <script>
        const data = <?php echo json_encode($data); ?>;
        const form = document.getElementById('feedbackForm');
        const submitButton = document.getElementById('submitEvaluation');
        let formQuestions = [];

        function generateStars(rating) {
            return '★'.repeat(rating) + '☆'.repeat(5 - rating);
        }

        form.addEventListener('input', () => {
            const year = document.getElementById('year').value;
            const course = document.getElementById('courses').value;
            const professor = document.getElementById('professors').value;
            const typeActivity = document.getElementById('type').value;
            const positiveFeedback = document.querySelector('[name="positive_feedback"]').value;
            const negativeFeedback = document.querySelector('[name="negative_feedback"]').value;

            const questions = document.querySelectorAll(".radios");
            let allAnswered = true;

            questions.forEach((radios) => {
                const selected = radios.querySelector("input[type='radio']:checked");
                if (!selected) {
                    allAnswered = false; 
                }
            });

            if (year && course && professor && positiveFeedback.trim() 
                && negativeFeedback.trim() && allAnswered && typeActivity) {
              submitButton.disabled = false;
            } else {
              submitButton.disabled = true;
            }
        });

        function updateCourses(year) {
            const coursesDropdown = document.getElementById("courses");
            const professorsDropdown = document.getElementById("professors");
            submitButton.disabled = true;

            coursesDropdown.innerHTML = '<option value="" disabled selected>Selectează cursul</option>';
            professorsDropdown.innerHTML = '<option value="" disabled selected>Selectează profesorul</option>';
            professorsDropdown.disabled = true;

            if (data[year] && data[year].courses) {
                for (const course in data[year].courses) {
                    const option = document.createElement("option");
                    option.value = course;
                    option.textContent = course;
                    coursesDropdown.appendChild(option);
                }
                coursesDropdown.disabled = false;
            }
        }

        function updateProfessors(course) {
            const year = document.getElementById("year").value;
            const professorsDropdown = document.getElementById("professors");
            submitButton.disabled = true;

            professorsDropdown.innerHTML = '<option value="" disabled selected>Selectează profesorul</option>';

            if (data[year] && data[year].courses && data[year].courses[course]) {
                data[year].courses[course].forEach(professor => {
                    const option = document.createElement("option");
                    option.value = professor;
                    option.textContent = professor;
                    professorsDropdown.appendChild(option);
                });
                professorsDropdown.disabled = false;
            }
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

        document.getElementById('feedbackForm').addEventListener('submit', function (e) {
            e.preventDefault(); 

            const negativeFeedback = document.getElementById('negative-feedback').value;
            const positiveFeedback = document.getElementById('positive-feedback').value;

            const xssPattern = /<[^>]*>|<script.*?>.*?<\/script.*?>|javascript:|on\w+\s*=/gi;

            if (xssPattern.test(negativeFeedback) || xssPattern.test(positiveFeedback)) {
                displayMessage("Invalid input detected in feedbacks", "error");
                return; 
            }

            const formData = new FormData(this); 
            const payload = {};

            formData.forEach((value, key) => {
                if (!key.startsWith('question')) {
                    payload[key] = value;
                }
            });

            const selectedAnswers = [];
            const radios = this.querySelectorAll('input[type="radio"]:checked');
            radios.forEach((radio) => {
                selectedAnswers.push({
                    question_id: radio.getAttribute('data-question-id'), 
                    answer: radio.value, 
                });
            });

            payload['feedback_answers'] = selectedAnswers;

            //console.log('Payload:', payload);
            fetch('?controller=Student&action=save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload), 
            })
            .then((response) => {
                    return response.json().then((data) => {
                        if (!response.ok) {
                            //console.log("Error: ", data);
                            throw new Error(data.error || "An unknown error occurred.");
                        }
                        return data; 
                    });
             })
                .then((data) => {
                    //console.log('Server response data:', data);
                    
                    displayMessage(data.message, 'success');

                    const reviewContainer = document.getElementById("review-container");
                    const reviewSection = document.getElementById("my-reviews");
                    
                    if (reviewSection.style.display === 'none') {
                      reviewSection.style.display = '';
                    }

                    const questionMap = {};
                    formQuestions.forEach(question => {
                      questionMap[question.id] = question.question_text;
                    });

                    //console.log('questionMap:', questionMap); 

                    const reviewCard = `
                         <div class="review-card">
                          <div class="card-body">
                            <h5 class="card-title">
                              <i class="fas fa-user-tie"></i> ${payload.professor}
                            </h5>
                            <h6 class="card-subtitle">
                              <i class="fas fa-book"></i> ${payload.course} 
                              <span class="badge">${payload.type === 'curs' ? 'Curs' : 'Seminar'}</span>
                            </h6>
                            <p class="card-text">
                              <i class="fas fa-thumbs-up"></i> ${payload.positive_feedback}
                            </p>
                            <p class="card-text">
                              <i class="fas fa-thumbs-down"></i> ${payload.negative_feedback}
                            </p>
                            <ul class="feedback-answers">
                              ${payload.feedback_answers
                                .map(answer => {
                                  const questionText = questionMap[answer.question_id] || "Unknown question";
                                  return `
                                    <li>
                                      <strong>${questionText}</strong>: ${generateStars(answer.answer)}
                                    </li>
                                  `;
                                })
                                .join('')}
                            </ul>
                          </div>
                        </div>
                    `;

                    reviewContainer.innerHTML += reviewCard;

                    this.reset();
                    const submitButton = document.getElementById('submitEvaluation');
                    submitButton.disabled = true;

                    document.getElementById("courses").disabled = true;
                    document.getElementById("professors").disabled = true;
                })
                .catch((error) => {
                  displayMessage(error.message || "Something went wrong. Please try again.", 'error');
                });
        });
         
        function loadQuestions() {
            fetch('?controller=Student&action=getQuestions', {
              method: 'GET',
            }) 
            .then(response => response.json())
            .then(data => {
                //console.log('Questions:', data.questions);
                const questions = data.questions;
                formQuestions = questions;
                //console.log('formQuestions:', formQuestions);
                const container = document.getElementById('questions-container');
                container.innerHTML = '';

                questions.forEach((question, index) => {
                    const questionDiv = document.createElement("div");
                    questionDiv.className = "row";

                    questionDiv.innerHTML = `
                        <label class="form-label">
                            <i class="fas fa-question-circle"></i> ${question.question_text}
                        </label>
                        <div class="radios">
                            <label class="radio-inline">
                                <input type="radio" name="question${question.id}" value="1" data-question-id="${question.id}" /> 1
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="question${question.id}" value="2" data-question-id="${question.id}" /> 2
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="question${question.id}" value="3" data-question-id="${question.id}" /> 3
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="question${question.id}" value="4" data-question-id="${question.id}" /> 4
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="question${question.id}" value="5" data-question-id="${question.id}" /> 5
                            </label>
                        </div>
                    `;

                    container.appendChild(questionDiv);
                });
              }).catch(error => console.error('Error loading questions:', error));
          }

        function getFeedbacks() {
            fetch('?controller=Student&action=getFeedbacks', {
                method: 'GET',
            })
                .then(response => {
                  if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                  }
                  return response.json(); 
                })
                .then(data => {
                    //console.log('Feedbacks:', data);

                    if (data.feedbacks.length === 0) {
                        //console.log('No feedbacks available.');
                        //displayMessage('No feedbacks found.', 'info');
                        const reviewContainer = document.getElementById("my-reviews");
                        reviewContainer.style.display = '';
                    } else {
                        const feedbacks = data.feedbacks;
                        const myReviewsContainer = document.getElementById("my-reviews");
                        const reviewContainer = document.getElementById("review-container");

                        if (myReviewsContainer.style.display === 'none') {
                            myReviewsContainer.style.display = '';
                        }

                        feedbacks.forEach(feedback => {
                        const reviewCard = `
                            <div class="review-card">
                              <div class="card-body">
                                <h5 class="card-title">
                                  <i class="fas fa-user-tie"></i> ${feedback.professor}
                                </h5>
                                <h6 class="card-subtitle">
                                  <i class="fas fa-book"></i> ${feedback.course} 
                                  <span class="badge">${feedback.type === 'curs' ? 'Curs' : 'Seminar'}</span>
                                </h6>
                                <p class="card-text">
                                  <i class="fas fa-thumbs-up"></i> ${feedback.positive_feedback}
                                </p>
                                <p class="card-text">
                                  <i class="fas fa-thumbs-down"></i> ${feedback.negative_feedback}
                                </p>
                                <ul class="feedback-answers">
                                  ${feedback.feedback_answers
                                    .map(
                                      (feedback) => `
                                      <li>
                                        <strong>${feedback.question}</strong> ${generateStars(feedback.answer)}
                                      </li>
                                    `
                                    )
                                    .join("")}
                                </ul>
                              </div>
                            </div>
                          `;

                          reviewContainer.innerHTML += reviewCard;
                      });
                    }
                }).catch(error => {
                      //console.error('Fetch error:', error);
                      displayMessage('An unexpected error occurred. Please try again later.', 'error');
                });
        }
              
        document.addEventListener('DOMContentLoaded', () => {
            loadQuestions();
            getFeedbacks();
        });

    </script>
  </body>
</html>