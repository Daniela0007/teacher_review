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

    // function getFeedbacks() {
    //   fetch('?controller=Teacher&action=getFeedbacks', {
    //     method: 'GET',
    //   })
    //     .then(response => response.json())
    //     .then(data => {
    //       //console.log(data);
    //       const feedbacks = data.feedbacks;
    //       const feedbackContainer = document.getElementById("review-container");

    //       feedbacks.forEach((feedbackData) => {
    //         const feedbackCard = document.createElement("div");
    //         feedbackCard.className = "review-card";

    //         feedbackCard.innerHTML = `
    //           <div class="card-body">
    //             <h6 class="card-subtitle">
    //               <i class="fas fa-book"></i> ${feedbackData.course}
    //               <span class="badge">${feedbackData.type === 'curs' ? 'Curs' : 'Seminar'}</span>
    //             </h6>
    //             <p class="card-text">
    //               <i class="fas fa-thumbs-up"></i> ${feedbackData.positive_feedback}
    //             </p>
    //             <p class="card-text">
    //               <i class="fas fa-thumbs-down"></i> ${feedbackData.negative_feedback}
    //             </p>
    //             <ul class="feedback-answers">
    //               ${feedbackData.feedback_answers
    //                 .map(
    //                   (feedback) => `
    //                   <li>
    //                     <strong>${feedback.question}</strong> ${generateStars(feedback.answer)}
    //                   </li>
    //                 `
    //                 )
    //                 .join("")}
    //             </ul>
    //             <p class="card-year">
    //               <i class="fas fa-calendar"></i> ${feedbackData.year}
    //             </p>
    //           </div>
    //         `;

    //         feedbackContainer.appendChild(feedbackCard);
    //       });

    //     })
    //     .catch(error => console.error('Error loading feedbacks:', error));
    // }
    
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
                    displayMessage('No feedbacks found for the selected filters.', 'info');
                    return;
                  }

                  const feedbacks = data.feedbacks;
                  const feedbackContainer = document.getElementById("review-container");
                  feedbackContainer.innerHTML = "";

                  feedbacks.forEach((feedbackData) => {
                    const feedbackCard = document.createElement("div");
                    feedbackCard.className = "review-card";

                    feedbackCard.innerHTML = `
                      <div class="card-body">
                        <h6 class="card-subtitle">
                          <i class="fas fa-book"></i> ${feedbackData.course}
                          <span class="badge">${feedbackData.type === 'curs' ? 'Curs' : 'Seminar'}</span>
                        </h6>
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
                        <p class="card-year">
                          <i class="fas fa-calendar"></i> ${feedbackData.year}
                        </p>
                      </div>
                    `;

                    feedbackContainer.appendChild(feedbackCard);
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
