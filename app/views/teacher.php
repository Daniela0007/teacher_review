<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recenziile tale</title>
  <link rel="stylesheet" href="./css/teacher.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
</head>
<body>
  <!-- Header Section -->
  <header class="teacher-header">
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

    <div class="filter-container">
      <h3>Filtrează Recenziile</h3>
      <form id="filter-form">
          <!-- Filter by Type -->
          <label for="type-filter">Tip activitate:</label>
          <select id="type-filter" name="type">
              <option value="">Toate</option>
              <option value="curs">Curs</option>
              <option value="seminar">Seminar</option>
          </select>

          <!-- Filter by Subject -->
          <label for="subject-filter">Materie:</label>
          <select id="subject-filter" name="subject">
              <option value="">Toate</option>
              <!-- Dynamically populate this dropdown with subjects -->
          </select>

          <!-- Submit Button -->
          <button type="submit" class="btn-filter">Aplică Filtre</button>
      </form>
  </div>


  <!-- Main Section -->
  <main>
    <section class="reviews-section">
      <div class="container">
        <div class="review-container" id="review-container">

        </div>
      </div>
    </section>
  </main>

  <!-- JavaScript -->
  <script>
    const questionToCategoryMap = {};

    function displayMessage(message, type) {
        const messageContainer = document.createElement('div');
  
        messageContainer.className = `${type}-message`;
        messageContainer.textContent = message;

        document.body.appendChild(messageContainer);

        setTimeout(() => {
            messageContainer.remove();
        }, 3000);
    }
    
    function fetchQuestionsData() {
        fetch('../app/utils/questions.json') 
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch JSON data');
                    }
                    return response.json(); 
                })
                .then((data) => {
                  data.categories.forEach(category => {
                    category.questions.forEach(question => {
                      questionToCategoryMap[question] = category.category_name;
                    });
                  });
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
    }
    
    function getSubjects() {
      fetch('?controller=Teacher&action=getSubjects', {
        method: 'GET',
      })
        .then(response => response.json())
        .then(data => {
          console.log("subiectele profesorilor: ", data);
          const subjects = data.subjects;

          const subjectFilter = document.getElementById("subject-filter");
          subjects.forEach((subject) => {
                  const option = document.createElement("option");
                  option.textContent = subject;
                  subjectFilter.appendChild(option);
          });
        })
        .catch(error => console.error('Error loading subjects:', error));
    }

    document.addEventListener('DOMContentLoaded', function() {
        //getFeedbacks();
        getSubjects();
        fetchQuestionsData();

        const form = document.getElementById("filter-form");
        const typeFilter = document.getElementById("type-filter");
        const subjectFilter = document.getElementById("subject-filter");

        form.addEventListener("submit", function (e) {
            e.preventDefault(); 

            const type = typeFilter.value.toLowerCase(); 
            const subject = subjectFilter.value; 

            let query = "?controller=Teacher&action=getFeedbacks";
            if (type) query += `&type=${encodeURIComponent(type)}`;
            if (subject) query += `&subject=${encodeURIComponent(subject)}`;

            fetch(query)
                .then((response) => {
                    if (!response.ok) {
                      throw new Error('Failed to fetch feedbacks');
                    }
                    return response.json();
                })
                .then((data) => {
                  console.log("these are the feebacks sent : ", data);

                  if (data.feedbacks.length === 0) {
                    displayMessage('Nu au fost găsite recenzii pentru filtrele selectate.', 'info');
                    return;
                  }

                  const feedbacks = data.feedbacks;
                  const feedbackContainer = document.getElementById("review-container");
                  feedbackContainer.innerHTML = "";

                  feedbacks.forEach((feedbackData) => {
                    const groupedAnswers = {};

                    feedbackData.feedback_answers.forEach(answer => {
                          const categoryName = questionToCategoryMap[answer.question];
                          if (!groupedAnswers[categoryName]) {
                              groupedAnswers[categoryName] = [];
                          }
                          groupedAnswers[categoryName].push(answer);
                      });

                      const feedbackCategoriesHTML = Object.keys(groupedAnswers)
                            .map(categoryName => {
                                const answers = groupedAnswers[categoryName];

                                const colorRow = answers
                                    .map(answer => {
                                        let color;
                                        switch (answer.answer) {
                                          case "Întotdeauna":
                                                color = "#8BC34A"; 
                                                break;
                                            case "Destul de des":
                                                color = "#CDDC39"; 
                                                break;
                                            case "Destul de rar":
                                                color = "#FFC107"; 
                                                break;
                                            case "Niciodată":
                                                color = "#FF5722"; 
                                                break;
                                            case "Nu mă pot pronunța":
                                                color = "#9E9E9E"; 
                                                break;
                                        }

                                        return `<div class="color-box" 
                                                      style="background-color: ${color}"
                                                      title="${answer.answer}"
                                                      data-question="${answer.question}"></div>`;
                                    })
                                    .join("");

                                return `
                                    <div class="feedback-category">
                                        <div class="color-row">${colorRow}</div>
                                        <div class="hover-question" style="margin-top: 10px; font-size: 12px; color: #555; display: none;"></div>
                                    </div>
                                `;
                            })
                            .join("");

                    const feedbackCard = document.createElement("div");
                    feedbackCard.className = "review-card";

                    feedbackCard.innerHTML = `
                      <div class="card-body">

                        <h6 class="card-subtitle card-header">
                          <div>
                            <i class="fas fa-book"></i> ${feedbackData.course}
                            <span class="badge">${feedbackData.type === 'curs' ? 'Curs' : 'Seminar'}</span>
                          </div>

                          <div class="legend">
                              <i class="fas fa-info-circle legend-icon" title="Vezi legenda"></i>
                              <div class="legend-box">
                                <div class="legend-item">
                                    <span class="color-box" style="background-color: #8BC34A;"></span>
                                    Întotdeauna
                                </div>
                                <div class="legend-item">
                                    <span class="color-box" style="background-color: #CDDC39;"></span>
                                    Destul de des
                                </div>
                                <div class="legend-item">
                                    <span class="color-box" style="background-color: #FFC107;"></span>
                                    Destul de rar
                                </div>
                                <div class="legend-item">
                                    <span class="color-box" style="background-color: #FF5722;"></span>
                                    Niciodată
                                </div>
                                <div class="legend-item">
                                    <span class="color-box" style="background-color: #9E9E9E;"></span>
                                    Nu mă pot pronunța
                                </div>
                              </div>
                          </div>
                        </h6>

                        <p class="card-text">
                          <i class="fas fa-thumbs-up"></i> ${feedbackData.positive_feedback}
                        </p>
                        <p class="card-text">
                          <i class="fas fa-thumbs-down"></i> ${feedbackData.negative_feedback}
                        </p>
                        <div class="feedback-categories">
                            ${feedbackCategoriesHTML}  
                        </div>
                        <div class="clicked-question-container"></div>
                        <p class="card-year">
                          <i class="fas fa-calendar"></i> ${feedbackData.year}
                        </p>
                      </div>
                    `;

                    feedbackContainer.appendChild(feedbackCard);
                  });

                  document.querySelectorAll('.legend').forEach(legend => {
                        const legendBox = legend.querySelector('.legend-box');

                        legend.querySelector('.legend-icon').addEventListener('click', () => {
                            if (legendBox.style.display === 'block') {
                                legendBox.style.display = 'none';
                            } else {
                                legendBox.style.display = 'block';
                            }
                        });

                        document.addEventListener('click', (e) => {
                            if (!legend.contains(e.target)) {
                                legendBox.style.display = 'none';
                            }
                        });
                  });

                  document.querySelectorAll('.review-card').forEach(card => {
                        const colorBoxes = card.querySelectorAll('.color-box');
                        const questionContainer = card.querySelector('.clicked-question-container');

                        colorBoxes.forEach(colorBox => {
                            const questionText = colorBox.getAttribute('data-question');

                            colorBox.addEventListener('click', () => {
                                if (questionContainer.textContent === questionText) {
                                    questionContainer.textContent = ''; 
                                } else {
                                    questionContainer.textContent = questionText; 
                                }
                            });
                        });
                    });

                })
                .catch((error) => {
                    console.error("Error fetching filtered feedbacks:", error);
                });
        });

    });

  </script>
</body>
</html>
